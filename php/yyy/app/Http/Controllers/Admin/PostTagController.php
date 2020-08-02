<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PostTag;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PostTagController extends Controller
{
    // 分页获取分类
    public function index(Request $request)
    {
        $limit = $request->get('limit') ? $request->get('limit') : 20;
        $tags = PostTag::withCount('posts')->orderBy('updated_at', 'desc')->paginate($limit);

        return sendData(200, '', $tags);
    }

    // 获取所有标签
    public function getAll(Request $request)
    {
        return sendData(200, '', PostTag::all());
    }

    // 新建目录
    public function store(Request $request)
    {
        $this->validate($request, [
            'tag_name' => 'required|min:2|max:16|unique:post_tags',
        ]);

        $data = $request->only('tag_name');

        if (!PostTag::create($data)) {
            return sendData(402, '创建失败');
        }

        return sendData();
    }

    // 修改目录
    public function update(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|numeric',
            'tag_name' => 'required|min:2|max:32',
        ]);

        if (!$postTag = PostTag::find($request->id)) {
            return sendData(402, 'ID错误');
        }
        if (PostTag::where('tag_name', $request->tag_name)
            ->whereNotIn('id', [$request->id])->first()
        ) {
            return sendData(402, '名称不能重复');
        }
        $postTag->tag_name = $request->tag_name;
        if (!$postTag->save()) {
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
        if (!$postTag = PostTag::withCount('posts')->find($request->id)) {
            return sendData(402, 'ID错误');
        }
        try {
            DB::beginTransaction();
            $postTag->posts()->detach();
            $postTag->delete();
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return sendData(402, '删除失败');
        }

        return sendData();
    }
}
