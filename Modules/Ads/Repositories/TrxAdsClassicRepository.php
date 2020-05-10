<?php

namespace Modules\Ads\Repositories;

use Gdevilbat\SpardaCMS\Modules\Core\Repositories;
use Gdevilbat\SpardaCMS\Modules\Core\Repositories\AbstractRepository;

use App\Models\TrxOrder as TrxOrder_m;
use App\Models\MstOrderStatus as MstOrderStatus_m;

use Modules\Ads\Entities\TrxAdsClassic as TrxAdsClassic_m;
use Modules\Ads\Entities\MstAdTypes as MstAdTypes_m;
use Modules\Ads\Entities\RltAdsCategories as RltAdsCategories_m;
use Modules\Ads\Entities\RltTaxonomyAds as RltTaxonomyAds_m;
use Modules\Ads\Entities\ClassicAdsSchedule as ClassicAdsSchedule_m;
use Modules\Ads\Entities\MstAdCategories as MstAdCategories_m;
use Modules\Ads\Entities\AdTaxonomy as AdTaxonomy_m;

use Auth;
use Validator;
use Carbon\Carbon;
use AgentRepository;

/**
 * Class EloquentCoreRepository
 *
 * @package Gdevilbat\SpardaCMS\Modules\Core\Repositories\Eloquent
 */
class TrxAdsClassicRepository  extends AbstractRepository
{
    protected $show_by_publish_date = false;

    public function __construct(\Illuminate\Database\Eloquent\Model $model, \Gdevilbat\SpardaCMS\Modules\Role\Repositories\Contract\AuthenticationRepository $acl)
    {
        parent::__construct($model, $acl);
        $this->trx_order_repository = resolve(\Modules\Ads\Repositories\TrxOrderRepository::class);
    }

    /**
     * @inheritdoc
     */
    public function all()
    {
        $this->model = $this->joinTrxOrder();

        if (method_exists($this->model, 'translations')) {
            return $this->model->with('translations')->orderBy('created_at', 'DESC')->get();
        }

        return $this->model->orderBy('created_at', 'DESC')->get();
    }

    /**
     * @inheritdoc
     */
    public function allWithBuilder() : \Illuminate\Database\Eloquent\Builder
    {
        $this->model = $this->joinTrxOrder();

        /*==========================================
        =            User Authorization            =
        ==========================================*/

            if(Auth::user()->can('read-all-transaction-classic-ads'))
            {
                $this->model = $this->model;
            }
            else
            {

               /* $this->model = $this->model->where(function($query){
                                            $query->where(TrxAdsClassic_m::getTableName().'.created_by', Auth::id())
                                                    ->orWhereHas('author.agent.users', function($query){
                                                        $query->where(\Gdevilbat\SpardaCMS\Modules\Core\Entities\User::getTableName().'.id', Auth::id());
                                                    }); /* Get Data By Created User
                });*/

                if(Auth::user()->can('create-transaction-classic-ads'))
                {
                    $this->model = $this->model->where(function($query){
                                            $query->where(TrxAdsClassic_m::getTableName().'.created_by', Auth::id())
                                            ->orWhereHas('author.agent.users', function($query){
                                                $query->where(\Gdevilbat\SpardaCMS\Modules\Core\Entities\User::getTableName().'.id', Auth::id());
                                            });
                                        }); /* Get Data By Created User */
                }
                elseif(Auth::user()->can('approving-payment-classic-ads'))
                {
                    $this->model = $this->model;
                }
                else
                {
                    $this->model = $this->model->where(function($query){

                        if(Auth::user()->can('approving-content-classic-ads'))
                        {
                            try {
                                $mst_order_status = MstOrderStatus_m::whereIn('status_name', [TrxOrder_m::TRX_DONE, TrxOrder_m::TRX_APPROVED])->get()->pluck(MstOrderStatus_m::getPrimaryKey());
                            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                                throw new \Exception($e->getMessage());
                            }

                            $query = $query->orWhereIn('order_status_id', $mst_order_status);
                        }

                        if(Auth::user()->can('layouting-content-classic-ads'))
                        {
                            try {
                                $mst_order_status = MstOrderStatus_m::whereIn('status_name', [TrxOrder_m::TRX_APPROVED, TrxOrder_m::TRX_EXPIRE, TrxOrder_m::TRX_CANCEL])->get()->pluck(MstOrderStatus_m::getPrimaryKey());
                            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                                throw new \Exception($e->getMessage());
                            }

                            $query = $query->orWhereIn('order_status_id', $mst_order_status);
                        }

                        return $query;

                    });

                    if($this->show_by_publish_date)
                    {
                        if(Auth::user()->can('approving-content-classic-ads') || Auth::user()->can('layouting-content-classic-ads'))
                        {
                            $this->model = $this->model->whereHas('schedules', function($query){
                                                        $query->whereDate('trx_ad_publish_date', \Carbon\Carbon::now()->addDay()->format('Y-m-d'));
                                            });
                        }
                    }

                }

            }

        /*=====  End of User Authorization  ======*/


        if (method_exists($this->model, 'translations')) {
            return $this->model->with('translations');
        }

        return $this->model;
    }

