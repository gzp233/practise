<?php

namespace App\Models\Outstorage;

use Illuminate\Database\Eloquent\Model;

class AntiCode extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'created_at', 'updated_at'
    ];

    protected $table = 'anti_code';

    public function product()
    {
        return $this->hasOne('App\Models\Base\Product', 'NewPRODUCTCD', 'PRODUCTCODE')->with('units');
    }

    public function dealUser() {
        return $this->belongsTo('App\Models\Auth\User', 'deal_user', 'id');
    }
}
