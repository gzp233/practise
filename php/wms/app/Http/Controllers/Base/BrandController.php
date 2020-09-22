<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2018/11/20
 * Time: 15:48
 */
namespace App\Http\Controllers\Base;

use App\Http\Controllers\Controller;
use DemeterChain\B;
use Illuminate\Http\Request;
use App\Models\Base\Brand;

class BrandController extends Controller
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
        if ($request->get('BRANDNM')) {
            $params[] = ['BRANDNM', 'like', '%' . $request->get('BRANDNM') . '%'];
        }
        $result = Brand::where($params)->orderBy($sort, 'desc')->paginate($limit);
        return sendData(200, '请求成功', $result);
    }
}