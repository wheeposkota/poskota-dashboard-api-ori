<?php

namespace App\Micro;

use App\Events\PostViewed;
use App\Models\Post;
use App\Repositories\PostRepository;
use App\Transformers\PostTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostEndpoint extends Controller
{
    public $post;
    public $postRepo;
    public $postTransform;

    public function __construct(Post $post, PostRepository $postRepo, PostTransformer $postTransform)
    {
        $this->post = $post;
        $this->postRepo = $postRepo;
        $this->postTransform = $postTransform;
    }

    public function category(Request $request)
    {
        $parent = (int)$request->get('parent', 0);

        $param = [
            'parent' => $parent,
        ];

        $result = $this->postRepo->retrieveCategory($param);

        $result = $this->postTransform->listCategory($result);

        return $this->res($result, true);
    }

    public function post_list(Request $request)
    {
        $rules = [
            'category' => 'nullable|numeric',
            'tag' => 'nullable',
            'limit' => 'nullable|numeric',
            'page' => 'nullable|numeric',
            'search' => 'nullable',
        ];
        $this->validate($request, $rules);

        $category = (int)$request->get('category', 0);
        $tag = (string)$request->get('tag', '');

        $type = $request->get('type', '');
        if (!in_array($type, $this->postRepo->getTypePost()))
            $type = $this->postRepo->getTypePost()[0];

        $limit = (int)$request->get('limit', 5);
        if ($limit > 100)
            $limit = 100;

        $page = (int)$request->get('page', 0);

        $search = $request->get('search', '');

        $param = [
            'category' => $category,
            'tag' => $tag,
            'type' => $type,
            'limit' => $limit,
            'page' => $page,
            'search' => $search,
        ];

        $result = $this->postRepo->retrievePostList($param);

        $result['list_data'] = $this->postTransform->listPost($result['list_data']);

        return $this->res($result, true);
    }

    public function post_related(Request $request)
    {
        $rules = [
            'id' => 'required',
            'limit' => 'nullable|numeric',
        ];
        $this->validate($request, $rules);

        $limit = (int) $request->get('limit', 6);
        if ($limit > 25)
            $limit = 25;

        $param = [
            'id' => $request->get('id'),
            'limit' => $limit,
        ];

        $result = $this->postRepo->retrieveRelated($param);

        $result = $this->postTransform->relatedPost($result);

        return $this->res($result, true);
    }

    public function post_detail(Request $request)
    {
        $rules = [
            'id' => 'nullable',
            'slug' => 'nullable',
        ];
        $this->validate($request, $rules);

        $param = [
            'id' => $request->get('id'),
            'slug' => $request->get('slug'),
        ];

        $result = $this->postRepo->retrievePostDetail($param);

        event(new PostViewed($result->getKey()));

        $result = $this->postTransform->detailPost($result);

        return $this->res($result, true);
    }

    public function other_page()
    {
        $result = $this->postRepo->retrieveOtherPage();

        $result = $this->postTransform->listOtherPage($result);

        return $this->res($result, true);
    }

    public function other_page_detail(Request $request)
    {
        $rules = [
            'link' => 'required',
        ];
        $this->validate($request, $rules);

        $param = [
            'link' => $request->get('link'),
        ];

        $result = $this->postRepo->detailOtherPage($param);

        $result = $this->postTransform->detailOtherPage($result);

        return $this->res($result, true);
    }

    public function retrieve_bookmark(Request $request)
    {
        $rules = [
            'limit' => 'nullable|numeric',
            'page' => 'nullable|numeric',
        ];
        $this->validate($request, $rules);

        $limit = (int)$request->get('limit', 5);
        if ($limit > 100)
            $limit = 100;

        $page = (int)$request->get('page', 0);

        $param = [
            'limit' => $limit,
            'page' => $page,
            'user_id' => Auth::id() ?? 0,
        ];

        $result = $this->postRepo->retrieveBookmarkList($param);

        $result['list_data'] = $this->postTransform->bookmarkPost($result['list_data']);

        return $this->res($result, true);
    }

    public function submit_bookmark(Request $request)
    {
        $rules = [
            'id' => 'required',
        ];
        $this->validate($request, $rules);

        $id = $request->get('id');

        $param = [
            'id' => $id,
            'user_id' => Auth::id() ?? 0,
        ];

        $result = $this->postRepo->submitBookmark($param);

        return $this->res($result, true);
    }
}
