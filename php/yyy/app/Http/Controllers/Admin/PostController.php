<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostComment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
    // 获取文章列表
    public function index(Request $request)
    {
        $limit = $request->get('limit') ? $request->get('limit') : 20;
        $posts = Post::with(['user', 'category', 'tags'])->withCount('comments')->orderBy('updated_at', 'desc')->paginate($limit);

        return sendData(200, '', $posts);
    }

    // 创建文章
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|min:2|max:128',
            'content' => 'required',
            'category_id' => 'required',
        ]);

        $data = $request->only('title', 'content', 'category_id', 'is_published');
        $data['user_id'] =  auth()->user()->id;
        if ($data['is_published']) $data['published_at'] = date('Y-m-d H:i:s');
        try {
            DB::beginTransaction();
            $post = Post::create($data);
            $post->tags()->sync($request->tags);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return sendData(402, '创建失败');
        }

        return sendData();
    }

    // 根据ID获取文章
    public function show($id)
    {
        $post = Post::with(['tags', 'category'])->find($id)->toArray();
        $tags = [];
        foreach ($post['tags'] as $tag) {
            $tags[] = $tag['id'];
        }
        $post['tags'] = $tags;

        return sendData(200, '', $post);
    }

    // 修改文章
    public function update(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|numeric',
            'title' => 'required|min:2|max:128',
            'content' => 'required',
            'category_id' => 'required',
        ]);

        if (!$post = Post::find($request->id)) {
            return sendData(402, 'ID错误');
        }
        if (Post::where('title', $request->title)
            ->whereNotIn('id', [$request->id])->first()
        ) {
            return sendData(402, '标题不能重复');
        }
        $data = $request->only('title', 'content', 'category_id', 'is_published');
        $data['user_id'] =  auth()->user()->id;
        if ($request->is_published) {
            $data['published_at'] = date('Y-m-d H:i:s');
        } else {
            $data['published_at'] = null;
        }
        try {
            DB::beginTransaction();
            $post->tags()->sync($request->tags);
            DB::table('posts')->where('id', $post->id)->update($data);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return sendData(402, '修改失败');
        }

        return sendData();
    }

    // 删除文章
    public function destroy(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|numeric',
        ]);
        if (!$post = Post::with('tags')->find($request->id)) {
            return sendData(402, 'ID错误');
        }
        try {
            DB::beginTransaction();
            $post->tags()->sync([]);
            PostComment::where('post_id', $post->id)->delete();
            $post->delete();
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return sendData(402, '删除失败');
        }

        return sendData();
    }

    // 发布、撤销
    public function togglePublish(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|numeric',
            'status' => 'required'
        ]);
        $update = [
            'is_published' => $request->status,
            'published_at' => date('Y-m-d H:i:s')
        ];
        if (!$request->status) {
            $update['published_at'] = null;
        }
        DB::table('posts')->where('id', $request->id)->update($update);

        return sendData();
    }
}
