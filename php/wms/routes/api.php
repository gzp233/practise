<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'auth', 'namespace' => 'Auth'], function ($router) {
    $router->post('login', 'AuthController@login');
    $router->post('logout', 'AuthController@logout');
    $router->post('me', 'AuthController@me');
});

Route::group(['prefix' => 'user', 'namespace' => 'Auth'], function ($router) {
    $router->post('index', 'UserController@index');
    $router->post('create', 'UserController@create');
    $router->post('update', 'UserController@update');
    $router->post('delete', 'UserController@delete');
    $router->post('getById', 'UserController@getById');
    $router->post('getList', 'UserController@getList');
});

$router->group(['prefix' => 'role', 'namespace' => 'Auth'], function ($router) {
    $router->post('index', 'RoleController@index');
    $router->post('getList', 'RoleController@getList');
    $router->post('getById', 'RoleController@getById');
    $router->post('create', 'RoleController@create');
    $router->post('update', 'RoleController@update');
    $router->post('delete', 'RoleController@delete');
    $router->post('getPermissions', 'RoleController@getPermissions');
    $router->post('changePermission', 'RoleController@changePermission');
});

Route::group(['prefix' => 'permission', 'namespace' => 'Auth'], function ($router) {
    $router->post('index', 'PermissionController@index');
    $router->post('create', 'PermissionController@create');
    $router->post('update', 'PermissionController@update');
    $router->post('delete', 'PermissionController@delete');
    $router->post('getById', 'PermissionController@getById');
    $router->post('getList', 'PermissionController@getList');
    $router->post('getTree', 'PermissionController@getTree');
});

// 基础设置-仓库
$router->group(['prefix' => 'warehourse', 'namespace' => 'Basic'], function ($router) {
    $router->post('index', 'WarehourseController@index');
    $router->post('save', 'WarehourseController@save');
    $router->post('del', 'WarehourseController@del');
    $router->post('getList', 'WarehourseController@getList');
    $router->post('getAll', 'WarehourseController@getAll');
});
// 基础设置-库位
$router->group(['prefix' => 'location', 'namespace' => 'Basic'], function ($router) {
    $router->any('index', 'LocationController@index');
    $router->any('save', 'LocationController@save');
    $router->any('del', 'LocationController@del');
    $router->any('getById', 'LocationController@getById');
    $router->post('getLocations', 'LocationController@getLocationsByAreaIds');
    $router->post('getLocationsAll', 'LocationController@getLocationsAll');
    $router->post('stockOut', 'LocationController@stockOut');
});

// 基础设置-库区
$router->group(['prefix' => 'area', 'namespace' => 'Basic'], function ($router) {
    $router->any('index', 'AreaController@index');
    $router->any('save', 'AreaController@save');
    $router->any('del', 'AreaController@del');
    $router->any('getById', 'AreaController@getById');
    $router->any('getAll', 'AreaController@getAll');
});
// 基础设置-商品单位
$router->group(['prefix' => 'unit', 'namespace' => 'Base'], function ($router) {
    $router->any('create', 'UnitController@create');
    $router->any('update', 'UnitController@update');
    $router->any('del', 'UnitController@del');
});
// 基础设置-商品属性
$router->group(['prefix' => 'attributes', 'namespace' => 'Basic'], function ($router) {
    $router->any('index', 'AttributesController@index');
    $router->any('save', 'AttributesController@save');
    $router->any('del', 'AttributesController@del');
    $router->post('getList', 'AttributesController@getList');
});
// 主数据

$router->group(['prefix' => 'test'], function ($router) {
    $router->any('scan', 'Base\TestController@scanForTXT');
    $router->any('sync', 'Base\TestController@sync');
});

//产品列表
$router->group(['prefix' => 'product', 'namespace' => 'Base'], function ($router) {
    $router->any('index', 'ProductController@index');
    $router->any('edit', 'ProductController@edit');
    $router->any('upload', 'ProductController@upload');
    $router->any('toggleCode', 'ProductController@toggleCode');
});

