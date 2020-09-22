<?php

namespace App\Models\Instorage;

use Illuminate\Database\Eloquent\Model;

class MovementStorageTag extends Model
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
    protected $table = 'move_in_dirt_tag';
}
