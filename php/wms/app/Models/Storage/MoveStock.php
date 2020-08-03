<?php

namespace App\Models\Storage;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class MoveStock extends Model
{
    use SoftDeletes;

    protected $table = 'move_stock';

    protected $guarded = [
        'created_at', 'updated_at'
    ];

    protected $dates = ['deleted_at'];

    public function product() {
        return $this->belongsTo('App\Models\Base\Product', 'product_id', 'id');
    }
}
