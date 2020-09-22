<?php

namespace Modules\Label\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Label\Models\Item;
use Illuminate\Support\Facades\Log;
use Modules\Label\Utils\Import;
use Illuminate\Support\Facades\DB;
use Excel;
use PHPExcel_Worksheet_Drawing;

class ItemController extends BaseController
{
    // 分页获取商品信息
    public function index(Request $request)
    {
        $limit = $request->get('limit') ? $request->get('limit') : 20;
        $sort = $request->get('sort') ? $request->get('sort') : 'updated_at';
        $where = [];
        if ($request->get('item_no')) $where[] = ['item_no', '=', $request->get('item_no')];
        if ($request->get('material_code')) $where[] = ['material_code', '=', $request->get('material_code')];
        $items = Item::where($where)->orderBy($sort, 'desc')->paginate($limit);

        return $this->sendData(200, '', $items);
    }

    //根据ID获取一条数据
    public function getById(Request $request)
    {
        $id = $request->get('id');
        if (!$id) return $this->sendData(402, 'ID不能为空');
        return $this->sendData(200, '', Item::find($id));
    }


    //修改商品信息
    public function save(Request $request)
    {
        // $file = $request->all();
        // dd($file);
        $params = $request->all();
        // dd($params);
        if (empty($params['item_no'])) return $this->sendData(402, '产品代码不能为空');
        if (empty($params['material_code'])) return $this->sendData(402, '新产品代码不能为空');
        if (empty($params['name'])) return $this->sendData(402, '中文名不能为空');
        if (empty($params['brand'])) return $this->sendData(402, '品牌不能为空');
        if (empty($params['flg'])) return $this->sendData(402, '产品类型不能为空');
        if (!isset($params['label_usage']) || $params['label_usage'] < 0) {
            return $this->sendData(402, '贴标数不能小于0');
        }
        if (!isset($params['valid_month']) || $params['valid_month'] <= 0) {
            return $this->sendData(402, '有效期必须大于0');
        }
        if (!isset($params['case_num']) || $params['case_num'] <= 0) {
            return $this->sendData(402, '盒规必须大于0');
        }
        if (!isset($params['box_num']) || $params['box_num'] <= 0) {
            return $this->sendData(402, '箱规必须大于0');
        }
        if (!isset($params['support_num']) || $params['support_num'] <= 0) {
            return $this->sendData(402, '托规必须大于0');
        }
    
        if (empty($params['is_mark_valid']) || !in_array($params['is_mark_valid'], ['是', '否'])) {
            return $this->sendData(402, '是否查询制造记号必须为"是"或者"否"');
        } else {
            $params['is_mark_valid'] = $params['is_mark_valid'] == '是' ? 0 : 1;
        }
        if (empty($params['is_stick_valid']) || !in_array($params['is_stick_valid'], ['是', '否'])) {
            return $this->sendData(402, '是否贴标必须为"是"或者"否"');
        } else {
            $params['is_stick_valid'] = $params['is_stick_valid'] == '是' ? 0 : 1;
        }
        $data = [
            'item_no' => $params['item_no'],
            'material_code' => $params['material_code'],
            'name' => $params['name'],
            'brand' => $params['brand'],
            'flg' => $params['flg'],
            'label_usage' => $params['label_usage'],
            'case_num' => $params['case_num'],
            'box_num' => $params['box_num'],
            'support_num' => $params['support_num'],
            'is_mark_valid' => $params['is_mark_valid'],
            'valid_month' => $params['valid_month'],
            'is_stick_valid' => $params['is_stick_valid'],
            'note' => empty($params['note']) ? '' : $params['note']
        ];
        $exist = Item::where(['material_code' => $params['material_code']])->first();
        if (isset($params['id']) && $item = Item::find($params['id'])) {
            if ($exist && $exist->id != $params['id']) return $this->sendData(402, '新产品代码不能重复');
            Item::where(['id' => $params['id']])->update($data);
            $this->log(self::LOG_OTHER, '修改商品' . $params['material_code']);
        } else {
            if ($exist) return $this->sendData(402, '新产品代码不能重复');
            Item::create($data);
            $this->log(self::LOG_OTHER, '添加商品' . $params['material_code']);
        }

        return $this->sendData();
    }

    // 下载导入模板
    public function downloadTpl(Request $request)
    {
        $import = new Import('', 'items', true);

        return $import->downloadTpl();
    }

