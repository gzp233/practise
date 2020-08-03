<?php

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
//    use SoftDeletes;

    protected $table = 'unit';

//    protected $dates = ['deleted_at'];

    protected $fillable = [
        'unit_name', 'number', 'product_id'
    ];

    public function product() {
        return $this->belongsTo('App\Models\Base\Product', 'product_id', 'id');
    }
}
