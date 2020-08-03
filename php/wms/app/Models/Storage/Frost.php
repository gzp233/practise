<?php

namespace App\Models\Storage;

use Illuminate\Database\Eloquent\Model;

class Frost extends Model
{

    protected $table = 'frost';

    public function product() {
        return $this->belongsTo('App\Models\Base\Product', 'product_id', 'id');
    }

    public function goods() {
        return $this->belongsTo('App\Models\Storage\Goods', 'goods_id', 'id');
    }

}
