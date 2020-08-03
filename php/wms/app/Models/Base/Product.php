<?php

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{

    protected $table = 'product';
    const UPDATED_AT = NULL;

    protected $fileable = [
        'CompanyCd', 'PlaceCd', 'NewPRODUCTCD','PRODUCTCD','PRODCHINM','PRODENGNM','ProdFlg','ProductAvail','BRANDCD','SERIESCD','MAILINNO','MAILINNOBIG','MAILINNOBIGQNTY','OrderAvail','ADMDATAFLG','ADMSTARTYMD','DataYmd'
    ];

    // 获取品牌信息
    public function brand()
    {
        return $this->belongsTo('App\Models\Base\Brand', 'BRANDCD', 'BRANDCD');
    }

    // 获取系列信息
    public function series()
    {
        return $this->belongsTo('App\Models\Base\Series', 'SERIESCD', 'SERIESCD');
    }

    // 获取系列信息
    public function company()
    {
        return $this->belongsTo('App\Models\Base\Company', 'CompanyCd', 'CompanyCd');
    }
    
    // 获取产品区分信息
    public function prodflg()
    {
        return $this->belongsTo('App\Models\Base\Flg', 'ProdFlg', 'PRODFLG');
    }

    // 获取产品单位
    public function units()
    {
        return $this->hasMany('App\Models\Base\Unit');
    }
}
