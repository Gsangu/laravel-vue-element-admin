<?php

namespace App\Http\Middleware;

use App\AdminLog;
use Closure;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class CheckRole extends BaseMiddleware
{
  /**
   * @param  \Illuminate\Http\Request $request
   * @param  \Closure $next
   * @param  $role
   * @return mixed
   * @throws UnauthorizedHttpException
   */
  public function handle($request, Closure $next, $role)
  {
    $user = $request->user();
    if (!isset($user['roles'])) {
      throw new UnauthorizedHttpException('jwt-auth', '登录失效');
    }
    if ($role) {
      $roles = explode('|', $role);
      foreach ($roles as $role) {
        if (!in_array($role, $user['roles']['data'])) {
          $this->saveSystemLogs($request);
          return $next($request);
        }
      }
    }

    return response()->json(['err' => '权限不足']);
  }

  /**
   * 保存系统操作日志
   * @param  \Illuminate\Http\Request $request
   */
  public function saveSystemLogs($request)
  {
    $params = $request->all();
    if (isset($params['password'])) unset($params['password']);
    $loginInfo = $request->user();
    AdminLog::create([
      'admin_id' => $loginInfo['id'],
      'operator' => $loginInfo['name'],
      'method' => $request->getMethod(),
      'url' => $request->getPathInfo(),
      'ip' => $request->getClientIp(),
      'details' => json_encode($params),
      'type' => 1
    ]);
  }
}
