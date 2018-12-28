<?php

namespace App\Observers;

use App\Field;
use Illuminate\Support\Facades\Cache;

class FieldObserver
{
    public function saved(Field $field)
    {
        Cache::forget('field-all');
        Cache::forget('field-key-name-all');
        Cache::forget('field-'. $field->id);
    }

    public function deleting(Field $field)
    {
        Cache::forget('field-all');
        Cache::forget('field-key-name-all');
        Cache::forget('field-'. $field->id);
    }
}