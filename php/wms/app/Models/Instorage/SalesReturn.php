<?php

namespace App\Models\Instorage;

use Illuminate\Database\Eloquent\Model;

class SalesReturn extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'created_at'
    ];

    protected $table = 'ret_in_dirt';

    public function product()
    {
        return $this->hasOne('App\Models\Base\Product', 'NewPRODUCTCD', 'NewProductCd');
    }

    public function customer()
    {
        return $this->hasOne('App\Models\Base\Customer', 'CUSTOMERCD', 'CustomerCd');
    }

    public function deliver()
    {
        return $this->hasOne('App\Models\Base\Deliver', 'DeliverAddCD', 'DeliverAddCD');
    }

    public function tag()
    {
        return $this->hasOne('App\Models\Instorage\SalesReturnTag', 'related_id');
    }

}
