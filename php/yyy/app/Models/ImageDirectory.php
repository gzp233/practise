<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImageDirectory extends Model
{
    protected $guarded = ['created_at', 'updated_at'];

    protected $table = 'image_directories';

    public function images()
    {
        return $this->hasMany(Image::class, 'directory_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
