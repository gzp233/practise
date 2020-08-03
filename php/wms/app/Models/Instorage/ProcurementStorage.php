<?php

namespace App\Models\Instorage;

use Illuminate\Database\Eloquent\Model;

class ProcurementStorage extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'prod_imp';

    protected $fileable = [
        'InvoiceNo','CompanyCD','PlaceCd','NewProductCd','ProductCd','PRODCHINM','PRODENGNM','ProdFlg','ImportQnty','MailinnoBig','MailinnoBigQnty','InstcDestYmd','BSART','PARTN','POSNR','LGORT','EKPOBEDNR','created_at'
    ];


    public function product()
    {
        return $this->hasOne('App\Models\Base\Product', 'NewPRODUCTCD', 'NewProductCd');
    }

    public function tag()
    {
        return $this->hasOne('App\Models\Instorage\ProcurementStorageTag', 'related_id');
    }
}
