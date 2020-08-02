<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ImageDirectory;

class ImageDirectoryController extends Controller
{
    // 图片目录列表
    public function index(Request $request)
    {
        $limit = $request->get('limit') ? $request->get('limit') : 20;
        $dirs = ImageDirectory::withCount('images')->paginate($limit);

        return sendData(200, '', $dirs);
    }

    // 根据ID获取目录和图片
    public function show(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|numeric',
        ]);
        if (!$imageDirectory = ImageDirectory::with('user')->find($request->id)) {
            return sendData(402, 'ID错误');
        }

        return sendData(200, '', $imageDirectory);
    }

    // 新建目录
    public function store(Request $request)
    {
        $this->validate($request, [
            'directory_name' => 'required|min:2|max:32|unique:image_directories',
            'desc' => 'required|max:140',
            'is_forbidden' => 'required|boolean',
        ]);

        $data = $request->only('directory_name', 'desc', 'is_forbidden');
        $data['user_id'] =  auth()->user()->id;

        if (!ImageDirectory::create($data)) {
            return sendData(402, '创建失败');
        }

        return sendData();
    }

    // 修改目录
    public function update(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|numeric',
            'directory_name' => 'required|min:2|max:32',
            'desc' => 'required|max:140',
            'is_forbidden' => 'required|boolean',
        ]);

        if (!$imageDirectory = ImageDirectory::find($request->id)) {
            return sendData(402, 'ID错误');
        }
        if (ImageDirectory::where('directory_name', $request->directory_name)
            ->whereNotIn('id', [$request->id])->first()
        ) {
            return sendData(402, '文件名不能重复');
        }
        $imageDirectory->directory_name = $request->directory_name;
        $imageDirectory->desc = $request->desc;
        $imageDirectory->is_forbidden = $request->is_forbidden;
        $imageDirectory->user_id = auth()->user()->id;
        if (!$imageDirectory->save()) {
            return sendData(402, '修改失败');
        }

        return sendData();
    }

    // 删除目录
    public function destroy(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|numeric',
        ]);
        if (!$imageDirectory = ImageDirectory::withCount('images')->find($request->id)) {
            return sendData(402, 'ID错误');
        }
        if ($imageDirectory->images_count > 0) {
            return sendData(402, '文件夹中存在图片，无法删除');
        }
        if (!$imageDirectory->delete()) {
            return sendData(402, '删除失败');
        }

        return sendData();
    }
}
