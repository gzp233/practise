<?php

namespace App\Models\Outstorage;

use Illuminate\Database\Eloquent\Model;

class OrderOutEnsure extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'created_at'
    ];

    protected $table = 'ord_out_ensure';

    const UPDATED_AT = null;

    public function product()
    {
        return $this->hasOne('App\Models\Base\Product', 'PRODUCTCD', 'PRODUCTCD')->with('brand', 'series', 'company', 'prodflg');
    }
}
