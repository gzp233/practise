<?php

namespace App\Models\Basic;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warehourse extends Model
{
    use SoftDeletes;

    protected $table = 'warehouse';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'warehouse_name', 'desc',
    ];

    public function areas()
    {
        return $this->hasMany('App\Models\Basic\Area', 'warehouse_id', 'id')->with('locations');
    }
}
