<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class ArticleResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'                => $this->id,
            'category_id'       => $this->category_id,
            'user_id'           => $this->user_id,
            'slug'              => $this->slug,
            'title'             => $this->title,
            'content'           => $this->content,
            'image_uri'         => $this->image_uri,
            'content_short'     => $this->content_short,
            'status'            => $this->status,
            'published_at'      => $this->published_at
        ];
    }
}
