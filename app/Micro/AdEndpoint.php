<?php

namespace App\Micro;

use App\Models\AdTrx;
use App\Models\TrxOrder;
use App\Models\TrxOrderMeta;
use App\Repositories\AdRepository;
use App\Services\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Ads\Entities\AdTaxonomy;
use Modules\Ads\Entities\MstAdCategories;
use Modules\Ads\Entities\MstAdTypes;
use Modules\Ads\Entities\MstCities;
use Modules\Ads\Entities\RltAdsCategories;
use Modules\Ads\Repositories\TrxAdsClassicRepository;

class AdEndpoint extends Controller
{
    const AD_GENERAL = 1;
    const AD_SPECIAL = 2;

    public $repo;
    public $repoCore;

    public function __construct(AdRepository $repo, TrxAdsClassicRepository $repoCore)
    {
        $this->repo = $repo;
        $this->repoCore = $repoCore;
    }

    public function advertisement(Request $request)
    {
        $rules = [
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

        $param = [
            'slug' => (string) $request->get('slug', ''),
            'search' => $request->get('search', ''),
            'limit' => $limit,
            'page' => $page,
        ];

        $result = $this->repo->retrieveAdToday($param);

        return $this->res($result, true);
    }

    public function advertisementGrouped(Request $request)
    {
        $rules = [
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

        $param = [
            'slug' => (string) $request->get('slug', ''),
            'search' => $request->get('search', ''),
            'limit' => $limit,
            'page' => $page,
        ];

        $result = $this->repo->retrieveAdToday($param);

        $result['list_data'] = $result['list_data']->groupBy('subcategory')->map(function($item, $key) {
            return [
                'group' => $key,
                'data' => $item,
            ];
        })->values();

        return $this->res($result, true);
    }

    public function advertisementCategory()
    {
        $result = $this->repo->retrieveCategory();

        return $this->res($result, true);
    }

    public function banner(Request $request)
    {
        $rules = [
            'position' => 'nullable',
        ];
        $this->validate($request, $rules);

        $param = [
            'position' => (string) $request->get('position', ''),
        ];

        $result = $this->repo->retrieveAdBanner($param);

        return $this->res($result, true);
    }

    public function bannerAll()
    {
        $param = [
            'position' => 'all', //hardcode
        ];

        $result = $this->repo->retrieveAdBanner($param);

        return $this->res($result, true);
    }

    public function advertisementParameter()
    {
        $cat_s = [
            DB::raw('id_rlt_ads_categories as id'),
            DB::raw('mst_ad_cat_name as name'),
            DB::raw('mst_ad_cat_slug as slug'),
            'min_char',
            'max_char',
            'min_line',
            'max_line',
            'char_on_line',
            'price',
        ];
        $cat_1 = RltAdsCategories::query()
            ->select($cat_s)
            ->leftJoin('mst_ad_categories', function ($q) {
                $q->on('rlt_ads_categories.category_id', '=', 'mst_ad_categories.id');
            })
            ->where('ads_id', self::AD_GENERAL)
            ->get()
            ->map(function($i) {
                $id = encrypt($i->id);
                unset($i->id);
                return [
                    'key' => $id,
                    'value' => $i,
                ];
            });

        $cat_2 = RltAdsCategories::query()
            ->select($cat_s)
            ->leftJoin('mst_ad_categories', function ($q) {
                $q->on('rlt_ads_categories.category_id', '=', 'mst_ad_categories.id');
            })
            ->where('ads_id', self::AD_SPECIAL)
            ->get()
            ->map(function($i) {
                $id = encrypt($i->id);
                unset($i->id);
                return [
                    'key' => $id,
                    'value' => $i,
                ];
            });

        $s = [
            DB::raw('mst_ad_type_name as name'),
            DB::raw('mst_ad_type_slug as slug'),
        ];
        $tipe = MstAdTypes::select($s)->get();

        $city = MstCities::select('id', 'name')->whereHas('province', function($query){
            $query->whereIn('name', MstAdCategories::HOME_PROVINCES);
        })->get()->map(function($i) {
            return [
                'id' => encrypt($i->id),
                'name' => $i->name,
            ];
        });

        $ad_taxo = AdTaxonomy::with('term')
            ->where('taxonomy', MstAdCategories::TAXONOMY_BRAND)
            ->get()
            ->map(function($i) {
                return [
                    'id' => $i->id,
                    'name' => $i->term->name,
                    'slug' => $i->term->slug,
                    'category' => $i->category_id,
                ];
            })
            ->groupBy('category')
            ->mapWithKeys(function($i, $k) {
                $slug = MstAdCategories::find($k)->mst_ad_cat_slug ?? "noname";
                $val = [];
                foreach ($i as $j)
                    $val[] = [
                        'id' => encrypt($j['id']),
                        'name' => $j['name'],
                        'slug' => $j['slug'],
                    ];
                return [$slug => $val];
            });

        $result = [
            'tipe' => $tipe,
            'kategori_umum' => $cat_1,
            'kategori_khusus' => $cat_2,
            'kota' => $city,
            'sub' => $ad_taxo,
            'add_param_kota' => MstAdCategories::SUB_LOCATION,
            'add_param_sub' => MstAdCategories::SUB_CATEGORY,
        ];
        return $this->res($result, true);
    }

    public function advertisementStore(Request $request)
    {
        $this->repoCore->validateAdsContent($request)->validate();

        $cities_id = (array) $request->get('mst_city_id');
        $subs_id = (array) $request->get('ad_taxonomy_id');

        foreach((array) $request->get('rlt_trx_ads_category_id') as $k => $cat_id) {
            $cat_id = decrypt($cat_id);
            $avail_sub = RltAdsCategories::query()
                ->with('mstAdsCategory')
                ->where('id_rlt_ads_categories', $cat_id)
                ->whereHas('mstAdsCategory', function($query){
                    $slugs = array_merge(MstAdCategories::SUB_CATEGORY, MstAdCategories::SUB_LOCATION);
                    $query->whereIn('mst_ad_cat_slug', $slugs);
                })
                ->first();
            if($avail_sub)
            {
                $category_slug = $avail_sub->mstAdsCategory->mst_ad_cat_slug;

                if(in_array($category_slug, MstAdCategories::SUB_LOCATION))
                {
                    $city_id = isset($cities_id[$k]) ? decrypt($cities_id[$k]) : 0;
                    MstCities::where('id', $city_id)->whereHas('province', function($query){
                        $query->whereIn('name', MstAdCategories::HOME_PROVINCES);
                    })->firstOrFail();
                }

                if(in_array($category_slug, MstAdCategories::SUB_CATEGORY)) {
                    $sub_id = isset($subs_id[$k]) ? decrypt($subs_id[$k]) : 0;
                    AdTaxonomy::with('term')
                        ->where('id', $sub_id)
                        ->where('taxonomy', MstAdCategories::TAXONOMY_BRAND)
                        ->firstOrFail();
                }
            }
        }

        try {
            $user = \Gdevilbat\SpardaCMS\Modules\Core\Entities\User::query()
                ->where('email', 'member@gmail.com')
                ->whereHas('role', function($query){
                    $query->where('slug', 'public');
                })
                ->firstOrFail();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // todo better handling for user not found
            $user = \Gdevilbat\SpardaCMS\Modules\Core\Entities\User::create([
                'name' => 'Member Poskota',
                'email' => 'member@gmail.com',
                'password' => bcrypt('poskota2019'),
                'created_by' => 1,
                'modified_by' => 1,
            ]);

            $role = DB::table('role')->where('slug', 'public')->first();
            if (is_null($role)) {
                DB::table('role')->insert([
                    'slug' => 'public',
                    'name' => 'Public Member',
                    'created_by' => 1,
                    'modified_by' => 1,
                ]);
            }
            $role = DB::table('role')->where('slug', 'public')->first();

            DB::table('role_users')->insert([
                'user_id' => $user->getKey(),
                'role_id' => $role->id_role,
            ]);
        }

        $ads = $this->repoCore->saveAds($request, $user);

        if($ads->status)
        {
            $rlt = \Modules\Ads\Entities\RltAdsMember::where('order_id', $ads->order_key)->first();
            if(empty($rlt))
                $rlt = new \Modules\Ads\Entities\RltAdsMember;

            $rlt->order_id = $ads->order_key;
            $rlt->member_id = \Auth::id(); // Changed!
            $rlt->save();

            return $this->res($ads->status, true);
        }

        return $this->res($ads->status, false);
    }

    public function advertisementCreated()
    {
        $s = [
            'trx_ads_classic.order_id',
            'trx_orders.order_number',
            DB::raw('status_name as status'),
            'trx_orders.created_at',
            'trx_order_price',
            'trx_order_quantity',
            'order_number',
            'mst_city_id',
            'mst_ad_type_id',
            DB::raw('trx_ads_classic.trx_ad_content as content'),
            DB::raw('trx_ads_classic.trx_ad_order_total_char as total_char'),
            DB::raw('mst_ad_types.mst_ad_type_name as type'),
            DB::raw('mst_cities.name as city'),
            DB::raw('mst_ad_categories.mst_ad_cat_name as category'),
            DB::raw('terms.name as subcategory'),
        ];
        $ads = AdTrx::query()
            ->select($s)
            ->leftJoin('trx_orders', function ($q) {
                $q->on('trx_ads_classic.order_id', '=', 'trx_orders.id');
            })
            ->leftJoin('mst_order_status', function ($q) {
                $q->on('trx_orders.order_status_id', '=', 'mst_order_status.id');
            })
            ->leftJoin('rlt_ads_member', function ($q) {
                $q->on('trx_ads_classic.order_id', '=', 'rlt_ads_member.order_id');
            })
            ->leftJoin('mst_ad_types', function ($q) {
                $q->on('trx_ads_classic.mst_ad_type_id', '=', 'mst_ad_types.id');
            })
            ->leftJoin('mst_cities', function ($q) {
                $q->on('trx_ads_classic.mst_city_id', '=', 'mst_cities.id');
            })
            ->leftJoin('rlt_ads_categories', function ($q) {
                $q->on('trx_ads_classic.rlt_trx_ads_category_id', '=', 'rlt_ads_categories.id_rlt_ads_categories');
            })
            ->leftJoin('mst_ad_categories', function ($q) {
                $q->on('rlt_ads_categories.category_id', '=', 'mst_ad_categories.id');
            })
            ->leftJoin('taxonomy_relationship_ads', function ($q) {
                $q->on('trx_ads_classic.id', '=', 'taxonomy_relationship_ads.object_id');
            })
            ->leftJoin('ad_taxonomy', function ($q) {
                $q->on('taxonomy_relationship_ads.ad_taxonomy_id', '=', 'ad_taxonomy.id');
            })
            ->leftJoin('terms', function ($q) {
                $q->on('ad_taxonomy.term_id', '=', 'terms.id_terms');
            })
            ->where('rlt_ads_member.member_id', Auth::id())
            ->orderByDesc('trx_ads_classic.id')
            ->get();
        return $this->res($ads, true);
    }

    public function advertisementConfirmation(Request $request)
    {
        $rules = [
            'order_id' => 'required',
            'confirmation' => 'required|image|mimes:jpeg,png,jpg,gif,svg,bmp|max:2048',
            'message' => 'required',
            'sender_name' => 'required',
            'sender_bank' => 'required',
            'sender_account' => 'required',
            'sender_nominal' => 'required',
        ];
        $this->validate($request, $rules);

        $req = $request->only(['message', 'sender_name', 'sender_bank', 'sender_account', 'sender_nominal']);
        if ($request->hasFile('confirmation')) {
            $file = $request->file('confirmation');

            if (!$file->isValid())
                return $this->res('Payment confirmation not valid');

            try {
                $fileimage = $file->store('payment_confirmation');
            } catch (Exception $e) {
                report($e);
                return $this->res('Payment confirmation not uploaded: ' . $e->getMessage());
            }
            $req['confirmation'] = $fileimage;
        }

        $trx = TrxOrder::findOrFail($request->get('order_id'));
        $status_id = Payment::getStatus(Payment::TRX_CONFIRM);
        $trx->order_status_id = $status_id;
        $trx->save();

        TrxOrderMeta::create([
            'trx_order_id' => $trx->getKey(),
            'meta_key' => TrxOrderMeta::KEY_CONFIRMATION,
            'meta_value' => $req,
        ]);

        return $this->res('', true);
    }
}
