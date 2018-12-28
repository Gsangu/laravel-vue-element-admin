<?php

namespace App\Http\Controllers;

use Cache;
use App\Message;
use App\Field;
use Jenssegers\Agent\Agent;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function getList(Request $request)
    {
        $limit = $request->has('limit') ? $request->limit : 10;
        $page = $request->has('page') ? ($request->page - 1) * $limit : 0;

        $results = Cache::rememberForever('message-all', function () {
            return Message::all();
        });

        $fields = Cache::rememberForever('field-key-name-all', function(){
            return Field::list();
        });
        $total = $results->count();
        $items = array_slice($results->toArray(), $page, $limit);

        return $this->respond(compact("items", "total", "fields"));
    }

    
    public function deleteMessage(Request $request)
    {
        if ($request->has('id')) {
            $id = $request->input('id');
        } else {
            $err = '留言ID不能为空，请重试！';
            return $this->respond(compact('err'));
        }

        try {
            Message::destroy($id);
            return $this->noContent();
        } catch (Exception $e) {
            return $this->respond(['err' => '删除留言失败，请重试！'.$e->getMessage()]);
        }
    }

    public function batchUpdateMessage(Request $request)
    {
        if ($request->has('messages')) {
            $messages = $request->input('messages');
        } else {
            $err = '留言ID不能为空，请重试！';
            return $this->respond(compact('err'));
        }

        $status = $request->input('status') ? 1 : 0;

        try {
            Message::whereIn('id', $messages)->update(array_filter(compact('status')));
            Cache::forget('message-all');
            return $this->noContent();
        } catch (Exception $e) {
            return $this->respond(['err' => '批量修改留言失败，请重试！'.$e->getMessage()]);
        }
    }

    public function createMessage(Message $message, Agent $agent, Request $request)
    {

        try {
            $message->content   = $request->except('_token');
            $message->ip        = ip2long($request->getClientIp());
            $message->sourceUrl = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
            $message->system    = $agent->platform();
            $message->status    = 0;        
            
            $message->save();
            echo '<script> alert("Thanks for your submit. We will response as soon as possible!");history.back(); </script>';
            //return redirect()->back();
        } catch (Exception $e) {
            echo '<script> alert("Sorry, please try again.") </script>';
            return redirect()->back();
        }
    }
}
