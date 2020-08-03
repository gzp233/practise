<?php

namespace Modules\Label\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Label\Models\Location;
use Modules\Label\Models\LabelStock;
use Modules\Label\Models\ItemStock;
use Illuminate\Support\Facades\Log;
use Modules\Label\Utils\Import;
use Illuminate\Support\Facades\DB;

class LocationController extends BaseController
{
    // 分页获取库位
    public function index(Request $request)
    {
        $limit = $request->get('limit') ? $request->get('limit') : 20;
        $sort = $request->get('sort') ? $request->get('sort') : 'updated_at';
        $where = [];
        if ($request->get('location_no')) $where[] = ['location_no', '=', $request->get('location_no')];
        if ($request->get('type') === '0' || $request->get('type') == 1) {
            $where[] = ['type', '=', $request->get('type')];
        }
        if ($request->get('type') == 1 && ($request->get('label_type') === '0' || in_array($request->get('label_type'), [1, 2, 3, 4]))) {
            $where[] = ['label_type', '=', $request->get('label_type')];
        }
        $locations = Location::where($where)->orderBy($sort, 'desc')->paginate($limit);

        return $this->sendData(200, '', $locations);
    }

    //根据ID获取一条数据
    public function getById(Request $request)
    {
        $id = $request->get('id');
        if (!$id) return $this->sendData(402, 'ID不能为空');
        return $this->sendData(200, '', Location::find($id));
    }


    //修改库位
    public function save(Request $request)
    {
        $params = $request->all();
        if (!isset($params['type']) || !in_array($params['type'], [0, 1])) {
            return $this->sendData(402, '类型错误');
        }
        if ($params['type'] == 1 && (!isset($params['label_type']) || !in_array($params['type'], [0, 1]))) {
            return $this->sendData(402, '标签类型错误');
        }
        if (!isset($params['location_no']) || !trim($params['location_no'])) {
            return $this->sendData(402, '库位号不能为空');
        }
        $params['location_no'] = trim($params['location_no']);
        $exist = Location::where(['type' => $params['type'], 'location_no' => $params['location_no']])->first();
        if (isset($params['id']) && $location = Location::find($params['id'])) {
            if ($exist && $exist->id != $params['id']) return $this->sendData(402, '库位号不能重复');
            $location->type = $params['type'];
            $location->location_no = $params['location_no'];
            $location->label_type = $params['type'] == 1 ? $params['label_type'] : 0;
            $location->save();
            $this->log(self::LOG_OTHER, '修改库位' . $params['location_no']);
        } else {
            if ($exist) return $this->sendData(402, '库位号不能重复');
            Location::create([
                'type' => $params['type'],
                'location_no' => $params['location_no'],
                'label_type' => $params['type'] == 1 ? $params['label_type'] : 0
            ]);
            $this->log(self::LOG_OTHER, '添加库位' . $params['location_no']);
        }

        return $this->sendData();
    }

    // 删除库位
    public function delete(Request $request)
    {
        $ids = $request->get('ids');
        if (!$ids) return $this->sendData(402, 'ID不能为空');
        if (!is_array($ids)) $ids = explode(',', $ids);
        $locations = Location::whereIn('id', $ids)->get();
        if (empty($locations)) return $this->sendData(402, '未找到库位，请刷新页面重试');

        foreach ($locations as $location) {
            if ($location->type == 0) {
                $count = ItemStock::where('location_no', $location->location_no)->count();
                if ($count > 0) return $this->sendData(402, $location->location_no . '库位仍在使用，无法删除');
            } else {
                $count = LabelStock::where('location_no', $location->location_no)->count();
                if ($count > 0) return $this->sendData(402, $location->location_no . '库位仍在使用，无法删除');
            }
        }

        try {
            Location::whereIn('id', $ids)->delete();
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            return $this->sendData(402, '删除失败');
        }
        $this->log(self::LOG_OTHER, "删除库位ID为" . implode(',', $ids));

        return $this->sendData();
    }

    // 根据标签名模糊查询库位号
    public function getLocationsByNo(Request $request)
    {
        $type = $request->get('type');
        $label_type = $request->get('label_type');
        $query = trim($request->get('query'));
        $limit = $request->get('limit') ? $request->get('limit') : 20;
        if ($type !== '0' && $type != 1) return $this->sendData(402, 'type不能为空');
        if (!$query) return $this->sendData(200, '', []);

        $search = [
            ['type', '=', $type],
            ['location_no', 'like', $query . '%'],
        ];
        if ($type == 1 && $label_type === null) return $this->sendData(402, '标签库位类型必选');
        if ($type == 1 && $label_type !== null) {
            $search['label_type'] = $label_type;
        }
        $locations = Location::where($search)->limit($limit)->get();
        if ($type == 0) {
            foreach ($locations as $location) {
                $stock = ItemStock::where('location_no', $location->location_no)->first();
                if ($stock) $location->support_no = $stock->support_no;
            }
        }

        return $this->sendData(200, '', $locations);
    }

    // 下载导入模板
    public function downloadTpl(Request $request)
    {
        $import = new Import('', 'location', true);

        return $import->downloadTpl();
    }

    // 根据库位号获取托号
    public function getSupportByLocation(Request $request)
    {
        $location_no = $request->get('location_no');
        if (!$location_no) return $this->sendData(402, '库位号不能为空');
        $stock = ItemStock::where('location_no', $location_no)->first();
        $support_no = '';
        if ($stock) $support_no = $stock->support_no;

        return $this->sendData(200, '', $support_no);
    }

    // 库位导入
    public function import(Request $request)
    {
        if (!$request->hasFile('file')) return $this->sendData(402, '上传失败!');
        try {
            $path = $this->saveFile($request->file('file'));
            $import = new Import($path, 'location');
            $excelData = $import->getImportData();
            // 如果基础数据验证有错误，直接报错
            DB::connection('labelDB')->beginTransaction();
            if ($excelData['errors'])  return $this->sendData(200, '', $excelData['errors']);
            $count = 0;
            $errors = [];
            foreach ($excelData['data'] as $line => $item) {
                if ($item['type'] == '标签') {
                    $item['type'] = 1;
                } elseif ($item['type'] == '商品') {
                    $item['type'] = 0;
                } else {
                    $errors = $import->setError($errors, $line, '库位类型错误');
                    continue;
                }
                if ($item['type'] == 0) {
                    $item['label_type'] = 0;
                } else {
                    switch ($item['label_type']) {
                        case '未盖章':
                            $item['label_type'] = 0;
                            break;
                        case '已盖章':
                            $item['label_type'] = 1;
                            break;
                        case '盖章报废':
                            $item['label_type'] = 2;
                            break;
                        case '贴标报废':
                            $item['label_type'] = 3;
                            break;
                        default:
                            $errors = $import->setError($errors, $line, '标签库位类型错误');
                            continue 2;
                    }
                }
                $search = [
                    'location_no' => $item['location_no'],
                    'type' => $item['type']
                ];
                $location = Location::where($search)->first();
                if (!$location) {
                    if (empty($errors)) {
                        Location::create($item);
                        $count++;
                    }
                } else {
                    if ($location->label_type != $item['label_type']) {
                        $errors = $import->setError($errors, $line, '存在其他标签类型');
                    }
                }
            }
            if (!empty($errors)) return $this->sendData(200, '', $errors);
            DB::connection('labelDB')->commit();
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            DB::connection('labelDB')->rollBack();
            return $this->sendData(402, $e->getMessage());
        }
        if ($count > 0) $this->log(self::LOG_IMPORT, '导入' . $count . '条库位数据');

        return $this->sendData(200, '', ['count' => $count]);
    }
}
