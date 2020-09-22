<?php

namespace App\Models\Storage;

use Illuminate\Database\Eloquent\Model;

class CheckTag extends Model
{

    protected $table = 'check_tag';

    

    public function product() {
        return $this->belongsTo('App\Models\Base\Product', 'product_id', 'id');
    }

    public function goods() {
        return $this->belongsTo('App\Models\Storage\Goods', 'goods_id', 'id');
    }

    public function user() {
        return $this->belongsTo('App\Models\Auth\User', 'user_id', 'id');
    }

}
