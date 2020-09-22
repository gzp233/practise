<?php

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Model;

class Deliver extends Model
{

    protected $table = 'deliver_address';

    protected $fileable = [
        'CompanyCd','DELFLG','DeliverAdd','DeliverAddCD','DeliverAddNM','FAXNO','POSTCD','TELNO','DataYmd'
    ];
}
