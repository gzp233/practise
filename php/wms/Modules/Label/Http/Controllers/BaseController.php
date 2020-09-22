<?php

namespace Modules\Label\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Label\Models\Location;
use Modules\Label\Models\Log;
use Modules\Label\Models\ItemStock;
use Modules\Label\Models\LabelStock;

class BaseController extends Controller
{
    const LOG_OTHER = 0; // 其他类型操作
    const LOG_IMPORT = 1; // 导入操作
    const LOG_STACK = 2; // 上架操作
    const LOG_QUERY = 3; // 查询操作
    const LOG_STAMP = 4; // 盖章操作
    const LOG_STICK = 5; // 贴标操作
    const LOG_OUTSTORAGE = 6; // 出库操作
    const LOG_FROZE = 7; // 冻结操作
    const LOG_ABANDON = 8; // 废弃操作
    const LOG_MOVE = 9; // 移库操作

    public  $specialLocations = ['暂存区'];

    // 构建函数
    public function __construct()
    {
        $this->user = auth()->user();
    }
    /**
     * API返回格式定义
     *
     * @param integer $code
     * @param string $message
     * @param array $data
     * @return array
     */
    protected function sendData($code = 200, $message = '', $data = [])
    {
        $result = [
            'code' => $code,
            'message' => $message,
            'data' => $data
        ];

        return response()->json($result);
    }

    // 保存文件到服务器
    protected function saveFile($file)
    {
        $path = storage_path() . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'label' . DIRECTORY_SEPARATOR . date('Ymd');
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        $extension = $file->extension() ? $file->extension() : 'xls';
        $filePath = $path . DIRECTORY_SEPARATOR . uniqid() . '.' . $extension;
        move_uploaded_file($file->path(), $filePath);

        return $filePath;
    }

    // 存储操作到数据库
    public function log($type, $action)
    {
        if (!$type || !$action) return false;
        if (!in_array($type, [1, 2, 3, 4, 5, 6, 7, 8, 9])) $type = 0;
        return Log::create(['type' => $type, 'action' => $action, 'user_id' => $this->user->id]);
    }

    // 生成唯一ID
    protected function genId()
    {
        return strtoupper(md5(uniqid(mt_rand(), true)));
    }

    // 获取所有库位信息
    protected function getAllLocations($type)
    {
        $type == 'label' ? $type = 1 : $type = 0;
        return Location::where('type', $type)->pluck('location_no')->toArray();
    }

    // 根据商品编号组织一下提交的数组
    protected function organizeData($items, $type)
    {
        $locations = $this->getAllLocations($type);
        $data = [];
        foreach ($items as $item) {
            if (empty($item['item_no']) || empty($item['invoice_no'])) {
                return ['code' => 1, 'message' => '发票号或商品代码不能为空'];
            }
            if (empty($item['num']) || $item['num'] <= 0) {
                return ['code' => 1, 'message' => '数量必须大于0'];
            }
            if (empty($item['location_no'])) {
                return ['code' => 1, 'message' => '库位号不能为空'];
            }
            if (!in_array($item['location_no'], $locations) && !in_array($item['location_no'], $this->specialLocations)) {
                return ['code' => 1, 'message' => '库位号' . $item['location_no'] . '不存在'];
            }
            if (empty($data[$item['item_no']])) {

                $data[$item['item_no']] = [
                    'total' => $item['num'],
                    'data' => [$item],
                ];
            } else {
                $data[$item['item_no']]['total'] += $item['num'];
                $data[$item['item_no']]['data'][] = $item;
            }
        }

        return $data;
    }

    // 校验库位托盘是否1对1,暂存区的先不管
    protected function checkSupport($items)
    {
        $others = [];
        // 获取非暂存区的库位号，托盘号对应，所有的发票号和托盘号对应
        $supports = ItemStock::whereNotIn('location_no', $this->specialLocations)
            ->where('status', '<>', 1)
            ->pluck('support_no', 'location_no')
            ->toArray();
        $invoices_nos = ItemStock::where('status', '<>', 1)->pluck('invoice_no', 'support_no')->toArray();
        $locations = array_flip($supports);
        $specials = [];

        foreach ($items as $item) {
            if (!$item['support_no']) return '托号不能为空';
            if (isset($invoices_nos[$item['support_no']]) && $invoices_nos[$item['support_no']] != $item['invoice_no']) {
                return $item['support_no'] . '托号已有其他发票号的商品';
            }
            if (!in_array($item['location_no'], $this->specialLocations)) {
                $others[] = $item;
            } else {
                $specials[] = $item['support_no'];
            }
            if (in_array($item['support_no'], array_values($supports))) return $item['support_no'] . '托号已被使用';
        }

        foreach ($others as $item) {
            if (isset($supports[$item['location_no']]) && $supports[$item['location_no']] != $item['support_no']) {
                return $item['support_no'] . '只能放在一个库位上';
            }
            if (isset($locations[$item['support_no']]) && $locations[$item['support_no']] != $item['location_no']) {
                return $item['location_no'] . '只能放一个托盘';
            }
            if (in_array($item['support_no'], $specials)) return $item['support_no'] . '只能放在一个位置';
            $supports[$item['location_no']] = $item['support_no'];
            $locations[$item['support_no']] = $item['location_no'];
        }

        return false;
    }

    // 把托盘上的其他商品上架
    protected function stackOthers($items)
    {
        $supportNos = [];
        foreach ($items as $item) {
            if ($item['location_no'] != '暂存区' && !in_array($item['support_no'], $supportNos)) {
                ItemStock::where(['support_no' => $item['support_no'], 'location_no' => '暂存区'])->update(['location_no' => $item['location_no']]);
                $supportNos[] = $item['support_no'];
            }
        }

        return true;
    }

    public function getTime($time)
    {
        $expired_at = $time;
        $expired_at = str_replace('有效期', '', $expired_at);
        $expired_at = str_replace('新地址', '', $expired_at);
        $expired_at = str_replace('年', '.', $expired_at);
        $expired_at = str_replace('月', '.', $expired_at);
        $expired_at = str_replace('日', '', $expired_at);
        if (strpos($expired_at, '.') !== false) {
            $arr = explode('.', $expired_at);
            if (count($arr) == 3) {
                if (strlen($arr[1]) == 1) $arr[1] = '0' . $arr[1];
                if (strlen($arr[2]) == 1) $arr[2] = '0' . $arr[2];
            }
            $expired_at = implode('-', $arr);
        }

        if (strtotime($expired_at)) return $expired_at;
        return $time;
    }

    // 标签插入库存
    protected function labelIn($item)
    {
        $search = $item;
        unset($search['num']);
        unset($search['frozen_num']);
        $stock = LabelStock::where($search)->first();
        if ($stock) {
            if (!empty($item['num'])) {
                $stock->num += $item['num'];
            }
            if (!empty($item['frozen_num'])) {
                $stock->frozen_num += $item['frozen_num'];
            }
            if ($stock->num == 0) {
                $stock->delete();
            } else {
                $stock->save();
            }
        } else {
            LabelStock::create($item);
        }
    }

    // 商品插入库存
    protected function itemIn($item)
    {
        $search = $item;
        unset($search['num']);
        $stock = ItemStock::where($search)->first();
        if ($stock) {
            $stock->num += $item['num'];
            $stock->save();
        } else {
            ItemStock::create($item);
        }
    }

    // 判断制造记号
    protected function getMark($branch_mark, $case_mark, $box_mark)
    {
        return $branch_mark ? $branch_mark : ($case_mark ? $case_mark : ($box_mark ? $box_mark : ''));
    }
}
