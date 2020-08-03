<?php

namespace App\Models\Outstorage;

use Illuminate\Database\Eloquent\Model;

class AdjustTag extends Model
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

    protected $table = 'adj_out_dirt_tag';

    public function adjust()
    {
        return $this->belongsTo('App\Models\Outstorage\Adjust', 'related_id');
    }
}
