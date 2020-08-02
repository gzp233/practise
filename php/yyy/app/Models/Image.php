<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Image extends Model
{
    protected $guarded = [];

    protected $table = 'images';

    protected $appends = ['src'];

    public function getSrcAttribute()
    {
        return Storage::disk('qiniu')->url($this->attributes['image']);
    }
}