    public function joinTrxOrder()
    {
        return $this->model->leftJoin(\App\Models\TrxOrder::getTableName(), \App\Models\TrxOrder::getTableName().'.'.\App\Models\TrxOrder::getPrimaryKey(), '=', \Modules\Ads\Entities\TrxAdsClassic::getTableName().'.order_id')
                        ->leftJoin(\App\Models\MstOrderStatus::getTableName(), \App\Models\TrxOrder::getTableName().'.'.'order_status_id', '=', \App\Models\MstOrderStatus::getTableName().'.'.\App\Models\MstOrderStatus::getPrimaryKey())
                        ->leftJoin(\Modules\Ads\Entities\ClassicAdsSchedule::getTableName(), \Modules\Ads\Entities\TrxAdsClassic::getTableName().'.'.\Modules\Ads\Entities\TrxAdsClassic::getPrimaryKey(), '=', \Modules\Ads\Entities\ClassicAdsSchedule::getTableName().'.trx_ad_classic_id')
                        ->selectRaw(\Modules\Ads\Entities\TrxAdsClassic::getTableName().'.*, '.\App\Models\TrxOrder::getTableName().'.*, '.\Modules\Ads\Entities\ClassicAdsSchedule::getTableName().'.*, '.\Modules\Ads\Entities\TrxAdsClassic::getTableName().'.'.\Modules\Ads\Entities\TrxAdsClassic::getPrimaryKey().' as id_classic_ads, '.\Modules\Ads\Entities\TrxAdsClassic::getTableName().'.'.'deleted_at as content_deleted_at, '.\Modules\Ads\Entities\ClassicAdsSchedule::getTableName().'.deleted_at as schedule_deleted_at,'.\App\Models\TrxOrder::getTableName().'.created_at as order_created_at,trx_order_price * trx_order_quantity * (100 - trx_order_discount) / 100 as trx_order_total');
    }


    public function getAdsPrice(int $char_lenght, \Modules\Ads\Entities\RltAdsCategories $rlt_ads_categories): int
    {
        if($char_lenght < ($rlt_ads_categories->min_line * $rlt_ads_categories->char_on_line))
        {
            return $rlt_ads_categories->min_line * $rlt_ads_categories->price;
        }

        return ceil(($char_lenght/($rlt_ads_categories->char_on_line))) * $rlt_ads_categories->price;
    }