//公司列表
$router->group(['prefix' => 'company'], function ($router) {
    $router->any('index', 'Base\CompanyController@index');
});

//客戶列表
$router->group(['prefix' => 'customer'], function ($router) {
    $router->any('index', 'Base\CustomerController@index');
});

//送货地列表
$router->group(['prefix' => 'deliver'], function ($router) {
    $router->any('index', 'Base\DeliverController@index');
});

//品牌列表
$router->group(['prefix' => 'brand'], function ($router) {
    $router->any('index', 'Base\BrandController@index');
});

//产品区分列表
$router->group(['prefix' => 'flg'], function ($router) {
    $router->any('index', 'Base\FlgController@index');
});

//订单原因列表
$router->group(['prefix' => 'reason'], function ($router) {
    $router->any('index', 'Base\ReasonController@index');
});

//订单原因列表
$router->group(['prefix' => 'rejected'], function ($router) {
    $router->any('index', 'Base\RejectedController@index');
});

//产品系列列表
$router->group(['prefix' => 'series'], function ($router) {
    $router->any('index', 'Base\SeriesController@index');
});

//供应商列表
$router->group(['prefix' => 'vendor'], function ($router) {
    $router->any('index', 'Base\VendorController@index');
});

// 退货入库
Route::group(['prefix' => 'sales_return_instorage', 'namespace' => 'Instorage'], function ($router) {
    $router->post('index', 'SalesReturnController@index');
    $router->post('getById', 'SalesReturnController@getById');
    $router->post('stockIn', 'SalesReturnController@stockIn');
    $router->post('confirmRe', 'SalesReturnController@confirmRe');
    $router->post('hasStocked', 'SalesReturnController@hasStocked');
    $router->get('exportDoc', 'SalesReturnController@exportDoc');
    $router->get('export', 'SalesReturnController@export');
});

// 入库前加工
Route::group(['prefix' => 'instorage_process', 'namespace' => 'Instorage'], function ($router) {
    $router->post('index', 'InstorageProcessController@index');
    $router->post('move', 'InstorageProcessController@move');
    $router->post('getAllProducts', 'InstorageProcessController@getAllProducts');
    $router->post('getAllGoods', 'InstorageProcessController@getAllGoods');
    $router->post('getGoodsByIds', 'InstorageProcessController@getGoodsByIds');
    $router->post('stockIn', 'InstorageProcessController@stockIn');
    $router->post('getDetailById', 'InstorageProcessController@getDetailById');
});

//采购入库
Route::group(['prefix' => 'procurement_storage', 'namespace' => 'Instorage'], function ($router) {
    $router->post('index', 'ProcurementStorageController@index');
    $router->post('getById', 'ProcurementStorageController@getById');
    $router->post('getByNo', 'ProcurementStorageController@getByNo');
    $router->post('stockIn', 'ProcurementStorageController@stockIn');
    $router->get('exportDoc', 'ProcurementStorageController@exportDoc');
    $router->post('confirmRe', 'ProcurementStorageController@confirmRe');
    $router->post('hasStocked', 'ProcurementStorageController@hasStocked');
    $router->get('export', 'ProcurementStorageController@export');
});

//内向交货入库
Route::group(['prefix' => 'inner_storage', 'namespace' => 'Instorage'], function ($router) {
    $router->post('index', 'InnerStorageController@index');
    $router->post('getById', 'InnerStorageController@getById');
    $router->post('stockIn', 'InnerStorageController@stockIn');
    $router->get('exportDoc', 'InnerStorageController@exportDoc');
    $router->post('confirmRe', 'InnerStorageController@confirmRe');
    $router->post('hasStocked', 'InnerStorageController@hasStocked');
    $router->get('export', 'InnerStorageController@export');
});

//移动入库
Route::group(['prefix' => 'movement_storage', 'namespace' => 'Instorage'], function ($router) {
    $router->post('index', 'MovementStorageController@index');
    $router->post('getById', 'MovementStorageController@getById');
    $router->post('stockIn', 'MovementStorageController@stockIn');
    $router->post('confirmRe', 'MovementStorageController@confirmRe');
    $router->get('exportDoc', 'MovementStorageController@exportDoc');
    $router->get('export', 'MovementStorageController@export');
    $router->post('hasStocked', 'MovementStorageController@hasStocked');
});

