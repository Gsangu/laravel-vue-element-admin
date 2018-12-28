<?php

namespace App\Observers;

use App\Article;
use Illuminate\Support\Facades\Cache;

class ArticleObserver
{
    public function saved(Article $article)
    {
        Cache::forget('article-all');
        if($article->status){
            Cache::forget('article-all-published');
            Cache::forget('category-'.$article->category->slug);
        }
        Cache::forget('article-'. $article->id);
        Cache::forget('article-'. $article->slug);
    }

    public function deleting(Article $article)
    {
        Cache::forget('article-all');
        if($article->status){
            Cache::forget('article-all-published');
            Cache::forget('category-'.$article->category->slug);
        }
        Cache::forget('article-'. $article->id);
        Cache::forget('article-'. $article->slug);
    }
}