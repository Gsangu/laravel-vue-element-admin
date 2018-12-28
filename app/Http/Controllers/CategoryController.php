<?php

namespace App\Http\Controllers;

use App\Category;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CategoryController extends Controller
{
    public function checkSlugDuplication(Request $request)
    {
        if ($request->has('slug')) {
            $slug = $request->slug;
        } else {
            return $this->noContent();
        }
        $validator = Validator::make($request->all(), [
                'slug' => 'unique:categories',
            ]);
        if ($validator->fails()) {
            return $this->respond(['err' => '短链接已存在']);
        } else {
            return $this->noContent();
        }
    }
    public function categoryAll()
    {
        $results = Cache::rememberForever('category-all', function () {
            return Category::all();
        });
        $items = [];
        foreach ($results as $result) {
            $items[$result->id] = $result->name;
        }
        return $this->respond(compact('items'));
    }
    public function categoryList(Request $request)
    {
        $limit = $request->has('limit') ? $request->limit : 10;
        $page = $request->has('page') ? ($request->page - 1) * $limit : 0;

        $results = Cache::rememberForever('category-all', function () {
            return Category::with(['articles' => function($query){
                $query->select('category_id');
            }])->get();
        });
        $total = $results->count();
        $items = array_slice($results->toArray(), $page, $limit);
        //var_dump($items);

        return $this->respond(compact("items", "total"));
    }
    public function createCategory(Request $request)
    {
        $category = new Category;
        $category->name = $request->input("category.name");
        $category->slug = $request->input("category.slug");

        try {
            $category->save();
            return $this->noContent();
        } catch (Exception $e) {
            return $this->respond(['err' => '创建分类失败，请重试！'.$e->getMessage()]);
        }
    }

    public function updateCategory(Request $request)
    {
        if ($request->has('category.id')) {
            $id = $request->input('category.id');
        } else {
            $err = '分类ID不能为空，请重试！';
            return $this->noContent();
        }
        $category = Category::find($id);
        $category->name = $request->input("category.name");
        $category->slug = $request->input("category.slug");

        try {
            $category->save();
            return $this->noContent();
        } catch (Exception $e) {
            return $this->respond(['err' => '更新分类失败，请重试！'.$e->getMessage()]);
        }
    }

    public function getCategory(Request $request)
    {
        if ($request->has('id')) {
            $id = $request->id;
        } else {
            return $this->respond(['err' => 'ID不能为空!']);
        }

        $item = Cache::rememberForever('category-'.$id, function () use ($id) {
            return Category::where('id', $id)->first();
        });

        return $this->respond(compact("item"));
    }

    public function deleteCategory(Request $request)
    {
        if ($request->has('id')) {
            $id = $request->input('id');
        } else {
            $err = '分类ID不能为空，请重试！';
            return $this->respond(compact('err'));
        }

        try {
            Category::destroy($id);
            return $this->noContent();
        } catch (Exception $e) {
            return $this->respond(['err' => '删除分类失败，请重试！'.$e->getMessage()]);
        }
    }
}
