<?php

namespace Modules\Label\Models;

use Illuminate\Database\Eloquent\Model;

class Stage extends Model
{

    protected $connection = 'labelDB';

    protected $table = 'stages';

    protected $guarded  = [];

    public function item()
    {
        return $this->hasOne('Modules\Label\Models\Item', 'material_code', 'material_code');
    }

}
