<?php

namespace App\Models\Storage;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Appear extends Model
{
    protected $table = 'ord_out_ensure';

    protected $guarded = [
        'created_at'
    ];
    public function product() {
        return $this->belongsTo('App\Models\Base\Product', 'PRODUCTCD', 'PRODUCTCD');
    }

    public function ordOutDirt()
    {
        return $this->belongsToMany('App\Models\Outstorage\SalesOut', 'OutStcNo', 'VBELN');
    }

    public function adjOutDirt()
    {
        return $this->belongsToMany('App\Models\Outstorage\Adjust', 'OutStcNo', 'VBELN');
    }

    public function moveOutDirt()
    {
        return $this->belongsToMany('App\Models\Outstorage\MoveOut', 'OutStcNo', 'VBELN');
    }
}
