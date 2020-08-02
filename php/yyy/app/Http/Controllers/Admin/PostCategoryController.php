<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PostCategory;

class PostCategoryController extends Controller
{
    // 获取分类
    public function index(Request $request)
    {
        $limit = $request->get('limit') ? $request->get('limit') : 20;
        $categories = PostCategory::with('user')->withCount('posts')->paginate($limit);

        return sendData(200, '', $categories);
    }

    // 获取所有分类
    public function getAll(Request $request)
    {
        return sendData(200, '', PostCategory::all());
    }

    // 新建目录
    public function store(Request $request)
    {
        $this->validate($request, [
            'category_name' => 'required|min:2|max:32|unique:post_categories',
            'desc' => 'required|max:140',
        ]);

        $data = $request->only('category_name', 'desc');
        $data['user_id'] =  auth()->user()->id;

        if (!PostCategory::create($data)) {
            return sendData(402, '创建失败');
        }

        return sendData();
    }

    // 修改目录
    public function update(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|numeric',
            'category_name' => 'required|min:2|max:32',
            'desc' => 'required|max:140',
        ]);

        if (!$postCategory = PostCategory::find($request->id)) {
            return sendData(402, 'ID错误');
        }
        if (PostCategory::where('category_name', $request->category_name)
            ->whereNotIn('id', [$request->id])->first()
        ) {
            return sendData(402, '分类不能重复');
        }
        $postCategory->category_name = $request->category_name;
        $postCategory->desc = $request->desc;
        $postCategory->user_id = auth()->user()->id;
        if (!$postCategory->save()) {
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
        if (!$postCategory = PostCategory::with('posts')->find($request->id)) {
            return sendData(402, 'ID错误');
        }
        if (count($postCategory->posts) > 0) {
            return sendData(402, '分类中存在文章，无法删除');
        }
        if (!$postCategory->delete()) {
            return sendData(402, '删除失败');
        }

        return sendData();
    }
}
