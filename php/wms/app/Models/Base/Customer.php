<?php

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{

    protected $table = 'customer';

    protected $fileable = [
        'CompanyCd','DELFLG','CUSTOMERCD','ShopSignNM','DeliverAddCD','ShopAdr','ShopPostCD','ShopTel','ShopFax','DataYmd','created_at'
    ];
}
