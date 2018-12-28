<?php

namespace App\Http\Controllers;

use App\User;
use Cache;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

/**
 * @OA\Tag(
 *     name="用户",
 *     description="用户相关接口，包括后台登录，注册等",
 * )
 */
class UserController extends Controller
{

  /**
   * @OA\Post(
   *     path="/user/login",
   *     tags={"用户"},
   *     summary="用户登录",
   *      @OA\Parameter(
   *          name="email",
   *          description="账号",
   *          required=true,
   *          in="query",
   *         @OA\Schema(
   *             type="string",
   *         )
   *      ),
   *      @OA\Parameter(
   *          name="password",
   *          in="query",
   *          description="密码",
   *          required=true,
   *         @OA\Schema(
   *             type="string",
   *         )
   *      ),
   *      @OA\Response(
   *          response=200,
   *          description="调用成功"
   *      )
   * )
   */
  public function login(Request $request)
  {
    $credentials = $request->only('email', 'password');
    $token = $this->guard()->attempt($credentials);
    if ($token) {
      return $this->respondWithToken($token);
    }

    return $this->respond(['err' => '用户认证失败，请重试！']);
  }


  /**
   * @OA\Post(
   *     path="/user/info",
   *     tags={"用户"},
   *     summary="获取用户信息",
   *     @OA\Response(
   *         response=200,
   *         description="获取成功"
   *     ),
   *      security={
   *         {"oauth2": {}}
   *     }
   * )
   */
  public function info()
  {
    $id = $this->guard()->user()->id;
    $data = Cache::rememberForever('user-' . $id, function () use ($id) {
      return User::find($id);
    });

    return $this->respond($data);
  }

  /**
   * @OA\Post(
   *     path="/user/logout",
   *     tags={"用户"},
   *     summary="退出登录",
   *     @OA\Response(
   *         response=200,
   *         description="退出成功"
   *     ),
   *      security={
   *         {"oauth2": {}}
   *     }
   * )
   */
  public function logout()
  {
    $this->guard()->logout();

    return $this->respond(['message' => '已退出！']);
  }

  protected function respondWithToken($token)
  {
    return $this->respond([
      'token' => 'bearer ' . $token,
      'expires_in' => $this->guard()->factory()->getTTL() * 60,
    ]);
  }



  /**
   * @OA\Post(
   *     path="/user/search",
   *     tags={"用户"},
   *     summary="搜索用户",
   *     @OA\Response(
   *         response=200,
   *         description="退出成功"
   *     ),
   *      security={
   *         {"oauth2": {}}
   *     }
   * )
   */
  public function search(Request $request)
  {
    $name = $request->input('name');
    $users = User::where('name', 'like', '%' . $name . '%')->get();

    return $this->respond($users);
  }

  public function guard()
  {
    return \auth('api');
  }

  public function getUser(Request $request)
  {
    if ($request->has('id')) {
      $id = $request->input('id');
    } else {
      return $this->respond(['err' => '用户ID不能为空！']);
    }
    $user = Cache::rememberForever('user-' . $id, function () use ($id) {
      return User::find($id);
    });

    return $this->respond(['user' => $user]);
  }

  public function updateUser(Request $request)
  {
    if ($request->has('user.id')) {
      $id = $request->input('user.id');
    } else {
      return $this->respond(['err' => '用户ID不能为空！']);
    }

    try {
      $user = User::find($id);
      $logout = '';
      $user->name = $request->input("user.name") ? $request->input("user.name") : $user->name;
      if ($request->input("user.password")) {
        $password = bcrypt($request->input("user.password"));
        if ($user->password !== $password) {
          $logout = '您已修改密码，请重新登录！';
          if ($user->default_pass == '1') {
            $user->default_pass = '0';
          }
        }
      }
      $user->password = $request->input("user.password") ? bcrypt($request->input("user.password")) : $user->password;
      $user->avatar = $request->input("user.avatar") ? $request->input("user.avatar") : $user->avatar;
      $user->roles = $request->input("user.roles") ? $request->input("user.roles") : $user->roles;
      //var_dump($user);
      $user->save();
      return $this->respond(['logout' => $logout]);
    } catch (Exception $e) {
      return $this->respond(['err' => '用户信息修改失败，请重试！' . $e->getMessage()]);
    }
  }

  public function createUser(Request $request)
  {
    $user = new User;
    $user->name = $request->input("user.name") ? $request->input("user.name") : 'default';
    $user->email = $request->input("user.email");
    $user->password = bcrypt($request->input("user.password"));
    $user->roles = $request->input("user.roles") ? $request->input("user.roles") : 'editor';
    $user->avatar = $request->input("user.avatar") ? $request->input("user.avatar") : '/uploads/avatar/default.gif';

    try {
      $user->save();
      return $this->noContent();
    } catch (Exception $e) {
      return $this->respond(['err' => '添加用户失败，请重试！' . $e->getMessage()]);
    }
  }

  public function checkEmail(Request $request)
  {
    if (!$request->has('email')) {
      return response()->json(['err' => 'email不能为空']);
    }
    $validator = Validator::make($request->all(), [
      'email' => 'unique:users',
    ]);
    if ($validator->fails()) {
      return $this->respond(['err' => '邮箱已存在']);
    } else {
      return $this->noContent();
    }
  }

  public function getList(Request $request)
  {
    $limit = $request->has('limit') ? $request->limit : 10;
    $page = $request->has('page') ? ($request->page - 1) * $limit : 0;
    $results = Cache::rememberForever('user-all', function () {
      return User::with(["articles" => function ($query) {
        $query->select('user_id');
      }])->get();
    });

    $total = $results->count();
    $items = array_slice($results->toArray(), $page, $limit);

    return $this->respond(compact("items", "total"));
  }

  public function userAll()
  {
    $results = Cache::rememberForever('user-all', function () {
      return User::with(["articles"])->get();
    });
    $items = [];
    foreach ($results as $result) {
      $items[$result->id] = $result->name;
    }
    return $this->respond(compact('items'));
  }

  public function deleteUser(Request $request)
  {
    if ($request->has('id')) {
      $id = $request->input('id');
    } else {
      $err = '用户ID不能为空，请重试！';
      return $this->respond(compact('err'));
    }

    try {
      User::destroy($id);
      return $this->noContent();
    } catch (Exception $e) {
      return $this->respond(['err' => '删除用户失败，请重试！' . $e->getMessage()]);
    }
  }

  public function index()
  {
    return view('admin.index');
  }
}
