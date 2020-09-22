<?php

namespace App\Models\Basic;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model
{
    use SoftDeletes;

    protected $table = 'stock';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'stock_no', 'area_id'
    ];

    public function area() {
        return $this->belongsTo('App\Models\Basic\Area', 'area_id', 'id')->with('warehourse');
    }
    public function state() {
        return $this->belongsTo('App\Models\Basic\Attributes', 'stock_state_id', 'id');
    }
}
