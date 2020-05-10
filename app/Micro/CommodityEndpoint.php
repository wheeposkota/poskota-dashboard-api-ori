<?php

namespace App\Micro;

use App\Repositories\CommodityRepository;
use App\Transformers\CommodityTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommodityEndpoint extends Controller
{
    public $repo;
    public $transform;

    public function __construct(CommodityRepository $repo, CommodityTransformer $transform)
    {
        $this->middleware('member_verified')->except('published');
        $this->repo = $repo;
        $this->transform = $transform;
    }

    public function published(Request $request)
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
        ];

        $result = $this->repo->retrieveListPublished($param);

        $result['list_data'] = $this->transform->listCommodity($result['list_data'], CommodityTransformer::PUBLISH);

        return $this->res($result, true);
    }

    public function index(Request $request)
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
            'id_member' => Auth::id(),
        ];

        $result = $this->repo->retrieveList($param);

        $result['list_data'] = $this->transform->listCommodity($result['list_data'], CommodityTransformer::PERSONAL);

        return $this->res($result, true);
    }

    public function type()
    {
        $result = $this->repo->retrieveType();

        return $this->res($result, true);
    }

    public function create(Request $request)
    {
        $rules = [
            'type_id' => 'required|numeric',
            'content' => 'required',
            'price' => 'required|numeric',
        ];
        $this->validate($request, $rules);

        $req = $request->all();
        list($log, $price_before) = $this->repo->compareBefore($req['type_id'], $req['price']);
        $req['log'] = $log;
        $req['price_before'] = $price_before;
        $req['member_id'] = Auth::id();
        $this->repo->store($req);

        return $this->res('created', true);
    }

    public function update(Request $request)
    {
        $rules = [
            'id' => 'required|numeric',
            'type_id' => 'required|numeric',
            'content' => 'required',
            'price' => 'required|numeric',
        ];
        $this->validate($request, $rules);

        $req = $request->only(array_keys($rules));
        $result = $this->repo->find($req['id']);

        if ($result->member_id != Auth::id())
            return $this->res('Invalid user');

        list($log, $price_before) = $this->repo->compareBefore($req['type_id'], $req['price'], $req['id']);
        $req['log'] = $log;
        $req['price_before'] = $price_before;
        $this->repo->update($result, $req);

        return $this->res('updated', true);
    }

    public function delete(Request $request)
    {
        $rules = [
            'id' => 'required|numeric',
        ];
        $this->validate($request, $rules);

        $result = $this->repo->find($request->get('id'));

        if ($result->member_id != Auth::id())
            return $this->res('Invalid user');

        $this->repo->delete($result);

        return $this->res('deleted', true);
    }
}
