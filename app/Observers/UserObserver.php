<?php

namespace App\Observers;

use App\User;
use Illuminate\Support\Facades\Cache;

class UserObserver
{
    public function saved(User $user)
    {
        Cache::forget('user-all');
        Cache::forget('user-'. $user->id);
    }

    public function deleting(User $user)
    {
        Cache::forget('user-all');
        Cache::forget('user-'. $user->id);
    }
}