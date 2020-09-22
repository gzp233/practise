<?php

namespace Modules\Label\Models;

use Illuminate\Database\Eloquent\Model;

class LabelArrival extends Model
{
    protected $connection = 'labelDB';

    protected $table = 'label_arrivals';

    protected $guarded  = [];

}
