<?php

namespace Modules\Ads\Micro;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

use Gdevilbat\SpardaCMS\Modules\Core\Http\Controllers\CoreController;
use Gdevilbat\SpardaCMS\Modules\Core\Repositories\Repository;
use Modules\Ads\Repositories\TrxAdsClassicRepository;

use Modules\Ads\Entities\RltAdsMember as RltAdsMember_m;
use App\Models\TrxOrder as TrxOrder_m;

use \Carbon\Carbon;
use Validator;
use Auth;

class TrxAdsClassicController extends CoreController
{
    public function __construct(TrxAdsClassicRepository $trx_ads_classic_repository)
    {
        parent::__construct();
        $this->trx_order_m = new TrxOrder_m;
        $this->trx_order_repository = new Repository(new TrxOrder_m, resolve(\Gdevilbat\SpardaCMS\Modules\Role\Repositories\Contract\AuthenticationRepository::class));
        $this->trx_ads_classic_repository = $trx_ads_classic_repository;
    }

    public function store(Request $request)
    {
        $validator = $this->trx_ads_classic_repository->validateAdsContent($request);

        if ($validator->fails()) {
            return $validator->errors();
        }

        try {
            $user = \Gdevilbat\SpardaCMS\Modules\Core\Entities\User::where('email', 'member@gmail.com')
                                                                    ->whereHas('role', function($query){
                                                                        $query->where('slug', 'public');
                                                                    })
                                                                    ->firstOrFail();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new \Exception($e->getMessage());
        }


        $ads = $this->trx_ads_classic_repository->saveAds($request, $user);

        if($ads->status)
        {
            $rlt = RltAdsMember_m::where('order_id', $ads->order_key)->first();
            if(empty($rlt))
                $rlt = new RltAdsMember_m;

            $rlt->order_id = $ads->order_key;
            $rlt->member_id = $request->input('member_id'); // Change Variable User login Here
            $rlt->save();

            return response()->json([
                'status' => $ads->status,
                'code' => 200
            ]);
        }
        else
        {
            return response()->json([
                'status' => $ads->status,
                'code' => 400
            ]);
        }
    }
}
