<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2018/11/20
 * Time: 15:48
 */
namespace App\Http\Controllers\Barcode;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Storage\Goods;
use App\Models\Storage\GoodsRecord;
use Illuminate\Support\Facades\Redis;
use App\Models\Base\Product;
use function GuzzleHttp\json_encode;
use Illuminate\Support\Facades\Storage;
use baidu\ocr\AipOcr;

class OcrController extends Controller
{
    public function __construct()
    {
        $this->middleware('refresh.token');
    }

    public function ocr($data)
    {
        
    
    }

    public function upload(Request $request){
        $base64_img = base64_decode(trim($request->get('id')));
        $client = new AipOcr(config('ocr.APP_ID'), config('ocr.API_KEY'), config('ocr.SECRET_KEY'));
        $result = $client->basicGeneral($base64_img);
        $arr = [];
        foreach($result as $key=>$val){
            if($key == 'words_result'){
                foreach ($val as $list){
                    $arr[]= $list['words'];
                //     if(preg_match("/^[0-9]{4}$/",$list['words']) || preg_match("/^[0-9]{4}\w{1}$/",$list['words']) || preg_match("/^[0-9]{4}\w{2}$/",$list['words'])){
                //         return sendData(200, '', $list['words']);
                //     }else{
                //         continue;
                //     }
                }
            }
        }
        return sendData(200, '', $arr);
    }

}
