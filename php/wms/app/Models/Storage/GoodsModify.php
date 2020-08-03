<?php

namespace App\Models\Storage;

use Illuminate\Database\Eloquent\Model;

class GoodsModify extends Model
{

    protected $table = 'goods_modify';

    protected $guarded = [
        'created_at', 'updated_at'
    ];

    public function product()
    {
        return $this->belongsTo('App\Models\Base\Product', 'product_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\Auth\User', 'user_id', 'id');
    }
}
