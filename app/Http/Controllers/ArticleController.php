<?php

namespace App\Http\Controllers;

use DB;
use Event;
use Validator;
use App\Article;
use App\Category;
use App\Http\Resources\ArticleResource;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\User;

/**
 * @OA\Tag(
 *     name="文章",
 *     description="文章相关接口，包含列表添加更新删除等",
 * )
 */
class ArticleController extends Controller
{
    public function checkSlugDuplication(Request $request)
    {
        if (!$request->has('slug')) {
            return $this->respond(['err'=>'slug不能为空']);
        }
        $validator = Validator::make($request->all(), [
            'slug' => 'unique:articles',
        ]);
        if ($validator->fails()) {
            return $this->respond(['err' => '短链接已存在']);
        } else {
            return $this->noContent();
        }
    }
    public function articleList(Request $request)
    {
      $this->checkRolePermission('admin');
        $limit = $request->has('limit') ? $request->limit : 10;
        $page = $request->has('page') ? ($request->page - 1) * $limit : 0;

        $results = Cache::rememberForever('article-all', function () {
            return Article::with(['user' => function($query){
                $query->select("id", "name");
            },
            'category' => function($query){
                $query->select("id", "name");
            }])->orderBy('id', 'desc')->get();
        });
        $total = $results->count();
        $items = array_slice($results->toArray(), $page, $limit);

        return $this->respond(compact("items", "total"));
    }

    public function getArticle(Request $request)
    {
        if ($request->has('id')) {
            $id = $request->id;
        } else {
            return $this->respond(['err' => 'ID不能为空！']);
        }

        $item = Cache::rememberForever('article-'.$id, function () use ($id) {
            return Article::where('id', $id)->first();
        });

        return new ArticleResource($item);
    }

    public function createArticle(Request $request)
    {

        try {
            Article::create(array_filter($request->input('article'), 'strlen'));
            return $this->noContent();
        } catch (Exception $e) {
            return $this->respond(['err' => '文章发布失败，请重试！'.$e->getMessage()]);
        }
    }

    public function updateArticle(Request $request)
    {
        if ($request->has('article.id')) {
            $id = $request->input('article.id');
        } else {
            $err = '文章ID不能为空，请重试！';
            return $this->respond(compact('err'));
        }

        try {
            $article = Article::where('id', $id)->first();
            $article->fill(array_filter($request->input('article'), 'strlen'))->save();
            return $this->noContent();
        } catch (Exception $e) {
            return $this->respond(['err' => '文章更新失败，请重试！'.$e->getMessage()]);
        }
    }

    public function deleteArticle(Request $request)
    {
        if ($request->has('id')) {
            $id = $request->input('id');
        } else {
            $err = '文章ID不能为空，请重试！';
            return $this->respond(compact('err'));
        }

        try {
            Article::destroy($id);
            return $this->noContent();
        } catch (Exception $e) {
            return $this->respond(['err' => '删除文章失败，请重试！'.$e->getMessage()]);
        }
    }

    public function batchUpdateArticle(Request $request)
    {
        if ($request->has('articles')) {
            $articles = $request->input('articles');
        } else {
            $err = '文章ID不能为空，请重试！';
            return $this->respond(compact('err'));
        }

        $category_id = $request->input('category');
        $status = $request->input('status');
        $user_id = $request->input('user');

        try {
            Article::whereIn('id', $articles)->update(array_filter(compact('category_id', 'status', 'user_id'), "strlen"));
            Cache::forget('article-all');
            foreach ($articles as $article) {
                Cache::forget('article-'. $article);
            }

            if ($category_id) {
                Event::fire("eloquent.saved: App\Category", Category::find($category_id));
            }


            if ($user_id) {
                Event::fire("eloquent.saved: App\User", User::find($user_id));
            }
            return $this->noContent();
        } catch (Exception $e) {
            return $this->respond(['err' => '批量修改文章失败，请重试！'.$e->getMessage()]);
        }
    }
}
