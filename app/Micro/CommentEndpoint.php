<?php

namespace App\Micro;

use App\Repositories\CommentRepository;
use App\Transformers\CommentTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentEndpoint extends Controller
{
    public $repo;
    public $transform;

    public function __construct(CommentRepository $repo, CommentTransformer $transform)
    {
        $this->repo = $repo;
        $this->transform = $transform;
    }

    public function comment_list(Request $request)
    {
        $rules = [
            'type' => 'required|in:post,media',
            'id' => 'required',
            'limit' => 'nullable|numeric',
            'page' => 'nullable|numeric',
            'search' => 'nullable',
        ];
        $this->validate($request, $rules);

        $type = $request->get('type');
        $id = $request->get('id');

        $limit = (int)$request->get('limit', 5);
        if ($limit > 100)
            $limit = 100;

        $page = (int)$request->get('page', 0);

        $param = [
            'type' => $type,
            'id' => $id,
            'limit' => $limit,
            'page' => $page,
        ];

        $result = $this->repo->retrieveCommentList($param);

        $result['list_data'] = $this->transform->listComment($result['list_data']);

        return $this->res($result, true);
    }

    public function comment_post(Request $request)
    {
        $rules = [
            'type' => 'required|in:post,media',
            'id' => 'required',
            'comment' => 'required|min:3',
        ];
        $this->validate($request, $rules);

        $type = $request->get('type');
        $id = $request->get('id');
        $comment = $request->get('comment');

        $param = [
            'type' => $type,
            'id' => $id,
            'comment' => $comment,
            'user_id' => Auth::id() ?? 0,
        ];

        $result = $this->repo->submitComment($param);

        //todo trigger update cache comment

        return $this->res($result, true);
    }
}
