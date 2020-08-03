<?php

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{

    protected $table = 'company';

    protected $fileable = [
        'CompanyCd','CompanyNm','CompanyShortNm','FaxNum','OfficeAdd','POSTCD','TelephoneNum','DataYmd','created_at'
    ];

    public function products()
    {
        return $this->hasMany('App\Models\Base\Product', 'CompanyCd', 'CompanyCd');
    }
}
