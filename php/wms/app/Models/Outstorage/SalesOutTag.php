<?php

namespace App\Models\Outstorage;

use Illuminate\Database\Eloquent\Model;

class SalesOutTag extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'created_at'
    ];
    const UPDATED_AT = null;

    protected $table = 'ord_out_dirt_tag';

    public function salesOut()
    {
        return $this->belongsTo('App\Models\Outstorage\SalesOut', 'related_id');
    }
}
