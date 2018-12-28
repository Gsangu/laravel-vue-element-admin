<?php
/**
 * Created by PhpStorm.
 * User: cocoyo
 * Date: 2018/5/13 0013
 * Time: 19:30
 */
namespace App\Traits;


use Illuminate\Support\Facades\Redis;

trait VisitsHelper
{
    protected $hash_prefix = 'cocoyo_visit';

    /**
     * 记录文章访问数
     *
     */
    public function recordVisit()
    {
        Redis::hIncrBy($this->hash_prefix, $this->slug, 1);
    }

    /**
     * 同步访问量至数据库
     */
    public function syncDatabaseVisit()
    {
        // 从 Redis 中获取所有哈希表里的数据
        $visits = Redis::hGetAll($this->hash_prefix);

        foreach ($visits as $slug => $visit) {
            if ($article = $this->where('slug', $slug)->first()) {
                $article->view_count = $visit;
                $article->save();
            }
        }
    }

    /**
     * 获取访问数
     *
     * @param $value
     * @return mixed
     */
    public function getViewCountAttribute($value)
    {
        return Redis::hGet($this->hash_prefix, $this->slug) ?: $value;
    }
}