//在库调整(入库)
Route::group(['prefix' => 'adjust_in', 'namespace' => 'Instorage'], function ($router) {
    $router->post('index', 'AdjustController@index');
    $router->post('getById', 'AdjustController@getById');
    $router->post('stockIn', 'AdjustController@stockIn');
    $router->post('confirmRe', 'AdjustController@confirmRe');
    $router->get('exportDoc', 'AdjustController@exportDoc');
    $router->get('export', 'AdjustController@export');
    $router->post('hasStocked', 'AdjustController@hasStocked');
});

//商品库存
Route::group(['prefix' => 'goods', 'namespace' => 'Storage'], function ($router) {
    $router->post('index', 'GoodsController@index');
    $router->post('unfreezeIndex', 'GoodsController@unfreezeIndex');
    $router->post('goodsList', 'GoodsController@goodsList');
    $router->post('goodsList2', 'GoodsController@goodsList2');
    $router->post('getGoodsByProductId', 'GoodsController@getGoodsByProductId');
    $router->post('getByNo', 'GoodsController@getByNo');
    $router->post('relieve', 'GoodsController@relieve');
    $router->get('export', 'GoodsController@export');
    $router->post('getAllGoods', 'GoodsController@getAllGoods');
    $router->post('getDetailById', 'GoodsController@getDetailById');
    $router->post('getAllProducts', 'GoodsController@getAllProducts');
    $router->post('getById', 'GoodsController@getById');
    $router->any('unfreeze', 'GoodsController@unfreeze');

    // 修改库存
    $router->post('modify', 'GoodsController@modify');
    $router->get('modifyList', 'GoodsController@modifyList');
});
//库内移动
Route::group(['prefix' => 'move_stock', 'namespace' => 'Storage'], function ($router) {
    $router->post('getGoodsByIds', 'MoveStockController@getGoodsByIds');
    $router->post('stockIn', 'MoveStockController@stockIn');
    $router->get('exportDoc', 'MoveStockController@exportDoc');
});
Route::group(['prefix' => 'statistics', 'namespace' => 'Storage'], function ($router) {
    $router->post('enter', 'StatisticsController@enter');
    $router->get('export', 'StatisticsController@export');
    $router->get('exports', 'StatisticsController@exports');
    $router->post('appear', 'StatisticsController@appear');
});
//库内移动SAP回传
Route::group(['prefix' => 'move_rolls', 'namespace' => 'Storage'], function ($router) {
    $router->post('index', 'MoveRollsController@index');
    $router->post('getGoodsByIds', 'MoveRollsController@getGoodsByIds');
    $router->post('stockIn', 'MoveRollsController@stockIn');
    $router->get('exportDoc', 'MoveRollsController@exportDoc');
});

// 受注出库
Route::group(['prefix' => 'sales_out', 'namespace' => 'Outstorage'], function ($router) {
    $router->post('index', 'SalesOutController@index');
    $router->post('getByNo', 'SalesOutController@getByNo');
    $router->post('stockOut', 'SalesOutController@stockOut');
    $router->post('backNo', 'SalesOutController@backNo');
    $router->get('exportDoc', 'SalesOutController@exportDoc');
    $router->get('exportPDF', 'SalesOutController@exportPDF');
    $router->get('accept', 'SalesOutController@accept');
    $router->post('getBySee', 'SalesOutController@getBySee');
    $router->post('getByOut', 'SalesOutController@getByOut');
    $router->post('rollback', 'SalesOutController@rollback');
    $router->any('wave', 'SalesOutController@wave');
    $router->any('export', 'SalesOutController@export');
    $router->get('binning', 'SalesOutController@binning');
    $router->get('downloadPX', 'SalesOutController@downloadPX');
    $router->post('pxList', 'SalesOutController@pxList');
    $router->get('downloadPXHZ', 'SalesOutController@downloadPXHZ');
});