    public function validateAdsContent(\Illuminate\Http\Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tipe' => 'required',
            'kriteria' => 'required',
            'trx_ad_publish_date' => 'required',
            'rlt_trx_ads_category_id' => 'required',
            'trx_ad_content' => 'required',
            'trx_ad_publish_date.*' => 'required|date_format:Y-m-d',
            'rlt_trx_ads_category_id.*' => 'required',
            'trx_ad_content.*' => 'required',
        ]);

        $validator->addRules([
            'trx_ad_content' => [
                function ($attribute, $value, $fail) use ($request, $validator) {
                    foreach ($value as $key => $string) {
                        $rlt = RltAdsCategories_m::with('mstAdsCategory')->where(RltAdsCategories_m::getPrimaryKey(), decrypt($request->input('rlt_trx_ads_category_id')[$key]))->first();
                        if (ceil(strlen(utf8_decode(str_replace("\r\n", ' ', $string)))/$rlt->char_on_line) > $rlt->max_line) {
                            $fail($attribute.' '.($key+1).' Must Be Smaller Than '.$rlt->max_line.' Line');
                        }

                        /* Check Taxonomy ID */
                        if(in_array($rlt->mstAdsCategory->mst_ad_cat_slug, MstAdCategories_m::SUB_CATEGORY) && empty($request->input('ad_taxonomy_id.'.$key)))
                        	$validator->errors()->add('ad_taxonomy_id.'.$key, 'ad_taxonomy_id.'.($key+1).' Required');


                        /* Check Location */
                        if(in_array($rlt->mstAdsCategory->mst_ad_cat_slug, MstAdCategories_m::SUB_LOCATION) && empty($request->input('mst_city_id.'.$key)))
                            $validator->errors()->add('mst_city_id.'.$key, 'mst_city_id.'.($key+1).' Required');

                        /*================================================
                        =            Check Ad Taxonomy Prefix            =
                        ================================================*/
                        
                            if($request->input('ad_taxonomy_id.'.$key))
                            {
                                $AdTaxonomy = AdTaxonomy_m::with('term')->where(AdTaxonomy_m::getPrimaryKey(), decrypt($request->input('ad_taxonomy_id.'.$key)))->first();
                                if(!preg_match("/^".$AdTaxonomy->term->name."/", $string))
                                    $validator->errors()->add('prefix_ad_taxonomy_id.'.$key, 'Iklan.'.($key+1).' Harus Berawalan '.$AdTaxonomy->term->name.' Sesuai Nama Subcategori');
                            }
                        
                        /*=====  End of Check Ad Taxonomy Prefix  ======*/
                        


                        /*=============================================
                        =            Check Content Legth            =
                        =============================================*/
                        
                        	$words = explode(' ', utf8_decode(str_replace("\r\n", ' ', $string)));
                        	foreach ($words as $word) {
                        		$listemail = ['@gmail', '@yahoo', '@outlook', '@live'];
								$includeemail = false; 
								for($m = 0; $m < count($listemail); $m++){
								  $email = strtolower($word);
								  $includeemail = (strpos($email,$listemail[$m]) !== false);
								  if($includeemail) {
								      break;
								  }
								}

                        		if(strlen($word) >= 25 && !$includeemail)
                        		 $validator->errors()->add('trx_ad_content.'.$key, 'trx_ad_content.'.($key+1).' '.$word.' Lebih dari 25 Karakter');
                        	}
                        
                        /*=====  End of Check Content Legth  ======*/
                        
                    }
                },
            ],
            'trx_ad_publish_date' => [
                function ($attribute, $value, $fail) use ($request, $validator) {
                    foreach ($value as $key => $date) {
                        if(Carbon::createFromFormat('Y-m-d', $date)->lt(Carbon::now()))
                            $validator->errors()->add($attribute.'.'.$key, $attribute.'.'.($key+1).' Must Be Bigger Than '.Carbon::now()->format('Y-m-d'));
                    }
                }
            ]
        ]);

        /*===============================================
        =            Validate Duplicate Data            =
        ===============================================*/
        
            $arr = collect($request->input('trx_ad_publish_date'));

            // Convert every value to uppercase, and remove duplicate values
            $unique = $arr->unique();

            if($unique->count() != $arr->count())
            {
                 $validator->addRules([
                    'trx_ad_publish_date' => [
                        function ($attribute, $value, $fail) use ($request) {
                            $fail($attribute.' Have Duplicate Publish Date, Check Again');
                        },
                    ]
                ]);
            }
        
        /*=====  End of Validate Duplicate Data  ======*/
        




        return $validator;
    }

    public function saveAds(\Illuminate\Http\Request $request, \Gdevilbat\SpardaCMS\Modules\Core\Entities\User $user)
    {
        if($request->isMethod('POST'))
        {
            $trx_order = new TrxOrder_m;
        }
        else
        {
            $trx_order = TrxOrder_m::findOrFail(decrypt($request->input(TrxOrder_m::getPrimaryKey())));
            //$this->authorize('update-master-ads', $trx_order);
        }

        try {
            $mst_order_status = MstOrderStatus_m::where('status_name', TrxOrder_m::TRX_PENDING)->firstOrFail();
            $mst_ad_type = MstAdTypes_m::where('mst_ad_type_slug', $request->input('tipe'))->firstOrFail();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new \Exception($e->getMessage());
        }

        if($request->isMethod('POST'))
        {
            $trx_order->trx_order_date = Carbon::now();
            $trx_order->order_number = $this->trx_order_repository->getOrderNumber();
        }

        if(AgentRepository::isAgent($user))
            $trx_order->trx_order_discount = AgentRepository::getUserDiscount($user);

        $trx_order->order_status_id = $mst_order_status->getKey();

        if($request->input('tipe') == 'non-kontrak' || ($request->input('tipe') == 'kontrak' && $request->input('kriteria') == 1))
        {
            $rlt_trx_ads_category_id = [];

            foreach ((array) $request->input('rlt_trx_ads_category_id') as $key => $value)
            {
                $rlt_trx_ads_category_id[$key] = decrypt($value);
            }

            $trx_order->trx_order_quantity = 1;
            $price = 0;
            $trx_ad_classic_object = [];

            foreach ($rlt_trx_ads_category_id as $key => $value)
            {
                $rlt_trx_ads_category = RltAdsCategories_m::where(RltAdsCategories_m::getPrimaryKey(), $value)->first();

                $length = strlen(utf8_decode(str_replace("\r\n", ' ', $request->input('trx_ad_content')[$key])));

                $price += $this->getAdsPrice($length, $rlt_trx_ads_category);

                $trx_ad_classic = new TrxAdsClassic_m;
                $trx_ad_classic->order_id = $trx_order->getKey();
                $trx_ad_classic->trx_ad_content = $request->input('trx_ad_content')[$key];
                $trx_ad_classic->trx_ad_order_total_char = $length;
                $trx_ad_classic->trx_ad_order_price = $this->getAdsPrice($length, $rlt_trx_ads_category);
                $trx_ad_classic->mst_ad_type_id = $mst_ad_type->getKey();
                $trx_ad_classic->rlt_trx_ads_category_id = $value;

                if($request->has('mst_city_id.'.$key) && !empty($request->input('mst_city_id.'.$key)))
                {
                    $trx_ad_classic->mst_city_id = decrypt($request->input('mst_city_id.'.$key));
                }

                $trx_ad_classic->created_by = $user->getKey();
                $trx_ad_classic->modified_by = $user->getKey();


                $trx_ad_classic_object[] = $trx_ad_classic;
            }

            $trx_order->trx_order_price = $price;

            if($trx_order->save())
            {
                TrxAdsClassic_m::where(['order_id' => $trx_order->getKey()])->forceDelete();
                $trx_ad_classics = $trx_order->trxAdClassics()->saveMany($trx_ad_classic_object);

                foreach ($trx_ad_classics as $key => $trx_ad_classic)
                {
                    $schedule = new ClassicAdsSchedule_m;
                    $schedule->trx_ad_publish_date = collect($request->input('trx_ad_publish_date'))->first();

                    if($request->has('ad_taxonomy_id.'.$key) && !empty($request->input('ad_taxonomy_id.'.$key)))
                    {
                        $rltTaxonomyAds = RltTaxonomyAds_m::where('object_id', $trx_ad_classic->getKey())->first();
                        if(empty($rltTaxonomyAds))
                            $rltTaxonomyAds = new RltTaxonomyAds_m;

                        $rltTaxonomyAds->ad_taxonomy_id = decrypt($request->input('ad_taxonomy_id.'.$key));

                        $trx_ad_classic->rltTaxonomyAds()->save($rltTaxonomyAds);
                    }

                    if($trx_ad_classic->schedules()->save($schedule))
                    {
                        $status = true;
                    }
                    else
                    {
                        $status = false;
                    }
                }
            }
            else
            {
                if($request->isMethod('POST'))
                {
                    $response = [
                        'status' => false,
                        'message' => 'Failed To Add Classic Ads Transaction!'
                    ];
                }
                else
                {
                    $response = [
                        'status' => false,
                        'message' => 'Failed To Update Classic Ads Transaction!'
                    ];
                }
            }


        }
        elseif(($request->input('tipe') == 'kontrak' && $request->input('kriteria') == 2))
        {
            $quantity = count($request->input('trx_ad_publish_date'));

            $rlt_trx_ads_category = RltAdsCategories_m::where(RltAdsCategories_m::getPrimaryKey(), decrypt(collect($request->input('rlt_trx_ads_category_id'))->first()))->first();

            $length = strlen(utf8_decode(str_replace("\r\n", ' ', collect($request->input('trx_ad_content'))->first())));

            $trx_order->trx_order_price = $this->getAdsPrice($length, $rlt_trx_ads_category);
            $trx_order->trx_order_quantity = $quantity;

            if($trx_order->save())
            {
                TrxAdsClassic_m::where(['order_id' => $trx_order->getKey()])->forceDelete();

                $trx_ad_classic = new TrxAdsClassic_m;
                $trx_ad_classic->order_id = $trx_order->getKey();
                $trx_ad_classic->trx_ad_content = collect($request->input('trx_ad_content'))->first();
                $trx_ad_classic->trx_ad_order_price = $this->getAdsPrice($length, $rlt_trx_ads_category);
                $trx_ad_classic->trx_ad_order_total_char = $length;
                $trx_ad_classic->mst_ad_type_id = $mst_ad_type->getKey();
                $trx_ad_classic->rlt_trx_ads_category_id = decrypt(collect($request->input('rlt_trx_ads_category_id'))->first());

                if($request->has('mst_city_id') && !empty(collect($request->input('mst_city_id'))->first()))
                {
                    $trx_ad_classic->mst_city_id = decrypt(collect($request->input('mst_city_id'))->first());
                }

                $trx_ad_classic->created_by = Auth::id();
                $trx_ad_classic->modified_by = Auth::id();

                if($trx_ad_classic->save())
                {
                    if($request->has('ad_taxonomy_id') && !empty(collect($request->input('ad_taxonomy_id'))->first()))
                    {
                        $rltTaxonomyAds = RltTaxonomyAds_m::where('object_id', $trx_ad_classic->getKey())->first();
                        if(empty($rltTaxonomyAds))
                            $rltTaxonomyAds = new RltTaxonomyAds_m;

                        $rltTaxonomyAds->ad_taxonomy_id = decrypt(collect($request->input('ad_taxonomy_id'))->first());

                        $trx_ad_classic->rltTaxonomyAds()->save($rltTaxonomyAds);
                    }

                    ClassicAdsSchedule_m::where('trx_ad_classic_id', $trx_ad_classic->getKey())->delete();

                    $schedules = [];
                    foreach ($request->input('trx_ad_publish_date') as $key => $value)
                    {

                        $schedule = new ClassicAdsSchedule_m;
                        $schedule->trx_ad_publish_date = $request->input('trx_ad_publish_date')[$key];
                        $schedule->trx_ad_classic_id = $trx_ad_classic->getKey();

                        $schedules[] = $schedule;
                    }


                    if($trx_ad_classic->schedules()->saveMany($schedules))
                    {
                        $status = true;
                    }
                    else
                    {
                        $status = false;
                    }
                }
            }
        }
        else
        {
            $response = [
                        'status' => false,
                        'message' => 'Tipe Ads dan Kriteria Tidak Ditemukan'
                    ];
        }

        if($status ?? false)
        {
            if($request->isMethod('POST'))
            {
                $response = [
                                'status' => true,
                                'message' => 'Successfully Add Classic Ads Transaction!',
                                'order_key' => $trx_order->getKey()
                            ];
            }
            else
            {
                $response = [
                                'status' => true,
                                'message' => 'Successfully Update Classic Ads Transaction!',
                                'order_key' => $trx_order->getKey()
                            ];
            }
        }
        else
        {
            if($request->isMethod('POST'))
            {
                $response = [
                                'status' => false,
                                'message' => 'Failed To Add Classic Ads Transaction!'
                            ];
            }
            else
            {
                $response = [
                                'status' => false,
                                'message' => 'Failed To Update Classic Ads Transaction!'
                            ];
            }
        }

        return json_decode(json_encode($response));
    }

    public function setShowByPublishDate()
    {
        $this->show_by_publish_date = true;

        return $this;
    }
}
