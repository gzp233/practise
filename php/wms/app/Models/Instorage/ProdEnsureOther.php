<?php

namespace App\Models\Instorage;

use Illuminate\Database\Eloquent\Model;

class ProdEnsureOther extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'created_at'
    ];

    const UPDATED_AT = null;

    protected $table = 'prod_imp_ensure_other';

    public function product()
    {
        return $this->hasOne('App\Models\Base\Product', 'PRODUCTCD', 'PRODUCTCD');
    }
}
