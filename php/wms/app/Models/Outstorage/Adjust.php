<?php

namespace App\Models\Outstorage;

use Illuminate\Database\Eloquent\Model;

class Adjust extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'created_at'
    ];

    protected $table = 'adj_out_dirt';

    public function product()
    {
        return $this->hasOne('App\Models\Base\Product', 'NewPRODUCTCD', 'NewProductCd')->with('units');
    }

    public function customer()
    {
        return $this->hasOne('App\Models\Base\Customer', 'CUSTOMERCD', 'CustomerCD');
    }

    public function deliver()
    {
        return $this->hasOne('App\Models\Base\Deliver', 'DeliverAddCD', 'DeliverAddCD');
    }

    public function tag()
    {
        return $this->hasOne('App\Models\Outstorage\AdjustTag', 'related_id');
    }

    public function company()
    {
        return $this->hasOne('App\Models\Base\Company', 'CompanyCd', 'CompanyCD');
    }
}
