<?php

namespace Modules\Label\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Label\Models\LabelArrival;
use Illuminate\Support\Facades\DB;
use Modules\Label\Utils\Import;
use Illuminate\Support\Facades\Log;
use Modules\Label\Models\LabelStock;

class LabelController extends BaseController
{
    // 分页获取标签到货明细
    public function index(Request $request)
    {
        $limit = $request->get('limit') ? $request->get('limit') : 20;
        $sort = $request->get('sort') ? $request->get('sort') : 'updated_at';
        $where = [];
        if ($request->get('invoice_no')) $where[] = ['invoice_no', 'like', '%'. $request->get('invoice_no'). '%'];
        if ($request->get('item_no')) $where[] = ['item_no', '=', $request->get('item_no')];
        $arrivals = LabelArrival::where($where)->orderBy($sort, 'desc')->paginate($limit);

        return $this->sendData(200, '', $arrivals);
    }

    // 下载导入模板
    public function downloadTpl(Request $request)
    {
        $import = new Import('', 'label_arrivals', true);

        return $import->downloadTpl();
    }

    // 标签到货明细导入
    public function import(Request $request)
    {

        if (!$request->hasFile('file')) return $this->sendData(402, '上传失败!');
        try {
            $path = $this->saveFile($request->file('file'));
            $import = new Import($path, 'label_arrivals');
            $excelData = $import->getImportData();
            // 如果基础数据验证有错误，直接报错
            DB::connection('labelDB')->beginTransaction();
            if ($excelData['errors'])  return $this->sendData(200, '', $excelData['errors']);
            $count = 0;
            foreach ($excelData['data'] as $item) {
                $search = [
                    'invoice_no' => $item['invoice_no'],
                    'item_no' => $item['item_no']
                ];
                $labelArrival = LabelArrival::where($search)->first();
                if ($item['expired_at']) {
                    $item['expired_at'] = $this->getTime($item['expired_at']);
                }
                if (!$labelArrival) {
                    LabelArrival::create($item);
                    $count++;
                }
            }
            DB::connection('labelDB')->commit();
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            DB::connection('labelDB')->rollBack();
            return $this->sendData(402, $e->getMessage());
        }
        if ($count > 0) $this->log(self::LOG_IMPORT, '导入' . $count . '条标签到货明细数据');

        return $this->sendData(200, '', ['count' => $count]);
    }

    //根据ID获取一条数据
    public function getById(Request $request)
    {
        $id = $request->get('id');
        if (!$id) return $this->sendData(402, 'ID不能为空');
        return $this->sendData(200, '', LabelArrival::find($id));
    }

    //保存到货明细
    public function save(Request $request)
    {
        $params = $request->all();
        if (empty($params['item_no'])) return $this->sendData(402, '进口代码不能为空');
        if (empty($params['invoice_no'])) return $this->sendData(402, '发票号不能为空');
        if (empty($params['label_name'])) return $this->sendData(402, '标签名称不能为空');
        if (empty($params['num']) || $params['num'] <= 0) return $this->sendData(402, '数量不能小于0');
        if (!empty($params['expired_at'])) {
            $params['expired_at'] = $this->getTime($params['expired_at']);
        }

        $exist = LabelArrival::where(['item_no' => $params['item_no'], 'invoice_no' => $params['invoice_no']])->first();
        if (isset($params['id']) && $arrival = LabelArrival::find($params['id'])) {
            if ($exist && $exist->id != $params['id']) return $this->sendData(402, '商品不能重复');
            $arrival->num = $params['num'];
            $arrival->expired_at = $params['expired_at'];
            $arrival->save();
            $this->log(self::LOG_OTHER, '修改到库信息发票号' . $params['invoice_no'] . '商品' . $params['item_no']);
        } else {
            if ($exist) return $this->sendData(402, '商品不能重复');
            LabelArrival::create([
                'pro_mag_no' => 0,
                'item_no' => $params['item_no'],
                'invoice_no' => $params['invoice_no'],
                'label_name' => $params['label_name'],
                'customer_order_no' => 0,
                'num' => $params['num'],
                'expired_at' => $params['expired_at'],
            ]);
            $this->log(self::LOG_OTHER, '添加到库信息发票号' . $params['invoice_no'] . '商品' . $params['item_no']);
        }

        return $this->sendData();
    }

    // 删除
    public function delete(Request $request)
    {
        $ids = $request->get('ids');
        if (!$ids) return $this->sendData(402, 'ID不能为空');
        $arrivals = LabelArrival::whereIn('id', $ids)->get();
        if (count($arrivals) == 0) return $this->sendData(402, 'ID错误');
        // 如果已上架，就不能删除
        foreach ($arrivals as $arrival) {
            $count = LabelStock::where(['invoice_no' => $arrival->invoice_no, 'item_no' => $arrival->item_no])->count();
            if ($count > 0) return $this->sendData(402, '已入库，无法删除');
        }
        LabelArrival::whereIn('id', $ids)->delete();
        $this->log(self::LOG_OTHER, '删除标签到货明细');

        return $this->sendData();
    }
}
