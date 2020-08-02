<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostCategory extends Model
{
    protected $guarded = ['created_at', 'updated_at'];

    protected $table = 'post_categories';

    public function posts()
    {
        return $this->hasMany(Post::class, 'category_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
