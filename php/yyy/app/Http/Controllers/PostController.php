<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\PostTag;
use App\Models\PostComment;

class PostController extends Controller
{
    private $more = '!--more--';

    public function __construct()
    {
        $this->middleware('refresh.token')->only(['publishComment']);
    }

    // 根据条件获取文章列表
    public function index(Request $request)
    {
        $limit = $request->get('limit') ? $request->get('limit') : 20;
        $where = [
            ['is_published', '=', 1],
        ];
        $params = $request->all();
        if (!empty($params['category_id'])) $where[] = ['category_id', '=', $params['category_id']];
        if (!empty($params['title'])) $where[] = ['title', 'like', '%' . $params['title'] . '%'];
        $posts = Post::with(['user', 'category', 'tags'])
            ->where($where)
            ->whereHas('tags', function ($q) use ($params) {
                if (!empty($params['tag_id'])) $q->where('tag_id', $params['tag_id']);
            })
            ->orderBy('published_at', 'desc')
            ->paginate($limit);
        foreach ($posts as $post) {
            $post->content = $this->getSummary($post->content);
        }

        return sendData(200, '', $posts);
    }

    // 根据ID获取文章
    public function show($id)
    {
        $post = Post::with(['tags', 'category', 'user'])->find($id);
        if (!$post) return sendData(402, '文章不存在');
        $post->content = $this->getContent($post->content);

        return sendData(200, '', $post);
    }

    // 发表评论
    public function publishComment(Request $request)
    {
        $this->validate($request, [
            'post_id' => 'required|numeric',
            'content' => 'required',
        ]);
        $data = $request->only(['post_id', 'content']);
        if ($request->get('parent_id')) {
            $data['parent_id'] = $request->parent_id;
        }
        $data['user_id'] = auth()->user()->id;
        PostComment::create($data);

        return sendData();
    }

    // 评论列表
    public function getCommentList(Request $request)
    {
        $this->validate($request, [
            'post_id' => 'required|numeric',
        ]);
        $limit = $request->get('limit') ? $request->get('limit') : 20;
        $comments = PostComment::where(['post_id' => $request->post_id])
            ->with('user')
            ->orderBy('updated_at', 'asc')
            ->paginate($limit);
        foreach ($comments as $comment) {
            if ($comment->parent_id != 0) {
                $comment->parent = PostComment::find($comment->parent_id);
            }
        }

        return sendData(200, '', $comments);
    }

    // 获取所有分类
    public function getCategoryList()
    {
        return sendData(200, '', PostCategory::all());
    }

    // 获取所有标签
    public function getTagList()
    {
        return sendData(200, '', PostTag::all());
    }

    // 获取文章摘要
    private function getSummary($content)
    {
        if (!$position = mb_strpos($content, $this->more)) {
            return $content;
        }

        return mb_substr($content, 0, $position);
    }

    // 过滤more标签
    private function getContent($content)
    {
        return str_replace($this->more, '', $content);
    }
}
