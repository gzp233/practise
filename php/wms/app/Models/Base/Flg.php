<?php

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Model;

class Flg extends Model
{

    protected $table = 'prod_flg';

    protected $fileable = [
        'CompanyCd','DELFLG','PRODFLG','PRODFLGNM','DataYmd'
    ];

    public function products()
    {
        return $this->hasMany('App\Models\Base\Product', 'PRODFLG', 'ProdFlg');
    }
}
