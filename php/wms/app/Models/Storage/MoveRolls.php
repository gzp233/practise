<?php

namespace App\Models\Storage;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class MoveRolls extends Model
{
    use SoftDeletes;

    protected $table = 'stc_state';

    protected $guarded = [
        'created_at'
    ];

    protected $dates = ['deleted_at'];

    public function product() {
        return $this->belongsTo('App\Models\Base\Product', 'product_id', 'id');
    }
}
