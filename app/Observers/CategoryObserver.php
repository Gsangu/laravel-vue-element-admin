<?php

namespace App\Observers;

use App\Category;
use Illuminate\Support\Facades\Cache;

class CategoryObserver
{
    public function saved(Category $category)
    {
        Cache::forget('category-all');
        Cache::forget('category-'. $category->id);
        Cache::forget('category-'. $category->slug);

    }

    public function deleting(Category $category)
    {
        Cache::forget('category-all');
        Cache::forget('category-'. $category->id);
        Cache::forget('category-'. $category->slug);
    }
}