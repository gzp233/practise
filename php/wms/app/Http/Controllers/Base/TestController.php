<?php

namespace App\Http\Controllers\Base;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderShipped;

class TestController extends Controller
{
    private $txtMap = [
        'MasterBrand' => 'brand',
        'MasterCompany' => 'company',
        'MasterCustomer' => 'customer',
        'MasterDeliverAddress' => 'deliver_address',
        'MasterProdflg' => 'prod_flg',
        'MasterProduct' => 'product',
        'MasterReason' => 'reason',
        'MasterRejectedReason' => 'rejected_reason',
        'MasterSeries' => 'series',
        'MasterVendor' => 'vendor',
        'AdjOutDirt' => 'adj_out_dirt',
        'ASN' => 'asn',
        'MoveInDirt' => 'move_in_dirt',
        'MoveOutDirt' => 'move_out_dirt',
        'OrdOutDirt' => 'ord_out_dirt',
        'Pre-picking' => 'pre_picking',
        'ProdImp' => 'prod_imp',
        'RetInDirt' => 'ret_in_dirt',
    ];

    private $notices = ['AdjOutDirt', 'ASN', 'MoveInDirt', 'MoveOutDirt', 'OrdOutDirt', 'Pre-picking', 'ProdImp', 'RetInDirt'];

    private $masterMap = [
        'brand' => 'BRANDCD',
        'company' => 'CompanyCd',
        'customer' => 'CUSTOMERCD',
        'deliver_address' => 'DeliverAddCD',
        'prod_flg' => 'PRODFLG',
        'product' => 'NewPRODUCTCD',
        'reason' => 'ReasonCd',
        'rejected_reason' => 'RejectedReasonCd',
        'series' => 'SERIESCD',
        'vendor' => 'VendorCode',
    ];

    private $okMap = [];
    private $path = '';

    public function __construct()
    {
        $this->path = storage_path() . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'IN';
        // 禁用日志记录
        DB::connection()->disableQueryLog();
    }

    // 解析txt文件,返回一个数组
    private function parseOk($oks)
    {
        $result = [];
        foreach ($oks as $ok) {
            $filePath = $this->path . DIRECTORY_SEPARATOR . $ok;
            $dom = $this->checkBOM(file_get_contents($filePath));
            $res = explode(PHP_EOL, $dom);
            foreach ($res as $item) {
                $fields = explode("\x08", $item);
                $result[$fields[0]] = ['name' => $fields[0], 'size' => $fields[1], 'okFile' => $ok, 'time' => 0, 'count' => 0];
            }
        }

        $this->okMap = $result;
        $result = null;
    }

