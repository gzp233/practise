<?php

namespace Modules\Label\Models;

use Illuminate\Database\Eloquent\Model;

class AbandonedLabel extends Model
{
    protected $connection = 'labelDB';

    protected $table = 'abandoned_labels';

    protected $guarded  = [];
}
