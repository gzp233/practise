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

class TemplateController extends Controller
{
    private $map = [];
    private $prefix = '';

    public function __construct()
    {
        $this->middleware('refresh.token');
        $this->prefix = public_path() . DIRECTORY_SEPARATOR.'template'.DIRECTORY_SEPARATOR;
        $this->map = [
            'product' => 'product.xlsx',
        ];
    }

    public function download(Request $request) {
        $key = $request->get('key');
        if (!$key || !isset($this->map[$key])) return 'key错误！';
        $filename = $this->prefix.$this->map[$key];
        if (!file_exists($filename)) return '模板不存在！';

        return response()->download($filename);
    }
}