    private function parseTXT($txts)
    {
        DB::beginTransaction();
        try {
            foreach ($txts as $txt) {
                $start = microtime(true);
                // 验证文件
                $filePath = $this->path . DIRECTORY_SEPARATOR . $txt;
                if (!array_key_exists($txt, $this->okMap) || filesize($filePath) != $this->okMap[$txt]['size']) {
                    continue;
                }

                // 逐行读取
                $line = '';
                $count = 0;
                $keys = [];

                $fp = fopen($filePath, 'r');
                clearstatcache(true, $filePath);
                // 获取数据数组keys

                $line = $this->checkBOM(stream_get_line($fp, 8192, PHP_EOL));
                $fields = explode("\x08", $line);
                foreach ($fields as $key => $value) {
                    $keys[] = trim($value);
                }

                //获取values并处理
                while (!feof($fp)) {
                    $values = [];
                    $line = $this->checkBOM(stream_get_line($fp, 8192, PHP_EOL));
                    $fields = explode("\x08", $line);
                    foreach ($fields as $k => $v) {
                        if (trim($v) !== 0 && trim($v) !== '0' && empty(trim($v))) {
                            $v = null;
                        } else {
                            $v = trim($v);
                        }
                        $values[] = $v;
                    }
                    $item = array_combine($keys, $values);
                   
                    // 插入数据库
                    $this->insertToDB($txt, $item);
                    $this->okMap[$txt]['count']++;
                }
                fclose($fp);
                $this->okMap[$txt]['time'] = microtime(true) - $start;
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }
        DB::commit();
        return false;
    }

    private function insertToDB($name, $item)
    {
        foreach ($this->txtMap as $prefix => $tableName) {
            if (preg_match('/^' . $prefix . '[0-9]*.txt$/', $name)) {
                $item['created_at'] = date('Y-m-d H:i:s');
                if (in_array($tableName, array_keys($this->masterMap))) {
                    $record = DB::table($tableName)->where($this->masterMap[$tableName], $item[$this->masterMap[$tableName]])->first();
                    if ($record) {
                        DB::table($tableName)->where($this->masterMap[$tableName], $item[$this->masterMap[$tableName]])->update($item);
                    } else {
                        DB::table($tableName)->insert($item);
                    }
                } else {
                    $sub =  substr($name,strpos($name,'7')+1,17);
                    $str = date("Y-m-d H:i:s",strtotime(substr($sub,3)));
                    $time =[
                        'time'=>$str
                    ];  
                    $item = array_merge($item,$time);
                    DB::table($tableName)->insert($item);
                }
            }
        }
    }

    //过滤BOM
    private function checkBOM($contents)
    {
        $charset[1] = substr($contents, 0, 1);
        $charset[2] = substr($contents, 1, 1);
        $charset[3] = substr($contents, 2, 1);
        if (ord($charset[1]) == 239 && ord($charset[2]) == 187 && ord($charset[3]) == 191) {
            $rest = substr($contents, 3);
            return $rest;
        }
        return $contents;
    }

    // 入口
    public function scanForTXT()
    {
        set_time_limit(0);
        $start = microtime(true);
        if (!is_dir($this->path)) return '目录错误';
        $files = array_filter(scandir($this->path), function ($filename) {
            return is_file($this->path . DIRECTORY_SEPARATOR . $filename);
        });

        $oks = array_filter($files, function ($filename) {
            return preg_match('/[A-Za-z0-9\-]*.ok$/', $filename);
        });
        $txts = array_filter($files, function ($filename) {
            return preg_match('/[A-Za-z0-9\-]*.txt$/', $filename);
        });

        // 分析OK文件
        $this->parseOk($oks);
        if (empty($this->okMap)) return false;
        // 处理TXT文件
        $res = $this->parseTXT($txts);
        if ($res) {
            return "发生错误，错误信息：" . $res;
        }
        // 删除和备份文件
        $path = storage_path() . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'backup' . DIRECTORY_SEPARATOR . date('Ymd');
        if (!is_dir($path)) mkdir($path);
        $msg = '共处理' . count($txts) . '个文件<br>';
        $sendMail = false;
        $date = array_column($this->okMap, 'time');
        array_multisort($date,SORT_ASC,$this->okMap);
        foreach ($this->okMap as $value) {
            foreach ($this->notices as $notice) {
                if ($sendMail) break;
                if (preg_match('/^' . $notice . '[0-9]*.txt$/', $value['name'])) $sendMail = true;
            }
            if (!rename($this->path . DIRECTORY_SEPARATOR . $value['name'], $path . DIRECTORY_SEPARATOR . $value['name'])) {
                $msg .= '文件' . $value['name'] . '备份失败<br>';
            }
            if (file_exists($this->path . DIRECTORY_SEPARATOR . $value['okFile'])) {
                if (!rename($this->path . DIRECTORY_SEPARATOR . $value['okFile'], $path . DIRECTORY_SEPARATOR . $value['okFile'])) {
                    $msg .= '文件' . $value['okFile'] . '备份失败<br>';
                }
            }
            $msg .= '文件' . $value['name'] . '更新了' . $value['count'] . '条数据，耗时' . $value['time'] . '秒<br>';
        }

        $msg .= '总耗时' . (microtime(true) - $start) . '秒';
        // 发送邮件
        if ($sendMail) Mail::to(explode(',', env('MAIL_TO')))->send(new OrderShipped($msg));

        return $msg;
    }
    public function sync()
    {
        $res = DB::select('SELECT * FROM `goods` WHERE `product_id` IN ( SELECT `product_id` FROM `goods` GROUP BY `product_id` HAVING count( `product_id` ) > 1 ) order By `product_id`,`stock_no`,`available_time`,`state_name` desc');
        $pre = $prf = [];
        $pre_key = '';
        foreach ($res as $k => $info) {
            $key = $info->product_id . '-' . $info->available_time. '-'. $info->stock_no . '-'. $info->state_name;
            if ($k === 0) {
                $pre[$key] = $info;
                $pre_key = $key;
                continue;
            }
            if (array_key_exists($key, $pre)) {
                $tag = DB::table('goods_tag')->where('goods_id',$info->id)->first();
                $frost = DB::table('frost')->where('goods_id',$info->id)->first();
                if($frost){
                    DB::table('frost')->where('goods_id',$info->id)->update(['goods_id' => $pre[$key]->id]);
                }
                if($tag){
                    DB::table('goods_tag')->where('goods_id',$info->id)->update(['goods_id' => $pre[$key]->id]);
                }
                $pre[$key]->number += $info->number;
                $pre[$key]->real_number += $info->real_number;
                $pre[$key]->frozen_number += $info->frozen_number;
                DB::table('goods')->where('id',$info->id)->delete();
            } else {
                $prf[] = $pre[$pre_key];
                $pre = [];
                $pre_key = $key;
                $pre[$key] = $info;
            }
        }
        //最后一条特殊处理
        $prf[] = $pre[$pre_key];
        
        foreach($prf as $val){
            $where = [
                'product_id' => $val->product_id,
                'stock_no' => $val->stock_no,
                'state_name' => $val->state_name,
                'CHARG' => $val->CHARG,
                'number' => $val->number,
                'available_time' => $val->available_time,
                'real_number' => $val->real_number,
                'frozen_number' => $val->frozen_number,
                'created_at' => $val->created_at,
                'updated_at' => $val->updated_at,
                'odd' => $val->odd,
            ];
            $ids = DB::table('goods')->insertGetId($where);
            $tags = DB::table('goods_tag')->where('goods_id',$val->id)->first();
            $frosts = DB::table('frost')->where('goods_id',$val->id)->first();
            if($frosts){
                DB::table('frost')->where('goods_id',$val->id)->update(['goods_id' => $ids]);
            }
            if($tags){
                DB::table('goods_tag')->where('goods_id',$val->id)->update(['goods_id' => $ids]);
            }
            DB::table('goods')->where('id',$val->id)->delete();
        }
        return 'ok';
    }
}
