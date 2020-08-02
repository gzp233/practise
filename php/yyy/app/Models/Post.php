<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $guarded = ['created_at', 'updated_at'];

    protected $table = 'posts';

    public function comments()
    {
        return $this->hasMany(PostComment::class, 'post_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo(PostCategory::class, 'category_id', 'id');
    }

    public function tags()
    {
        return $this->belongsToMany(PostTag::class, 'post_tag_relation', 'post_id', 'tag_id');
    }
}
