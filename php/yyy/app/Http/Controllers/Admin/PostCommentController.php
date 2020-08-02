<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PostComment;

class PostCommentController extends Controller
{
    // 获取评论列表
    public function index(Request $request)
    {
        $limit = $request->get('limit') ? $request->get('limit') : 20;
        $id = $request->get('id');
        $comments = PostComment::with('user')->where('post_id', $id)->paginate($limit);

        return sendData(200, '', $comments);
    }

    // 删除评论
    public function destroy(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|numeric',
        ]);
        if (!$comment = PostComment::find($request->id)) {
            return sendData(402, 'ID错误');
        }
        $comment->delete();

        return sendData();
    }
}