// 移动出库
Route::group(['prefix' => 'move_out', 'namespace' => 'Outstorage'], function ($router) {
    $router->post('index', 'MoveOutController@index');
    $router->post('getStockById', 'MoveOutController@getStockById');
    $router->post('getByNo', 'MoveOutController@getByNo');
    $router->post('backNo', 'MoveOutController@backNo');
    $router->post('stockOut', 'MoveOutController@stockOut');
    $router->get('exportDoc', 'MoveOutController@exportDoc');
    $router->get('exportPDF', 'MoveOutController@exportPDF');
    $router->get('accept', 'MoveOutController@accept');
    $router->post('getBySee', 'MoveOutController@getBySee');
    $router->post('rollback', 'MoveOutController@rollback');
    $router->get('export', 'MoveOutController@export');
    $router->get('binning', 'MoveOutController@binning');
    $router->post('pxList', 'MoveOutController@pxList');
    $router->get('downloadPX', 'MoveOutController@downloadPX');
    $router->get('downloadPXHZ', 'MoveOutController@downloadPXHZ');
});
// 集货
Route::group(['prefix' => 'consolidation', 'namespace' => 'Outstorage'], function ($router) {
    $router->post('index', 'ConsolidationController@index');
    $router->get('wave', 'ConsolidationController@wave');
    $router->post('getByNo', 'ConsolidationController@getByNo');
    $router->post('getList', 'ConsolidationController@getList');
    $router->post('show', 'ConsolidationController@show');
    $router->post('del', 'ConsolidationController@del');
});
// 预拣配出库
Route::group(['prefix' => 'picking', 'namespace' => 'Outstorage'], function ($router) {
    $router->post('index', 'PickingController@index');
    $router->post('getStockById', 'PickingController@getStockById');
    $router->post('getByNos', 'PickingController@getByNos');
    $router->post('stockOut', 'PickingController@stockOut');
});
// 在库调整
Route::group(['prefix' => 'adjust', 'namespace' => 'Outstorage'], function ($router) {
    $router->post('index', 'AdjustController@index');
    $router->post('getStockById', 'AdjustController@getStockById');
    $router->post('getByNo', 'AdjustController@getByNo');
    $router->post('stockOut', 'AdjustController@stockOut');
    $router->post('backNo', 'AdjustController@backNo');
    $router->get('exportDoc', 'AdjustController@exportDoc');
    $router->get('exportPDF', 'AdjustController@exportPDF');
    $router->get('accept', 'AdjustController@accept');
    $router->post('getBySee', 'AdjustController@getBySee');
    $router->post('rollback', 'AdjustController@rollback');
    $router->any('wave', 'AdjustController@wave');
    $router->get('export', 'AdjustController@export');
    $router->get('binning', 'AdjustController@binning');
    $router->post('pxList', 'AdjustController@pxList');
    $router->get('downloadPX', 'AdjustController@downloadPX');
    $router->get('downloadPXHZ', 'AdjustController@downloadPXHZ');
});

// 接受出库确认
Route::group(['prefix' => 'out_ensure', 'namespace' => 'Outstorage'], function ($router) {
    $router->post('index', 'OrdOutEnsureController@index');
    $router->post('getAllProducts', 'OrdOutEnsureController@getAllProducts');
    $router->post('generate', 'OrdOutEnsureController@generate');
    $router->post('getByNo', 'OrdOutEnsureController@getByNo');
    $router->post('moveByNo', 'OrdOutEnsureController@moveByNo');
    $router->post('adjustNo', 'OrdOutEnsureController@adjustNo');
    $router->post('show', 'OrdOutEnsureController@show');
});

