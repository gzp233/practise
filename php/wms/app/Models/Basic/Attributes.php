<?php

namespace App\Models\Basic;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attributes extends Model
{
    use SoftDeletes;

    protected $table = 'stock_state';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'state_name',
    ];
}
