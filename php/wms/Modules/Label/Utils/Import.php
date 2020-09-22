<?php

namespace Modules\Label\Utils;

use Excel;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redis;
use PHPExcel_Shared_Date;
use Maatwebsite\Excel\Collections\SheetCollection;

class Import
{
    public $path = '';
    public $type = '';
    public $setting = [];
    public $isTemplate = false;

    public function __construct($path = '', $type, $isTemplate = false)
    {
        if (!$isTemplate) {
            if (!$path || !is_file($path)) {
                throw new \Exception('文件路径错误');
            }
        }
        
        $settings = Config::get('label.import');
        if (!isset($settings[$type])) {
            throw new \Exception('配置信息不存在');
        }
        $setting = [];
        foreach ($settings[$type] as $value) {
            $setting[$value['name']] = $value;
        }

        $this->path = $path;
        $this->type = $type;
        $this->setting = $setting;
        $this->isTemplate = $isTemplate;
        
    }

    // 导出模板
    public function downloadTpl()
    {
        return Excel::create($this->type, function($excel) {
            $excel->setTitle('库位导入模板');
            $excel->sheet($this->type, function($sheet) {
                $sheet->row(1, array_keys($this->setting));
                // 必填字段设置背景色
                $start = 65;
                foreach ($this->setting as $value) {
                    if ($value['required']) {
                        $sheet->cell(chr($start).'1', function($row) {
                            $row->setBackground('#FF0000');
                        });
                    }
                    $start++;
                }
            });
        })->export('xls');
    }

    // 获取导入信息并进行初步验证
    public function getImportData()
    {
        if ($this->isTemplate) throw new \Exception('初始化类型错误，无法使用该方法');
        $setting = $this->setting;
        $type = $this->type;

        $return = ['line' => 1, 'errors' => [], 'data' => []];
        Redis::set('Import_'.$this->path, json_encode($return));
        Excel::filter('chunk')->load($this->path)->chunk(1000, function ($sheets) use ($setting) {
            $redisData = json_decode(Redis::get('Import_'.$this->path), true);
            if (empty($sheets)) throw new \Exception('sheet不能为空');
            if ($sheets instanceof SheetCollection) {
                $sheet = $sheets[0];
            } else {
                $sheet = $sheets;
            }
            foreach ($sheet as $row) {
                $redisData['line'] += 1;
                foreach ($row as $k => $v) {
                    $v = trim((string)$v);
                    if (!isset($setting[$k])) continue;
                    if ($setting[$k]['required'] && !$v) {
                        $redisData['errors'] = $this->setError($redisData['errors'], $redisData['line'], $k . '不能为空');
                    }
                    if ($v) {
                        switch ($setting[$k]['type']) {
                            case 'number':
                                if (!is_numeric($v)) $redisData['errors'] = $this->setError($redisData['errors'], $redisData['line'], $k . '必须是一个数字');
                                break;
                            case 'date':
                                if (is_numeric($v)) {
                                    $v = gmdate("Y-m-d H:i:s", PHPExcel_Shared_Date::ExcelToPHP($v));
                                }
                                if (!strtotime($v)) $redisData['errors'] = $this->setError($redisData['errors'], $redisData['line'], $k . '必须是一个有效的日期');
                                break;
                            default:
                                break;
                        }
                    }
                    $redisData['data'][$redisData['line']][$setting[$k]['field']] = $v;
                }
            }
            Redis::set('Import_'.$this->path, json_encode($redisData));
        });
        
        $return = json_decode(Redis::get('Import_'.$this->path), true);
        Redis::del('Import_'.$this->path);
        if($type == 'invoices'){
            foreach($return['data'] as $da){
                if(count($da) != 22){
                    $return['errors']  = ['1'=>['line' => '1', 'message' => '导入失败，字段名称不正确']];
                }
            }
        }
        return $return;
    }

    //保存错误
    public function setError($errors, $line, $message)
    {
        if (isset($errors[$line])) {
            $errors[$line]['message'] .= ';' . $message;
        } else {
            $errors[$line] = ['line' => $line, 'message' => $message];
        }

        return $errors;
    }
}
