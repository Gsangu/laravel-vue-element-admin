<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Field extends Model
{
    /**
     * 返回index->name的数组
     */

    public function scopeList($query){
        $fields = $query->get();
        $keys   = $fields->pluck('index')->flatten();
        $names  = $fields->pluck('name')->flatten();
        return array_combine($keys->toArray(), $names->toArray());
    }
}
