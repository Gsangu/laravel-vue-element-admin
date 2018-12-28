<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $guarded = ['content'];
    protected $casts = [
        'content' => 'array'
    ];

    public function getIpAttribute($value){
        return long2ip($value);
    }

    public function getStatusAttribute($value){
        return $value ? true : false;
    }

}
