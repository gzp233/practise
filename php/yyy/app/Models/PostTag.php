<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostTag extends Model
{
    protected $guarded = ['created_at', 'updated_at'];

    protected $table = 'post_tags';

    public function posts()
    {
        return $this->belongsToMany(Post::class, 'post_tag_relation', 'tag_id', 'post_id');
    }
}
