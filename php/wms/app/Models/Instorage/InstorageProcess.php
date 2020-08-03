<?php

namespace App\Models\Instorage;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InstorageProcess extends Model
{
    use SoftDeletes;

    protected $table = 'instorage_process';

    protected $guarded = [
        'created_at', 'updated_at'
    ];

    protected $dates = ['deleted_at'];

    public function product() {
        return $this->belongsTo('App\Models\Base\Product', 'product_id', 'id');
    }
}
