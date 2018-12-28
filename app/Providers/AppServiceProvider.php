<?php

namespace App\Providers;

use App\Article;
use App\Category;
use App\User;
use App\Message;
use App\Field;
use App\Observers\FieldObserver;
use App\Observers\MessageObserver;
use App\Observers\ArticleObserver;
use App\Observers\CategoryObserver;
use App\Observers\UserObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Resources\Json\Resource;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Article::observe(ArticleObserver::class);
        Category::observe(CategoryObserver::class);
        User::observe(UserObserver::class);
        Message::observe(MessageObserver::class);
        Field::observe(FieldObserver::class);
        Resource::withoutWrapping();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
