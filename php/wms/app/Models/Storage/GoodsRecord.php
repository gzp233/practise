<?php

namespace App\Models\Storage;

use Illuminate\Database\Eloquent\Model;

class GoodsRecord extends Model
{

    protected $table = 'goods_record';

    protected $guarded = [
        'created_at', 'updated_at'
    ];

    public function attribute()
    {
        return $this->belongsTo('App\Models\Basic\Attributes', 'state_name', 'state_name');
    }

    public function product()
    {
        return $this->belongsTo('App\Models\Base\Product', 'product_id', 'id');
    }

    public function location()
    {
        return $this->hasOne('App\Models\Basic\Location', 'stock_no', 'stock_no')->with('area');
    }

    public function originLocation()
    {
        return $this->hasOne('App\Models\Basic\Location', 'stock_no', 'origin_stock_no');
    }

    public function Unit()
    {
        return $this->hasMany('App\Models\Base\Unit', 'product_id', 'product_id');
    }
   
}
