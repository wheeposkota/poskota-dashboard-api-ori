<?php

namespace App\Micro;

use App\Models\Media;
use App\Repositories\MediaRepository;
use App\Transformers\MediaTransformer;
use Illuminate\Http\Request;

class MediaEndpoint extends Controller
{
    public $repo;
    public $transform;

    public function __construct(MediaRepository $repo, MediaTransformer $transform)
    {
        $this->repo = $repo;
        $this->transform = $transform;
    }

    public function media_list(Request $request)
    {
        $rules = [
            'type' => 'required|in:' . Media::TYPE_PHOTO . ',' . Media::TYPE_VIDEO,
            'slug' => 'nullable',
            'limit' => 'nullable|numeric',
            'page' => 'nullable|numeric',
            'search' => 'nullable',
        ];
        $this->validate($request, $rules);

        $limit = (int)$request->get('limit', 5);
        if ($limit > 100)
            $limit = 100;

        $page = (int)$request->get('page', 0);

        $search = $request->get('search', '');

        $param = [
            'type' => $request->get('type'),
            'limit' => $limit,
            'page' => $page,
            'search' => $search,
        ];

        $result = $this->repo->retrieveMediaList($param);

        $result['list_data'] = $this->transform->listMedia($result['list_data']);

        return $this->res($result, true);
    }

    public function media_detail(Request $request)
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

        $result = $this->repo->retrieveMediaDetail($param);

        $result = $this->transform->detailMedia($result);

        return $this->res($result, true);
    }
}
