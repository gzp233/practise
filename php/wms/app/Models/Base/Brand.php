<?php

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{

    protected $table = 'brand';

    protected $fileable = [
        'CompanyCd','DELFLG','BRANDCD','BRANDNM','DataYmd','created_at'
    ];

    public function products()
    {
        return $this->hasMany('App\Models\Base\Product', 'BRANDCD', 'BRANDCD');
    }
}
