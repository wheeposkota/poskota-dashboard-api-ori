<?php

namespace App\Micro;

use App\Repositories\EpaperRepository;
use App\Transformers\EpaperTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EpaperEndpoint extends Controller
{
    public $repo;
    public $transform;

    public function __construct(EpaperRepository $repo, EpaperTransformer $transform)
    {
        $this->middleware('epaper_subs')->only(['epaper_list', 'epaper_detail']);
        $this->repo = $repo;
        $this->transform = $transform;
    }

    public function epaper_list(Request $request)
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

        $result = $this->repo->retrieveEPaperList($param);

        $result['list_data'] = $this->transform->listEPaper($result['list_data']);

        return $this->res($result, true);
    }

    public function epaper_detail(Request $request)
    {
        $rules = [
            'date' => 'required|date:Y-m-d',
        ];
        $this->validate($request, $rules);

        $param = [
            'date' => $request->get('date'),
        ];

        $result = $this->repo->retrieveEpaperDetail($param);

        $result = $this->transform->detailEPaper($result);

        return $this->res($result, true);
    }

    public function epaper_package(Request $request)
    {
        $rules = [
            'id' => 'nullable|numeric',
        ];
        $this->validate($request, $rules);

        if ($request->has('id') && $request->get('id')) {
            $result = $this->repo->retrieveEPaperPackageDetail($request->get('id'));

            $result = $this->transform->packageEPaperDetail($result);

            return $this->res($result, true);
        }

        $result = $this->repo->retrieveEPaperPackage();

        $result = $this->transform->packageEPaper($result);

        return $this->res($result, true);
    }

    public function epaper_check()
    {
        $user = Auth::user();

        $result = $this->repo->checkEPaperSubs($user);

        return $this->res($result, true);
    }

    public function epaper_history_payment(Request $request)
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

        $user = Auth::user();

        $result = $this->repo->historyEPaperSubs($param, $user);

        $result['list_data'] = $this->transform->listEPaperSubs($result['list_data']);

        return $this->res($result, true);
    }
}
