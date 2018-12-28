<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $guarded = [];
    protected $casts = [
        'category_id' => 'string',
        'user_id'     => 'string'
    ];

    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function scopePublished($q){
        return $q->where('status', 1);
    }
}
