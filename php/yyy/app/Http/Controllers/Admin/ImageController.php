<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Image;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    // 根据ID获取图片
    public function getImagesByDirectoryId(Request $request)
    {
        $limit = $request->get('limit') ? $request->get('limit') : 20;
        $this->validate($request, [
            'id' => 'required|numeric',
        ]);
        $images = Image::where('directory_id', $request->id)->orderBy('updated_at', 'desc')->paginate($limit);
        return sendData(200, '', $images);
    }

    // 上传图片
    public function upload(Request $request)
    {
        $this->validate($request, [
            'directory_id' => 'required|exists:image_directories,id',
        ]);
        if (!$request->hasFile('files')) {
            return $this->sendData(402, '请选择上传的图片');
        }
        $images = $request->file('files');
        $data = [];
        foreach ($images as $image) {
            $path = $image->store('', 'qiniu');
            $data[] = [
                'directory_id' => $request->directory_id,
                'image' => $path,
                'user_id' => auth()->user()->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
        }
        if (!Image::insert($data)) {
            return sendData(402, '上传失败');
        }

        return sendData();
    }

    // 删除图片
    public function deleteByIds(Request $request)
    {
        $ids = $request->get('ids');
        if (empty($ids)) {
            return sendData(402, '请选择要删除的图片');
        }
        $images = Image::whereIn('id', $ids)->get();
        foreach ($images as $image) {
            Storage::disk('qiniu')->delete($image->image);
            $image->delete();
        }

        return sendData();
    }
}
