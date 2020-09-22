<?php

Route::group(['middleware' => 'auth:api', 'prefix' => 'api/label', 'namespace' => 'Modules\Label\Http\Controllers'], function () {
    // 标签
    Route::group(['prefix' => 'label'], function ($router) {
        $router->post('index', 'LabelController@index');
        $router->post('import', 'LabelController@import');
        $router->post('getById', 'LabelController@getById');
        $router->post('save', 'LabelController@save');
        $router->post('delete', 'LabelController@delete');
        $router->get('downloadTpl', 'LabelController@downloadTpl');
    });

    // 发票
    Route::group(['prefix' => 'invoice'], function ($router) {
        $router->post('index', 'InvoiceController@index');
        $router->post('unionIndex', 'InvoiceController@unionIndex');
        $router->post('unionIndexs', 'InvoiceController@unionIndexs');
        $router->post('import', 'InvoiceController@import');
        $router->post('getImportResult', 'InvoiceController@getImportResult');
        $router->post('getById', 'InvoiceController@getById');
        $router->post('getByIds', 'InvoiceController@getByIds');
        $router->post('save', 'InvoiceController@save');
        $router->post('delete', 'InvoiceController@delete');
        $router->get('downloadTpl', 'InvoiceController@downloadTpl');
        $router->post('importInspection', 'InvoiceController@importInspection');
        $router->post('updateCommodity', 'InvoiceController@updateCommodity');
    });

    // 库位管理
    Route::group(['prefix' => 'location'], function ($router) {
        $router->post('index', 'LocationController@index');
        $router->post('getById', 'LocationController@getById');
        $router->post('save', 'LocationController@save');
        $router->post('delete', 'LocationController@delete');
        $router->post('getLocationsByNo', 'LocationController@getLocationsByNo');
        $router->get('downloadTpl', 'LocationController@downloadTpl');
        $router->post('import', 'LocationController@import');
        $router->post('getSupportByLocation', 'LocationController@getSupportByLocation');
    });

    // 商品基础信息管理
    Route::group(['prefix' => 'item'], function ($router) {
        $router->post('index', 'ItemController@index');
        $router->post('getById', 'ItemController@getById');
        $router->post('save', 'ItemController@save');
        $router->get('downloadTpl', 'ItemController@downloadTpl');
        $router->get('exportBooks', 'ItemController@exportBooks');
        $router->post('import', 'ItemController@import');
        $router->post('upload', 'ItemController@upload');
    });

    // 标签库存管理
    Route::group(['prefix' => 'labelStock'], function ($router) {
        $router->post('index', 'LabelStockController@index');
        $router->post('stockList', 'LabelStockController@stockList');
        $router->post('getByNo', 'LabelStockController@getByNo');
        $router->post('stock', 'LabelStockController@stock');
        $router->post('freeze', 'LabelStockController@freeze');
        $router->post('unfreeze', 'LabelStockController@unfreeze');
        $router->post('abandonedIndex', 'LabelStockController@abandonedIndex');
        $router->post('abandonedList', 'LabelStockController@abandonedList');
        $router->post('abandonedSubmit', 'LabelStockController@abandonedSubmit');
    });

    // 商品库存管理
    Route::group(['prefix' => 'itemStock'], function ($router) {
        $router->post('index', 'ItemStockController@index');
        $router->post('stockList', 'ItemStockController@stockList');
        $router->post('getByNo', 'ItemStockController@getByNo');
        $router->post('getByIds', 'ItemStockController@getByIds');
        $router->post('stage', 'ItemStockController@stage');
        $router->post('stageIndex', 'ItemStockController@stageIndex');
        $router->post('stageList', 'ItemStockController@stageList');
        $router->post('stageSubmit', 'ItemStockController@stageSubmit');
    });

    // 查询任务
    Route::group(['prefix' => 'query'], function ($router) {
        $router->post('index', 'QueryController@index');
        $router->get('export', 'QueryController@export');
        $router->get('exports', 'QueryController@exports');
        $router->post('import', 'QueryController@import');
        $router->post('checkInvoices', 'QueryController@checkInvoices');
        $router->post('checkInvoice', 'QueryController@checkInvoice');
        $router->post('delete', 'QueryController@delete');
    });

    // 盖章任务
    Route::group(['prefix' => 'stamp'], function ($router) {
        $router->post('index', 'StampController@index');
        $router->post('getList', 'StampController@getList');
        $router->post('getLists', 'StampController@getLists');
        $router->post('create', 'StampController@create');
        $router->post('getByNo', 'StampController@getByNo');
        $router->post('complete', 'StampController@complete');
        $router->post('terminate', 'StampController@terminate');
        $router->get('export', 'StampController@export');
        $router->get('exportPicking', 'StampController@exportPicking');
        $router->get('exportBooks', 'StampController@exportBooks');
    });

    // 贴标任务
    Route::group(['prefix' => 'stick'], function ($router) {
        $router->post('index', 'StickController@index');
        $router->post('cartdel', 'StickController@cartdel');
        $router->post('getList', 'StickController@getList');
        $router->post('shopping', 'StickController@shopping');
        $router->post('getCartList', 'StickController@getCartList');
        $router->post('getLists', 'StickController@getLists');
        $router->post('create', 'StickController@create');
        $router->post('getByNo', 'StickController@getByNo');
        $router->post('complete', 'StickController@complete');
        $router->post('terminate', 'StickController@terminate');
        $router->post('pick', 'StickController@pick');
        $router->get('export', 'StickController@export');
        $router->get('exportBooks', 'StickController@exportBooks');
        $router->get('printItemPickOrder', 'StickController@printItemPickOrder');
        $router->get('printLabelPickOrder', 'StickController@printLabelPickOrder');
        $router->post('itemRollback', 'StickController@itemRollback');
        $router->post('labelRollback', 'StickController@labelRollback');
    });

    // 出库任务
    Route::group(['prefix' => 'outstorage'], function ($router) {
        $router->post('index', 'OutstorageController@index');
        $router->post('getList', 'OutstorageController@getList');
        $router->post('getLists', 'OutstorageController@getLists');
        $router->post('del', 'OutstorageController@del');
        $router->get('export', 'OutstorageController@export');
        $router->post('getByNo', 'OutstorageController@getByNo');
        $router->post('shopping', 'OutstorageController@shopping');
        $router->post('submit', 'OutstorageController@submit');
    });

    // 标签移库
    Route::group(['prefix' => 'labelMove'], function ($router) {
        $router->post('index', 'LabelMoveController@index');
        $router->post('getList', 'LabelMoveController@getList');
        $router->post('getByNo', 'LabelMoveController@getByNo');
        $router->post('submit', 'LabelMoveController@submit');
    });

    // 商品移库
    Route::group(['prefix' => 'itemMove'], function ($router) {
        $router->post('index', 'ItemMoveController@index');
        // $router->post('getList', 'ItemMoveController@getList');
        $router->post('getByNo', 'ItemMoveController@getByNo');
        // $router->post('submit', 'ItemMoveController@submit');
    });
});