// 防串货
Route::group(['prefix' => 'anticode', 'namespace' => 'Outstorage'], function ($router) {
    $router->post('index', 'AntiCodeController@index');
    $router->post('sendCode', 'AntiCodeController@sendCode');
    $router->get('export', 'AntiCodeController@export');
    $router->post('del', 'AntiCodeController@del');
});
//盘点
Route::group(['prefix' => 'check', 'namespace' => 'Storage'], function ($router) {
    $router->post('index', 'CheckController@index');
    $router->post('unfreezeIndex', 'CheckController@unfreezeIndex');
    $router->post('goodsList', 'CheckController@goodsList');
    $router->post('shoppingList', 'CheckController@shoppingList');
    $router->post('getByNo', 'CheckController@getByNo');
    $router->post('relieve', 'CheckController@relieve');
    $router->get('export', 'CheckController@export');
    $router->post('getAllGoods', 'CheckController@getAllGoods');
    $router->post('getDetailById', 'CheckController@getDetailById');
    $router->post('getDetailByIds', 'CheckController@getDetailByIds');
    $router->post('separate', 'CheckController@separate');
    $router->post('shopping', 'CheckController@shopping');
    $router->post('shoppingIndex', 'CheckController@shoppingIndex');
    $router->post('getAllProducts', 'CheckController@getAllProducts');
    $router->post('getById', 'CheckController@getById');
    $router->any('unfreeze', 'CheckController@unfreeze');
    $router->post('getGoodsByProductId', 'CheckController@getGoodsByProductId');
    $router->post('del', 'CheckController@del');
    $router->get('exportDiff', 'CheckController@exportDiff');
    $router->get('verify', 'CheckController@verify');
    $router->get('getExport', 'CheckController@getExport');
    $router->post('batches', 'CheckController@batches');
});
//异动盘点
Route::group(['prefix' => 'diff_check', 'namespace' => 'Storage'], function ($router) {
    $router->post('index', 'DiffCheckController@index');
    $router->post('unfreezeIndex', 'DiffCheckController@unfreezeIndex');
    $router->post('goodsList', 'DiffCheckController@goodsList');
    $router->post('getByNo', 'DiffCheckController@getByNo');
    $router->post('relieve', 'DiffCheckController@relieve');
    $router->get('export', 'DiffCheckController@export');
    $router->post('getAllGoods', 'DiffCheckController@getAllGoods');
    $router->post('getDetailById', 'DiffCheckController@getDetailById');
    $router->post('getAllProducts', 'DiffCheckController@getAllProducts');
    $router->post('getById', 'DiffCheckController@getById');
    $router->any('unfreeze', 'DiffCheckController@unfreeze');
    $router->post('getGoodsByProductId', 'DiffCheckController@getGoodsByProductId');
    $router->get('exportDiff', 'DiffCheckController@exportDiff');
    $router->get('verify', 'DiffCheckController@verify');
    $router->get('getExport', 'DiffCheckController@getExport');
    $router->post('shopping', 'DiffCheckController@shopping');
    $router->post('shoppingList', 'DiffCheckController@shoppingList');
    $router->post('separate', 'DiffCheckController@separate');
    $router->post('batches', 'DiffCheckController@batches');
    $router->post('del', 'DiffCheckController@del');
    $router->post('separate', 'DiffCheckController@separate');
});
//扫码
Route::group(['prefix' => 'barcode', 'namespace' => 'Barcode'], function ($router) {
    $router->post('getErrors', 'BarcodeController@getErrors');
    $router->post('delCode', 'BarcodeController@delCode');
    $router->post('saveCode', 'BarcodeController@saveCode');
    $router->post('getBarCode', 'BarcodeController@getBarCode');
    $router->post('stockOut', 'BarcodeController@stockOut');
    $router->post('getGoods', 'BarcodeController@getGoods');
    $router->post('getFuheOrderByNo', 'BarcodeController@getFuheOrderByNo');
    $router->post('checkJanhuoOrder', 'BarcodeController@checkJanhuoOrder');
    $router->post('getJianhuoStockList', 'BarcodeController@getJianhuoStockList');
    $router->post('getJianhuoStock', 'BarcodeController@getJianhuoStock');
    $router->post('doJianhuoStock', 'BarcodeController@doJianhuoStock');
    $router->post('doJianhuo', 'BarcodeController@doJianhuo');
    $router->post('getProduct', 'BarcodeController@getProduct');
});
//扫码集货
Route::group(['prefix' => 'jihuo', 'namespace' => 'Barcode'], function ($router) {
    $router->post('getByNo', 'JihuoController@getByNo');
    $router->post('getJihuoStockList', 'JihuoController@getJihuoStockList');
    $router->post('getJihuoStock', 'JihuoController@getJihuoStock');
    $router->post('doJihuoStock', 'JihuoController@doJihuoStock');
    $router->post('doJihuo', 'JihuoController@doJihuo');
});
//播种
Route::group(['prefix' => 'bozhong', 'namespace' => 'Barcode'], function ($router) {
    $router->post('getByNo', 'BozhongController@getByNo');
    $router->post('getBozhongStockList', 'BozhongController@getBozhongStockList');
    $router->post('getProduct', 'BozhongController@getProduct');
    $router->post('getbozhongStock', 'BozhongController@getbozhongStock');
    $router->post('doBozhongStock', 'BozhongController@doBozhongStock');
    $router->post('doBozhong', 'BozhongController@doBozhong');
});

