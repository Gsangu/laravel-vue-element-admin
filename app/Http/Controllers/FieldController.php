<?php

namespace App\Http\Controllers;

use Cache;
use Validator;
use App\Field;
use Illuminate\Http\Request;

class FieldController extends Controller
{
    public function getList(Request $request)
    {
        $limit = $request->has('limit') ? $request->limit : 10;
        $page = $request->has('page') ? ($request->page - 1) * $limit : 0;


        $results = Cache::rememberForever('field-all', function(){
            return Field::all();
        });
        $total = $results->count();
        $items = array_slice($results->toArray(), $page, $limit);

        return $this->respond(compact("items", "total"));
    }

    public function createField(Request $request)
    {
        $field = new Field;
        $field->name = $request->input("field.name");
        $field->index = $request->input("field.index");

        try {
            $field->save();
            return $this->noContent();
        } catch (Exception $e) {
            return $this->respond(['err' => '创建字段失败，请重试！'.$e->getMessage()]);
        }
    }
    public function deleteField(Request $request)
    {
        if ($request->has('id')) {
            $id = $request->input('id');
        } else {
            $err = '字段ID不能为空，请重试！';
            return $this->respond(compact('err'));
        }

        try {
            Field::destroy($id);
            return $this->noContent();
        } catch (Exception $e) {
            return $this->respond(['err' => '删除字段失败，请重试！'.$e->getMessage()]);
        }
    }
    public function checkIndex(Request $request)
    {
        if (!$request->has('index')) {
            return response()->json(['err'=>'index不能为空']);
        }
        $validator = Validator::make($request->all(), [
            'index' => 'unique:fields',
        ]);
        if ($validator->fails()) {
            return $this->respond(['err' => '索引已存在']);
        } else {
            return $this->noContent();
        }
    }
    public function updateField(Request $request)
    {
        if ($request->has('field.id')) {
            $id = $request->input('field.id');
        } else {
            return $this->respond(['err'=>'字段ID不能为空！']);
        }
        
        try {
            $field = Field::find($id);
            $field->name = $request->input("field.name") ? $request->input("field.name") : $field->name;
            $field->index = $request->input("field.index") ? $request->input("field.index") : $field->index;
            $field->save();
            return $this->noContent();
        } catch (Exception $e) {
            return $this->respond(['err' => '字段修改失败，请重试！'.$e->getMessage()]);
        }
    }
}
