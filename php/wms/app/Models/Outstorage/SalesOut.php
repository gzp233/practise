<?php

namespace App\Models\Outstorage;

use Illuminate\Database\Eloquent\Model;

class SalesOut extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'created_at'
    ];

    protected $table = 'ord_out_dirt';

    public function product()
    {
        return $this->hasOne('App\Models\Base\Product', 'NewPRODUCTCD', 'NewProductCd')->with('units');
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
        return $this->hasOne('App\Models\Outstorage\SalesOutTag', 'related_id');
    }

    public function company()
    {
        return $this->hasOne('App\Models\Base\Company', 'CompanyCd', 'CompanyCD');
    }

}