Route::group(['middleware' => 'auth:api', 'prefix' => 'api/barcode', 'namespace' => 'Modules\Label\Http\Controllers\Barcode'], function () {
    // 查询
    Route::group(['prefix' => 'query'], function ($router) {
        $router->post('getById', 'QueryBarcodeController@getById');
        $router->post('upload', 'QueryBarcodeController@upload');
        $router->post('ocrWrite', 'QueryBarcodeController@ocrWrite');
        $router->post('ocrList', 'QueryBarcodeController@ocrList');
        $router->post('getByNo', 'QueryBarcodeController@getByNo');
        $router->post('del', 'QueryBarcodeController@del');
        $router->post('support', 'QueryBarcodeController@support');
        $router->post('supports', 'QueryBarcodeController@supports');
        $router->post('doSubmit', 'QueryBarcodeController@doSubmit');
        $router->post('ids', 'QueryBarcodeController@ids');
    });
    //扫码移库
    Route::group(['prefix' => 'move'], function ($router) {
        $router->post('getById', 'MoveController@getById');
        $router->post('getByNo', 'MoveController@getByNo');
        $router->post('getOriginStockList', 'MoveController@getOriginStockList');
        $router->post('submitOrigin', 'MoveController@submitOrigin');
        $router->post('getToStockList', 'MoveController@getToStockList');
        $router->post('getGoodsList', 'MoveController@getGoodsList');
        $router->post('stockTo', 'MoveController@stockTo');
        $router->post('submitTo', 'MoveController@submitTo');
        $router->post('createTask', 'MoveController@createTask');
        $router->post('submitWhole', 'MoveController@submitWhole');
        $router->post('submitPort', 'MoveController@submitPort');
        $router->post('getYikuNos', 'MoveController@getYikuNos');
    });
});
