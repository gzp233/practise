<?php

namespace Modules\Label\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model
{
    use SoftDeletes;

    protected $connection = 'labelDB';

    protected $table = 'locations';

    protected $dates = ['deleted_at'];

    protected $guarded  = [];

}
