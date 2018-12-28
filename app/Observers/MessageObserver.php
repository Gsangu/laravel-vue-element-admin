<?php

namespace App\Observers;

use App\Message;
use Illuminate\Support\Facades\Cache;

class MessageObserver
{
    public function saved(Message $message)
    {
        Cache::forget('message-all');
        Cache::forget('message-'. $message->id);
    }

    public function deleting(Message $message)
    {
        Cache::forget('message-all');
        Cache::forget('message-'. $message->id);
    }
}