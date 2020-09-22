<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2018/11/20
 * Time: 15:48
 */
namespace App\Http\Controllers\Base;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Base\Product;
use App\Models\Base\Unit;
use Illuminate\Support\Facades\Redis;
use Excel;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('refresh.token');
    }

    public function index(Request $request)
    {
        $limit = $request->get('limit') ? $request->get('limit') : 20;
        $sort = $request->get('sort') ? $request->get('sort') : 'created_at';
        $params = [];
        if ($request->get('PRODCHINM')) $params[] = ['PRODCHINM', 'like', '%' . $request->get('PRODCHINM') . '%'];
        if ($request->get('NewPRODUCTCD')) $params[] = ['NewPRODUCTCD', 'like', '%' . $request->get('NewPRODUCTCD')];
        if ($request->get('PRODUCTCD')) $params[] = ['PRODUCTCD', 'like', '%' . $request->get('PRODUCTCD')];
        
        $result = Product::where($params)
        ->with(['company', 'prodflg', 'series', 'brand', 'units'])
        ->orderBy($sort, 'desc')
        ->paginate($limit);
        return sendData(200, '请求成功', $result);
    }

    public function toggleCode(Request $request)
    {
        $id = $request->get('id');
        if (!$id || !$product = Product::find($id)) {
            return sendData(402, '请求失败');
        }
        if ($product->is_need_code == '否') {
            $product->is_need_code = '是';
        } else {
            $product->is_need_code = '否';
        }
        if (!$product->save()) return sendData(402, '请求失败');

        return sendData();
    }

    public function edit(Request $request) {
        $id = $request->get('id');
        if (!$id || !$product = Product::with('units')->find($id)) {
            return sendData(402, '请求失败');
        }
        Redis::del('barcode::'.$product->barCode);
        $product->validity = $request->get('validity');
        $product->barCode = $request->get('barCode');
        if (!$product->save()) return sendData(402, '请求失败');
        Redis::set($product->PRODUCTCD, json_encode($product->toArray()));
        Redis::set('barcode::'.$product->barCode, json_encode($product->toArray()));
        return sendData();
    }

    public function upload(Request $request) {
        if (!$request->hasFile('file')) return sendData(402, '上传失败!');
        $file = $request->file('file');
        $path = storage_path().DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'product'.DIRECTORY_SEPARATOR.date('Ymd');
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        $filePath = $path.DIRECTORY_SEPARATOR.uniqid().'.'.$file->extension();
        move_uploaded_file($file->path(), $filePath);
    
        //开始导入逻辑
        $errors = [];
        $count = 0;
        Excel::load($filePath, function($reader) use (&$errors, &$count) {
            $data = $reader->all();
            $sheet = $data[0];
            $i = 0;
            foreach ($sheet as $row) {
                $i++;
                $NewPRODUCTCD = (string)$row['新产品代码'];
                $validity = $row['有效期'];
                $barCode = (string)$row['支码'];
                $hegui = $row['盒规'];
                $xianggui = $row['箱规'];
                $tuogui = $row['托规'];
                if (empty($NewPRODUCTCD) || !$product = Product::where('NewPRODUCTCD', $NewPRODUCTCD)->with('units')->first()) {
                    $errors[] = ['line' => $i, 'message' => '产品代码为空或不存在!'];
                    continue;
                }
                if ($validity) {
                    if ($validity <= 0 || $validity >= 1000) {
                        $errors[] = ['line' => $i, 'message' => '有效期必须大于0小于1000!'];
                        continue;
                    }
                    $product->validity = $validity;
                }

                if ($barCode) {
                    $product->barCode = $barCode;
                }
                if ($validity || $barCode) {
                    if (!$product->save()) {
                        $errors[] = ['line' => $i, 'message' => '有效期或支码导入失败!'];
                        continue;
                    }
                }
                //插入箱规
                $units = [];
                foreach ($product->units as $unit) {
                    $units[$unit->unit_name] = ['number' => $unit->number, 'id' => $unit->id];
                }

                if ($hegui) {
                    if (isset($units['盒'])) {
                        if ($hegui != $units['盒']['number']) {
                            if (!Unit::where('id', $units['盒']['id'])->update(['number' => $hegui])) {
                                $errors[] = ['line' => $i, 'message' => '盒规导入失败!'];
                                continue;
                            }
                        }
                    } else {
                        Unit::create(['unit_name' => '盒', 'number' => $hegui, 'product_id' => $product->id]);
                    }
                }

                if ($xianggui) {
                    if (isset($units['箱'])) {
                        if ($hegui != $units['箱']['number']) {
                            if (!Unit::where('id', $units['箱']['id'])->update(['number' => $xianggui])) {
                                $errors[] = ['line' => $i, 'message' => '箱规导入失败!'];
                                continue;
                            }
                        }
                    } else {
                        Unit::create(['unit_name' => '箱', 'number' => $xianggui, 'product_id' => $product->id]);
                    }
                }

                if ($tuogui) {
                    if (isset($units['托'])) {
                        if ($hegui != $units['托']['number']) {
                            if (!Unit::where('id', $units['托']['id'])->update(['number' => $tuogui])) {
                                $errors[] = ['line' => $i, 'message' => '托规导入失败!'];
                                continue;
                            }
                        }
                    } else {
                        Unit::create(['unit_name' => '托', 'number' => $tuogui, 'product_id' => $product->id]);
                    }
                }
                $count++;
            }
        });
        
        return sendData(200, '成功'.$count.'条', $errors);
    }
}