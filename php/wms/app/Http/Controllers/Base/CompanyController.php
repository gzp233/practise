<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2018/11/20
 * Time: 16:51
 */
namespace App\Http\Controllers\Base;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Base\Company;

class CompanyController extends Controller
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
        if ($request->get('CompanyNm')) {
            $params[] = ['CompanyNm', 'like', '%' . $request->get('CompanyNm') . '%'];
        }
        $result = Company::where($params)->orderBy($sort, 'desc')->paginate($limit);;
        return sendData(200, '请求成功', $result);
    }
}
