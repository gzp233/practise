<?php

// 用户
Route::resource('user', 'UserController')->only(['index', 'destroy']);
Route::prefix('user')->group(function () {
    Route::post('modifyPassword', 'UserController@modifyPassword');
});

// 相册
Route::resource('imageDirectory', 'ImageDirectoryController')->except(['create', 'edit']);
Route::prefix('image')->group(function () {
    Route::get('getImagesByDirectoryId', 'ImageController@getImagesByDirectoryId');
    Route::post('upload', 'ImageController@upload');
    Route::post('deleteByIds', 'ImageController@deleteByIds');
});

// 文章
Route::resource('postCategory', 'PostCategoryController')->only(['index', 'store', 'update', 'destroy']);
Route::prefix('postCategory')->group(function () {
    Route::get('getAll', 'PostCategoryController@getAll');
});
Route::resource('postTag', 'PostTagController')->only(['index', 'store', 'update', 'destroy']);
Route::prefix('postTag')->group(function () {
    Route::get('getAll', 'PostTagController@getAll');
});
Route::resource('post', 'PostController')->except(['create', 'edit']);
Route::prefix('post')->group(function () {
    Route::post('togglePublish', 'PostController@togglePublish');
});
Route::prefix('postComment')->group(function () {
    Route::post('/', 'PostCommentController@index');
    Route::delete('{id}', 'PostCommentController@destroy');
});
