<?php

/**
 * Created by Valley.
 * User: Valley
 * Date: 2019/05/08
 * Time: 15:48
 */

namespace App\Http\Controllers\Storage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Storage\Yiku;
use App\Models\Storage\Goods;
use App\Models\Storage\GoodsRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Excel;
use PHPExcel_Worksheet_Drawing;

class YikuController extends Controller
{
    /**
     * Create a new Controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('refresh.token');
        $this->user = auth()->user();
    }

    // 移库列表
    public function index(Request $request)
    {
        $params = $request->all();
        $where = [];
        $limit = isset($params['limit']) ? $params['limit'] : 20;

        if (!empty($params['status'])) {
            if ($params['status'] == '未处理') {
                $where[] = ['status',  '=', 0];
            } else if ($params['status'] == '处理中') {
                $where[] = ['status',  '=', 1];
            } else {
                $where[] = ['status',  '=', 2];
            }
        }
        // dd($params);
        if (!empty($params['yiku_no'])) $where[] = ['yiku_no',  'like', '%' . $params['yiku_no']];
        if (!empty($params['stock_no'])) $where[] = ['origin_stock_no',  'like', '%' . $params['stock_no']];
        if (!empty($params['origin_stock_name'])) $where[] = ['origin_state_name',  'like', '%' . $params['origin_stock_name']];
        if (!empty($params['origin_available_time'])) $where[] = ['origin_available_time',  'like', '%' . $params['origin_available_time']];
        if (!empty($params['to_stock_no'])) $where[] = ['stock_no',  'like', '%' . $params['to_stock_no']];
        if (!empty($params['state_name'])) $where[] = ['state_name',  'like', '%' . $params['state_name']];
        if (!empty($params['available_time'])) $where[] = ['available_time',  'like', '%' . $params['available_time']];

        if (!empty($params['create_user'])) $where[] = ['create_user',  '=', $params['create_user']];
        if ($request->get('created_at') && $request->get('created_at')[0] && $request->get('created_at')[0] != 'null') {
            $starttime = date('Y-m-d H:i:s', strtotime($request->get('created_at')[0]));
            $endtime = date('Y-m-d H:i:s', strtotime($request->get('created_at')[1]));
            $where[] = ['created_at', '>=', $starttime];
            $where[] = ['created_at', '<=', $endtime];
        }
        if ($request->get('starttime') && $request->get('starttime')[0] && $request->get('starttime')[0] != 'null') {
            $starttime = date('Y-m-d H:i:s', strtotime($request->get('starttime')[0]));
            $endtime = date('Y-m-d H:i:s', strtotime($request->get('starttime')[1]));
            $where[] = ['starttime', '>=', $starttime];
            $where[] = ['starttime', '<=', $endtime];
        }
        if ($request->get('endtime') && $request->get('endtime')[0] && $request->get('endtime')[0] != 'null') {
            $starttime = date('Y-m-d H:i:s', strtotime($request->get('endtime')[0]));
            $endtime = date('Y-m-d H:i:s', strtotime($request->get('endtime')[1]));
            $where[] = ['endtime', '>=', $starttime];
            $where[] = ['endtime', '<=', $endtime];
        }
        $yikus = Yiku::where($where)
            ->whereHas('product', function ($q) use ($params) {
                if (!empty($params['NewPRODUCTCD'])) $q->where('NewPRODUCTCD',  'like', '%' . $params['NewPRODUCTCD']);
                if (!empty($params['PRODUCTCD'])) $q->where('PRODUCTCD',  'like', '%' . $params['PRODUCTCD']);
                if (!empty($params['PRODCHINM'])) $q->where('PRODCHINM',  'like', '%' . $params['PRODCHINM']);
            })
            ->whereHas('createUser', function ($q) use ($params) {
                if (!empty($params['create_user.username'])) $q->where('username',  'like', '%' . $params['create_user.username']);
            })
            ->whereHas('dealUser', function ($q) use ($params) {
                if (!empty($params['deal_user.username'])) $q->where('username',  'like', '%' . $params['deal_user.username']);
            })
            ->with(['dealUser', 'createUser', 'product'])
            ->orderBy('created_at', 'desc')
            ->paginate($limit);
        foreach ($yikus as $key => $yiku) {
            $yikus[$key]->status = $yiku->status == 0 ? '未处理' : ($yiku->status == 1 ? '处理中' : '处理完成');
        }

        return sendData(200, '', $yikus);
    }

    //购物车列表
    public function cartList(Request $request)
    {
        $cart = $this->getCartFromRedis();
        $data = $save = [];
        // 检查
        if ($cart) {
            foreach ($cart as $item) {
                $params = [
                    'g.product_id' => $item->product_id,
                    'g.stock_no' => $item->stock_no,
                    'g.state_name' => $item->state_name,
                    'g.available_time' => $item->available_time,
                ];
                $res = DB::table('goods as g')
                    ->leftJoin('product as p', 'p.id', '=', 'g.product_id')
                    ->leftJoin('prod_flg as f', 'p.ProdFlg', '=', 'f.PRODFLG')
                    ->leftJoin('brand as b', 'p.BRANDCD', '=', 'b.BRANDCD')
                    ->select(
                        'g.product_id',
                        'g.stock_no',
                        'g.state_name',
                        'g.available_time',
                        DB::raw('sum(g.number) as number'),
                        DB::raw('group_concat(g.id) as ids'),
                        'p.PRODCHINM',
                        'p.NewPRODUCTCD',
                        'p.PRODUCTCD'
                    )
                    ->groupBy('g.product_id', 'g.stock_no', 'g.state_name', 'g.available_time', 'p.PRODCHINM', 'p.NewPRODUCTCD', 'p.PRODUCTCD')
                    ->where($params)
                    ->whereNotIn('stock_no', ['复核区', '移库区', '加工区'])
                    ->orderBy('g.stock_no', 'asc')
                    ->first();
                if (!$res || $res->number != $item->number) continue;
                $goodsKey = $item->product_id . '_' . $item->stock_no . '_' . $item->state_name . '_' . $item->available_time;
                $save[$goodsKey] = $item;
                $data[] = $item;
            }
        }
        $this->setCartToRedis($save);

        return sendData(200, '', $data);
    }

    // 从redis中获取购物车数据
    private function getCartFromRedis()
    {
        return json_decode(Redis::get('yiku::cart::' . $this->user->id));
    }

    // 保存购物车数据到redis
    private function setCartToRedis($data)
    {
        if ($data) {
            Redis::set('yiku::cart::' . $this->user->id, json_encode($data));
        } else {
            Redis::del('yiku::cart::' . $this->user->id);
        }

        return true;
    }

    //添加到购物车
    public function addCart(Request $request)
    {
        $goods = $request->all();
        if (empty($goods)) return sendData(402, '添加的内容不能为空');
        $cart = $this->getCartFromRedis();
        $data = [];

        foreach ($goods as $item) {
            $goodsKey = $item['product_id'] . '_' . $item['stock_no'] . '_' . $item['state_name'] . '_' . $item['available_time'];
            if ($cart && isset($cart->$goodsKey)) {
                unset($cart->$goodsKey);
            }
            $data[$goodsKey] = $item;
        }
        if ($cart) {
            foreach ($cart as $goodsKey => $value) {
                $data[$goodsKey] = $value;
            }
        }

        $this->setCartToRedis($data);
        return sendData();
    }

    //购物车中删除
    public function delCart(Request $request)
    {
        $keys = $request->get('keys');
        if (empty($keys)) return sendData(402, '删除的条目不能为空');
        $cart = $this->getCartFromRedis();
        foreach ($keys as $key) {
            if (isset($cart->$key)) {
                unset($cart->$key);
            }
        }
        $this->setCartToRedis($cart);

        return sendData();
    }

    // 提交订单
    public function submitCart(Request $request)
    {
        $params = $request->get('params');
        $deal_user = (int) $request->get('deal_user');
        if (empty($params)) return sendData(402, '提交的数据不能为空！');
        if (empty($deal_user)) return sendData(402, '处理人不能为空！');

        $toMobile = $toPC = $keys = [];
        foreach ($params as $item) {
            $item['available_time'] = str_replace('-', '', $item['available_time']);
            $item['to_available_time'] = str_replace('-', '', $item['to_available_time']);
            // 如果是原地移动，则剔除
            $keys[] = $item['product_id'] . '_' . $item['stock_no'] . '_' . $item['state_name'] . '_' . $item['available_time'];
            if ($item['stock_no'] == $item['to_stock_no'] && $item['state_name'] == $item['to_state_name'] && $item['available_time'] == $item['to_available_time']) {
                continue;
            }
            if ($item['to_number'] <= 0) return sendData(402, '移动数量必修大于0');
            if ($item['number'] < $item['to_number']) return sendData(402, '移动数量不能大于库存数量');
            if ($item['stock_no'] == $item['to_stock_no']) {
                $toPC[] = $item;
            } else {
                $toMobile[] = $item;
            }
        }

        try {
            // 开启事务
            DB::beginTransaction();
            // 生成移库单ID
            $yiku_no = date('YmdHis') . rand(10, 99);
            // PC端自动处理的
            $huichuanFlag = 0;
            foreach ($toPC as $item) {
                if ($item['state_name'] != $item['to_state_name']) $huichuanFlag = 1;
                $this->execYiku($item, $yiku_no, $deal_user, 'toPC');
            }
            if (!empty($toMobile)) {
                foreach ($toMobile as $item) {
                    $this->execYiku($item, $yiku_no, $deal_user, 'toMobile');
                }
            } else {
                // 直接生成回传文件
                if ($huichuanFlag) {
                    $this->genTxt($toPC);
                }
                $username = $this->user->username;
                $name = date('Y-m-d');
                Log::useFiles(storage_path('logs/Movereturn/error' . $name . '.log'));
                //记录日志
                foreach ($toPC as $pc) {
                    Log::info('操作人：' . $username . '产品id：' . $pc['product_id'] . '移出状态：' . $pc['state_name'] . '移入状态：' . $pc['to_state_name'] . '数量：' . $pc['to_number']);
                }
            }
            $cart = $this->getCartFromRedis();
            foreach ($cart as $key => $value) {
                if (in_array($key, $keys)) unset($cart->$key);
            }
            $this->setCartToRedis($cart);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return sendData(402, $e->getMessage());
        }

        return sendData();
    }

    // 生成回传文件
    private function genTxt($data)
    {
        if (Cache::get('generate_file_gen') == 1) {
            $time = date('YmdHis', strtotime('+1second'));
        } else {
            Cache::put('generate_file_gen', 1, 0.1);
            $time = date('YmdHis');
        }
        $path = storage_path() . '/uploads/OUT/';
        $fileName = 'StcState7858' . $time . '.txt';
        $location = $path . $fileName;
        foreach ($data as $value) {
            $item = [
                'BLDAT' => date('Ymd'),
                'BUDAT' => date('Ymd'),
                'BKTXT' => date('Ymd') . rand(100000, 999999),
                'MATNR' => $value['NewPRODUCTCD'],
                'WERKS' => '7858',
                'LGORT' => $value['state_name'],
                'CHARG' => '00',
                'UMLGO' => $value['to_state_name'],
                'UMCHA' => '00',
                'BWART' => '311',
                'ERFMG' => $value['to_number'],
                'ERFME' => 'EA',
                'SGTXT' => '',
            ];;
            if (!file_exists($location)) {
                file_put_contents($location, implode("\x08", array_keys($item)), FILE_APPEND);
            }
            file_put_contents($location, "\r\n" . implode("\x08", array_values($item)), FILE_APPEND);
        }
        $okName = 'LO0057858' . $time . '.ok';
        file_put_contents($path . $okName, $fileName . "\x08" . filesize($location));
    }

    // 移库数据库操作
    private function execYiku($item, $yiku_no, $deal_user, $type)
    {
        $ids = explode(',', $item['ids']);
        $sum = Goods::whereIn('id', $ids)->sum('number');
        if ($sum != $item['number']) {
            throw new \Exception('库存状态发生变化，请刷新页面重试');
        }
        $total = $item['to_number'];
        foreach ($ids as $id) {
            if ($total == 0) break;
            $goods = Goods::find($id);
            if ($goods->frozen_number == 0) {
                if ($goods->number > $total) {
                    $goods->number -= $total;
                    $goods->save();
                    $total = 0;
                } else {
                    $goods->delete();
                    $total -= $goods->number;
                }
            } else {
                if ($goods->number > $total) {
                    $goods->number -= $total;
                    $goods->save();
                    $total = 0;
                } else {
                    $goods->number = 0;
                    $goods->save();
                    $total -= $goods->number;
                }
            }
        }

        $goodsData = [
            'product_id' => $item['product_id'],
            'state_name' => $item['to_state_name'],
            'CHARG' => '00',
            'number' => $item['to_number'],
            'available_time' => $item['to_available_time'],
            'real_number' => $item['to_number'],
            'frozen_number' => 0,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($type == 'toPC') {
            $goodsData['stock_no'] =  $item['to_stock_no'];
        } else {
            $goodsData['stock_no'] =  '移库区';
        }
        $rc = Goods::insertGetId($goodsData);
        $res = [
            'odd' => $yiku_no,
            'goods_id' => $rc,
        ];
        DB::table('goods_tag')->insert($res);
        //
        $other = [
            'origin_stock_no' => $item['stock_no'],
            'type' => 'yiku',
            'available_time' => $item['to_available_time'],
            'related_id' => 1,
            'odd' => $yiku_no,
        ];
        $goodsRecordData = array_merge($goodsData, $other);
        unset($goodsRecordData['real_number'], $goodsRecordData['frozen_number']);

        GoodsRecord::create($goodsRecordData);
        $yikuData = [
            'yiku_no' => $yiku_no,
            'create_user' => $this->user->id,
            'goods_id' => $rc,
            'deal_user' => $deal_user,
            'product_id' => $item['product_id'],
            'origin_stock_no' => $item['stock_no'],
            'origin_state_name' => $item['state_name'],
            'origin_available_time' => $item['available_time'],
            'stock_no' => $item['to_stock_no'],
            'state_name' => $item['to_state_name'],
            'available_time' => $item['to_available_time'],
            'number' => $item['to_number'],
            'status' => $type == 'toPC' ? 2 : 0
        ];
        if ($type == 'toPC') {
            $yikuData['starttime'] = date('Y-m-d H:i:s');
            $yikuData['endtime'] = date('Y-m-d H:i:s');
        }
        Yiku::create($yikuData);
    }
    public function export(Request $request)
    {
        $id = $request->get('id');
        $res = [['移库任务号', '产品中文名称', '新产品代码', '产品代码', '效期', '良品标志', '移出库位', '移入库位', '移动数量', '移入效期', '移入良品标志', '任务状态', '创建时间', '开始时间', '结束时间', '创建人ID', '执行人ID']];
        if (!$id) {
            $all = $request->all();
            $where = (array) json_decode($all['query']);
            foreach ($where as $k => $v) {
                if ($v == '') {
                    unset($where[$k]);
                }
                if ($k == 'created_at') {
                    if ($v[0] == null) {
                        unset($where[$k]);
                    }
                }
                if ($k == 'starttime') {
                    if ($v[0] == null) {
                        unset($where[$k]);
                    }
                }
                if ($k == 'endtime') {
                    if ($v[0] == null) {
                        unset($where[$k]);
                    }
                }
            }
            foreach ($where as $key => $val) {
                if ($key == 'created_at') {
                    $starttime = date('Y-m-d H:i:s', strtotime($where['created_at'][0]));
                    $endtime = date('Y-m-d H:i:s', strtotime($where['created_at'][1]));
                    $where[] = ['y.created_at', '>=', $starttime];
                    $where[] = ['y.created_at', '<=', $endtime];
                }
                if ($key == 'starttime') {
                    $starttime = date('Y-m-d H:i:s', strtotime($where['starttime'][0]));
                    $endtime = date('Y-m-d H:i:s', strtotime($where['starttime'][1]));
                    $where[] = ['y.starttime', '>=', $starttime];
                    $where[] = ['y.starttime', '<=', $endtime];
                }
                if ($key == 'endtime') {
                    $starttime = date('Y-m-d H:i:s', strtotime($where['endtime'][0]));
                    $endtime = date('Y-m-d H:i:s', strtotime($where['endtime'][1]));
                    $where[] = ['y.endtime', '>=', $starttime];
                    $where[] = ['y.endtime', '<=', $endtime];
                }
                if ($key == 'yiku_no') {
                    $where[] = ['y.yiku_no', '=', $val];
                }
                if ($key == 'PRODCHINM') {
                    $where[] = ['p.PRODCHINM', '=', $val];
                }
                if ($key == 'NewPRODUCTCD') {
                    $where[] = ['p.NewPRODUCTCD', '=', $val];
                }
                if ($key == 'PRODUCTCD') {
                    $where[] = ['p.PRODUCTCD', '=', $val];
                }
                if ($key == 'available_time') {
                    $where[] = ['y.available_time', '=', $val];
                }
                if ($key == 'origin_stock_name') {
                    $where[] = ['y.origin_state_name', '=', $val];
                }
                if ($key == 'stock_no') {
                    $where[] = ['y.origin_stock_no', '=', $val];
                }
                if ($key == 'to_stock_no') {
                    $where[] = ['y.stock_no', '=', $val];
                }
                if ($key == 'state_name') {
                    $where[] = ['y.state_name', '=', $val];
                }
                if ($key == 'create_user.username') {
                    $where[] = ['u.username', '=', $val];
                }
                if ($key == 'deal_user.username') {
                    $where[] = ['u.username', '=', $val];
                }
                if ($key == 'status') {
                    if ($val == '未处理') {
                        $where[] = ['y.status',  '=', 0];
                    } else if ($val == '处理中') {
                        $where[] = ['y.status',  '=', 1];
                    } else {
                        $where[] = ['y.status',  '=', 2];
                    }
                }
            }
            unset($where['yiku_no']);
            unset($where['PRODCHINM']);
            unset($where['NewPRODUCTCD']);
            unset($where['PRODUCTCD']);
            unset($where['available_time']);
            unset($where['origin_stock_name']);
            unset($where['stock_no']);
            unset($where['to_stock_no']);
            unset($where['state_name']);
            unset($where['created_at']);
            unset($where['starttime']);
            unset($where['endtime']);
            unset($where['create_user.username']);
            unset($where['deal_user.username']);
            unset($where['status']);
            $yikus = DB::table('yiku as y')
                ->leftJoin('user as u', 'y.create_user', '=', 'u.id')
                ->leftJoin('product as p', 'y.product_id', '=', 'p.id')
                ->where($where)
                ->orderBy('y.created_at', 'desc')
                ->get();
                foreach ($yikus as $key) {
                    $res[] = [
                        $key->yiku_no,
                        $key->PRODCHINM,
                        $key->NewPRODUCTCD,
                        $key->PRODUCTCD,
                        $key->origin_available_time,
                        $key->origin_state_name,
                        $key->origin_stock_no,
                        $key->stock_no,
                        $key->number,
                        $key->available_time,
                        $key->state_name,
                        $key->status == 0 ? '未处理' : ($key->status == 1 ? '处理中' : '处理完成'),
                        $key->created_at,
                        $key->starttime,
                        $key->endtime,
                        $key->username,
                        $key->username,
                    ];
                    
                }
        }else{
            $pieces = explode(",", $id);
            $yikus = Yiku::whereIn('yiku_no',$pieces)->with(['dealUser', 'createUser', 'product'])->orderBy('created_at', 'desc')->get();

            foreach ($yikus as $key) {
                $yi = ' '.$key['yiku_no'];
                $res[] = [
                    $yi,
                    $key['product']['PRODCHINM'],
                    $key['product']['NewPRODUCTCD'],
                    $key['product']['PRODUCTCD'],
                    $key['origin_available_time'],
                    $key['origin_state_name'],
                    $key['origin_stock_no'],
                    $key['stock_no'],
                    $key['number'],
                    $key['available_time'],
                    $key['state_name'],
                    $key['status'] == 0 ? '未处理' : ($key['status'] == 1 ? '处理中' : '处理完成'),
                    $key['created_at'],
                    $key['starttime'],
                    $key['endtime'],
                    $key['dealUser']['username'],
                    $key['dealUser']['username'],
                ];
            }
        }
        // dd($res);
        $name = '库内移动';
        Excel::create($name, function ($excel) use ($res) {
            $excel->sheet('score', function ($sheet) use ($res) {
                $sheet->getStyle('A')->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                $styleThinBlackBorderOutline = array(
                    'borders' => array(
                        'allborders' => array( //设置全部边框
                            'style' => \PHPExcel_Style_Border::BORDER_THIN //粗的是thick
                        ),
                    ),
                );
                $sheet->rows($res);
                $count = count($res);
                $sheet->getStyle('A1:' . 'Q' . $count)->applyFromArray($styleThinBlackBorderOutline);
            });
        })->export('xls');
    }
}
