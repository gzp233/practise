<?php

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{

    protected $table = 'vendor';

    protected $fileable = [
        'VendorCode','AccountGroup','Country','Name1','Name2','City','PostalCode','Region','Street','Telephone','Fax','CompanyCode','PurchasingOrganization','DataYmd'
    ];
}