    // 商品基础数据
    public function import(Request $request)
    {
        if (!$request->hasFile('file')) return $this->sendData(402, '上传失败!');
        try {
            $path = $this->saveFile($request->file('file'));
            $import = new Import($path, 'items');
            $excelData = $import->getImportData();
            // 如果基础数据验证有错误，直接报错
            if ($excelData['errors'])  return $this->sendData(200, '', $excelData['errors']);
            DB::connection('labelDB')->beginTransaction();
            $count = 0;
            $errors = [];
            foreach ($excelData['data'] as $line => $item) {
                if ($item['label_usage'] < 0) {
                    $errors = $import->setError($errors, $line, '贴标数不能小于0');
                    continue;
                }
                if ($item['valid_month'] < 0) {
                    $errors = $import->setError($errors, $line, '有效期不能小于0');
                    continue;
                }
                if (!in_array($item['is_mark_valid'], ['是', '否'])) {
                    $errors = $import->setError($errors, $line, '是否查询制造记号值错误');
                    continue;
                } else {
                    $item['is_mark_valid'] = $item['is_mark_valid'] == '是' ? 0 : 1;
                }
                if (!in_array($item['is_stick_valid'], ['是', '否'])) {
                    $errors = $import->setError($errors, $line, '是否贴标值错误');
                    continue;
                } else {
                    $item['is_stick_valid'] = $item['is_stick_valid'] == '是' ? 0 : 1;
                }

                if ($item['case_num'] <= 0 || $item['box_num']<=0 || $item['support_num']<=0) {
                    $errors = $import->setError($errors, $line, '盒规、箱规和托规必须大于0');
                    continue;
                }

                $search = [
                    'material_code' => $item['material_code'],
                ];

                $itemData = Item::where($search)->first();
                if (!$itemData) {
                    Item::create($item);
                    $count++;
                } else {
                    Item::where($search)->update($item);
                }
            }
            if (!empty($errors)) $this->sendData(200, '', $errors);
            DB::connection('labelDB')->commit();
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            DB::connection('labelDB')->rollBack();
            return $this->sendData(402, $e->getMessage());
        }
        if ($count > 0) $this->log(self::LOG_IMPORT, '导入' . $count . '条商品基础数据');

        return $this->sendData(200, '', ['count' => $count]);
    }

    //上传图片
    public function upload(Request $request)
    {
        if (!$request->hasFile('file')) {
            return $this->sendData(200, '');
        }
        $file = $request->file('file');
        if ($file->isValid()) {
            $originalName = $file->getClientOriginalName(); // 文件原名
            $file->move('./uploads/',$originalName);
            $len = strlen($originalName);
            $name = substr($originalName,0,$len-4);
            $item = [
                'instruction' =>'是',
                'img' =>$originalName
            ];
            Item::where('material_code',$name)->update($item);
        }
        return $this->sendData();
    }


    public function exportBooks(Request $request){
        $id = $request->get('id');
        $pieces = explode(",", $id);
        $item = Item::whereIn('id', $pieces)->get();
        $image = [];
        foreach($item as $list){
                if(!empty($list->img)){
                    $image[] = $list->img;
                }
        }
        $result_01 = array_flip($image);
        $result  = array_keys($result_01);
        $name = date('YmdHis') . rand('1', '999');
        return Excel::create($name, function ($excel) use ($result) {
            $excel->sheet('score', function ($sheet) use ($result) {
                $sum = 1;
                foreach($result as $key =>$val){
                    $objDrawing = new PHPExcel_Worksheet_Drawing;
                    $objDrawing->setPath(public_path(). '/' .'uploads'. '/' . $val);
                    $objDrawing->setCoordinates('A'.$sum);
                    $objDrawing->setHeight(400);
                    $objDrawing->setWidth(300);
                    $objDrawing->setOffsetX(100); //写入图片在指定格中的X坐标值
                    $objDrawing->setOffsetY(-25); //写入图片在指定格中的Y坐标值
                    $objDrawing->setRotation(1); //设置旋转角度
                    $objDrawing->getShadow()->setVisible(true); //
                    $objDrawing->getShadow()->setDirection(50); //
                    $objDrawing->setWorksheet($sheet);
                    $sum += 20;
                }  
            });
        })->export('xls');
    }
}
