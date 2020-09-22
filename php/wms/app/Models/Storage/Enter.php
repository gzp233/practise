<?php

namespace App\Models\Storage;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Enter extends Model
{
    protected $table = 'prod_imp_ensure_other';

    protected $guarded = [
        'created_at'
    ];
    public function product() {
        return $this->belongsTo('App\Models\Base\Product', 'PRODUCTCD', 'PRODUCTCD');
    }
}
