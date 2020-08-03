<?php

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Model;

class Reason extends Model
{

    protected $table = 'reason';

    protected $fileable = [
        'CompanyCd','ReasonCd','ReasonNm','DataYmd'
    ];
}
