<?php

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

// 登录注册
Route::prefix('auth')->group(function () {
    Route::post('login', 'AuthController@login');
    Route::post('register', 'AuthController@register');
    Route::post('me', 'AuthController@me');
    Route::post('logout', 'AuthController@logout');
});

// 图片
Route::prefix('image')->group(function () {
    Route::get('directoryList', 'ImageController@directoryList');
    Route::get('getDirectoryById', 'ImageController@getDirectoryById');
    Route::get('getImagesByDirectoryId', 'ImageController@getImagesByDirectoryId');
});

//文章
Route::prefix('post')->group(function () {
    Route::post('index', 'PostController@index');
    Route::get('show/{id}', 'PostController@show');
    Route::get('getCategoryList', 'PostController@getCategoryList');
    Route::get('getTagList', 'PostController@getTagList');
    Route::post('getCommentList', 'PostController@getCommentList');
    Route::post('publishComment', 'PostController@publishComment');
});

// 后台路由，需要管理员身份
Route::middleware(['refresh.token', 'is.admin'])
    ->namespace('Admin')
    ->prefix('admin')
    ->group(base_path('routes/admin.php'));
