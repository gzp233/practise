<?php

namespace App\Models\Outstorage;

use Illuminate\Database\Eloquent\Model;

class MoveOutTag extends Model
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

    protected $table = 'move_out_dirt_tag';

    public function moveOut()
    {
        return $this->belongsTo('App\Models\Outstorage\MoveOut', 'related_id');
    }
}
