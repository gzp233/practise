<?php

namespace Modules\Label\Models;

use Illuminate\Database\Eloquent\Model;

class QueryResult extends Model
{
    protected $connection = 'labelDB';

    protected $table = 'query_result';

    protected $guarded  = [];

}
