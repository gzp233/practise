<?php

namespace App\Models\Storage;

use Illuminate\Database\Eloquent\Model;

class Yiku extends Model
{

    protected $table = 'yiku';

    protected $guarded = [
        'created_at', 'updated_at'
    ];

    public function dealUser() {
        return $this->belongsTo('App\Models\Auth\User', 'deal_user', 'id')->withTrashed();
    }

    public function createUser() {
        return $this->belongsTo('App\Models\Auth\User', 'create_user', 'id')->withTrashed();
    }

    public function product() {
        return $this->belongsTo('App\Models\Base\Product', 'product_id', 'id');
    }
}