// 导入模板下载
$router->group(['prefix' => 'template', 'namespace' => 'Base'], function ($router) {
    $router->get('download', 'TemplateController@download');
});

// 移库
$router->group(['prefix' => 'yiku', 'namespace' => 'Storage'], function ($router) {
    $router->post('index', 'YikuController@index');
    $router->post('cartList', 'YikuController@cartList');
    $router->post('addCart', 'YikuController@addCart');
    $router->post('delCart', 'YikuController@delCart');
    $router->post('submitCart', 'YikuController@submitCart');
    $router->get('export', 'YikuController@export');
});
// OCR
$router->group(['prefix' => 'ocr', 'namespace' => 'Barcode'], function ($router) {
    //    $router->post('ocr', 'OcrController@ocr');
    $router->post('upload', 'OcrController@upload');
});

//移库WAP
Route::group(['prefix' => 'yiku_wap', 'namespace' => 'Barcode'], function ($router) {
    $router->post('getYikuNos', 'YikuController@getYikuNos');
    $router->post('getStockListByNo', 'YikuController@getStockListByNo');
    $router->post('getStock', 'YikuController@getStock');
    $router->post('doStock', 'YikuController@doStock');
    $router->post('submit', 'YikuController@submit');

    $router->post('createTask', 'YikuController@createTask');
    $router->post('getOriginStockList', 'YikuController@getOriginStockList');
    $router->post('getOriginByStockNo', 'YikuController@getOriginByStockNo');
    $router->post('stockOrigin', 'YikuController@stockOrigin');
    $router->post('submitOrigin', 'YikuController@submitOrigin');
    $router->post('getGoodsList', 'YikuController@getGoodsList');
    $router->post('getOriginGoodsList', 'YikuController@getOriginGoodsList');
    $router->post('getToStockList', 'YikuController@getToStockList');
    $router->post('getToByStockNo', 'YikuController@getToByStockNo');
    $router->post('stockTo', 'YikuController@stockTo');
    $router->post('submitTo', 'YikuController@submitTo');
});
//盘点
Route::group(['prefix' => 'pandian', 'namespace' => 'Barcode'], function ($router) {
    $router->post('checkPandianOrder', 'PandianController@checkPandianOrder');
    $router->post('getPandianStockList', 'PandianController@getPandianStockList');
    $router->post('getPandianStock', 'PandianController@getPandianStock');
    $router->post('barCode', 'PandianController@barCode');
    $router->post('doPandianStock', 'PandianController@doPandianStock');
    $router->post('doPandian', 'PandianController@doPandian');
});

//移库WAP
Route::group(['prefix' => 'fangchuanhuo', 'namespace' => 'Barcode'], function ($router) {
    $router->post('index', 'FangchuanhuoController@index');
    $router->post('checkFangchuanhuoOrder', 'FangchuanhuoController@checkFangchuanhuoOrder');
    $router->post('getByOrderNo', 'FangchuanhuoController@getByOrderNo');
    $router->post('submit', 'FangchuanhuoController@submit');
    $router->post('setRedis', 'FangchuanhuoController@setRedis');
});
