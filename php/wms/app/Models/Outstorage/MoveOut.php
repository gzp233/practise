<?php

namespace App\Models\Outstorage;

use Illuminate\Database\Eloquent\Model;

class MoveOut extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'created_at'
    ];

    protected $table = 'move_out_dirt';

    public function product()
    {
        return $this->hasOne('App\Models\Base\Product', 'NewPRODUCTCD', 'NewProductCD')->with('units');
    }

    public function company()
    {
        return $this->hasOne('App\Models\Base\Customer', 'CompanyCd', 'CompanyCD');
    }

    public function deliver()
    {
        return $this->hasOne('App\Models\Base\Deliver', 'DeliverAddCD', 'DeliverAddCD');
    }

    public function tag()
    {
        return $this->hasOne('App\Models\Outstorage\MoveOutTag', 'related_id');
    }

}
