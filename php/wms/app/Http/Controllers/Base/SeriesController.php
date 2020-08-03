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
use App\Models\Base\Series;

class SeriesController extends Controller
{
    public function __construct()
    {
        $this->middleware('refresh.token');
    }

    public function index(Request $request)
    {
        $limit = $request->get('limit') ? $request->get('limit') : 20;
        $sort = $request->get('sort') ? $request->get('sort') : 'DataYmd';
        $params = [];
        if ($request->get('SERIESNM')) {
            $params[] = ['SERIESNM', 'like', '%' . $request->get('SERIESNM') . '%'];
        }
        $result = Series::where($params)->orderBy($sort, 'desc')->paginate($limit);
        return sendData(200, '请求成功', $result);
    }
}