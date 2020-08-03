<?php

namespace Modules\Label\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $connection = 'labelDB';

    protected $table = 'invoices';

    protected $guarded  = [];

    public function item()
    {
        return $this->hasOne('Modules\Label\Models\Item', 'material_code', 'material_code');
    }

}
