<?php

namespace Modules\Label\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $connection = 'labelDB';

    protected $table = 'logs';

    const UPDATED_AT = null;

    protected $guarded  = [];

}
