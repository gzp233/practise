<?php

namespace App\Models\Storage;

use Illuminate\Database\Eloquent\Model;

class GoodsTag extends Model
{

    protected $table = 'goods_tag';

    protected $guarded = [
        'created_at', 'updated_at'
    ];

}
