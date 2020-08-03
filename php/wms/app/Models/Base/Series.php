<?php

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Model;

class Series extends Model
{

    protected $table = 'series';

    protected $fileable = [
        'CompanyCd','DELFLG','SERIESCD','SERIESNM','DataYmd'
    ];

    public function products()
    {
        return $this->hasMany('App\Models\Base\Product', 'SERIESCD', 'SERIESCD');
    }
}
