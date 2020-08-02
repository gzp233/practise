<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ImageDirectory;
use App\Models\Image;

class ImageController extends Controller
{
    // 图片目录列表
    public function directoryList(Request $request)
    {
        return sendData(200, '', ImageDirectory::where('is_forbidden', 0)->get());
    }

    // 根据ID获取目录和图片
    public function getDirectoryById(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|numeric',
        ]);
        if (!$imageDirectory = ImageDirectory::with('user')->where('is_forbidden', 0)->find($request->get('id'))) {
            return sendData(402, '该相册不存在');
        }

        return sendData(200, '', $imageDirectory);
    }

    // 根据ID获取图片
    public function getImagesByDirectoryId(Request $request)
    {
        $limit = $request->get('limit') ? $request->get('limit') : 20;
        $this->validate($request, [
            'id' => 'required|numeric',
        ]);
        $dir = ImageDirectory::find($request->get('id'));
        if (!$dir || $dir->is_forbidden) {
            return sendData(402, "未找到目录");
        }
        $images = Image::where('directory_id', $request->id)->orderBy('updated_at', 'desc')->paginate($limit);

        return sendData(200, '', $images);
    }
}
