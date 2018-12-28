<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UploadController extends Controller
{
    //上传图片
    public function uploadfile(Request $request, $filename="file", $filepath = 'uploads/images', $file_extension = ['jpeg','jpg','gif','gpeg','png'])
    {
        if($request->has('avatar')){
            $filepath = 'uploads/avatar';
        }
        //1.首先检查文件是否存在
        if ($request->hasFile($filename)){
            //          2.获取文件
            $file = $request->file($filename);
            //          3.其次检查图片手否合法
            if ($file->isValid()){
    //                先得到文件后缀,然后将后缀转换成小写,然后看是否在否和图片的数组内
                if(in_array( strtolower($file->extension()),$file_extension)){
                    //          4.将文件取一个新的名字
                    $newName = 'img'.time().rand(100000, 999999).'.'.$file->extension();
                    //           5.移动文件,并修改名字
                    if($file->move($filepath,$newName)){
                        return response()->json(['imgUrl' => $filepath.'/'.$newName, 'code' => '20000', 'msg' => '上传成功！']);   //返回一个地址
                    }else{
                        return response()->json(['code' => '20004', 'msg' => '储存失败，请重试！']);
                    }
                }else{
                    return response()->json(['code' => '20003', 'msg' => '后缀不合法，请重试！']);
                }
                            
            }else{
                return response()->json(['code' => '20002', 'msg' => '文件不合法，请重试！']);
            }
        }else{
            return response()->json(['code' => '20001', 'msg' => '上传失败，请重试！']);
        }
    }

}
