<?php

namespace App\Models\Basic;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Area extends Model
{
    use SoftDeletes;

    protected $table = 'area';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'area_name', 'warehouse_id'
    ];

    public function warehourse() {
        return $this->belongsTo('App\Models\Basic\Warehourse', 'warehouse_id', 'id');
    }

    public function locations()
    {
        return $this->hasMany('App\Models\Basic\Location', 'area_id', 'id');
    }
}
