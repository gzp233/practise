<?php

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Model;

class Rejected extends Model
{

    protected $table = 'rejected_reason';

    protected $fileable = [
        'CompanyCd','RejectedReasonCd','RejectedReasonNm','DataYmd'
    ];
}
