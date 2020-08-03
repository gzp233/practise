<?php

namespace App\Models\Storage;

use Illuminate\Database\Eloquent\Model;

class Goods extends Model
{

    protected $table = 'goods';

    protected $guarded = [
        'created_at', 'updated_at'
    ];

    public function attribute() {
        return $this->belongsTo('App\Models\Basic\Attributes', 'state_name', 'state_name');
    }

    public function product() {
        return $this->belongsTo('App\Models\Base\Product', 'product_id', 'id');
    }

    public function location() {
        return $this->hasOne('App\Models\Basic\Location', 'stock_no', 'stock_no')->with('area');
    }
    public function tag() {
        return $this->belongsTo('App\Models\Storage\GoodsTag', 'id', 'goods_id');
    }

}
