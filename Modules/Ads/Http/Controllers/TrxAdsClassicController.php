<?php

namespace Modules\Ads\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

use Gdevilbat\SpardaCMS\Modules\Core\Http\Controllers\CoreController;
use Gdevilbat\SpardaCMS\Modules\Core\Repositories\Repository;
use Modules\Ads\Repositories\TrxAdsClassicRepository;
use Modules\Agent\Repositories\AgentRepository;

use Modules\Ads\Entities\TrxAdsClassic as TrxAdsClassic_m;

use App\Models\TrxOrder as TrxOrder_m;
use App\Models\TrxOrderMeta as TrxOrderMeta_m;
use App\Models\MstOrderStatus as MstOrderStatus_m;
use Modules\Ads\Entities\MstAds as MstAds_m;
use Modules\Ads\Entities\RltAdsCategories as RltAdsCategories_m;
use Modules\Ads\Entities\MstAdCategories as MstAdCategories_m;
use Modules\Ads\Entities\ClassicAdsSchedule as ClassicAdsSchedule_m;

use Modules\Payment\Traits\MidtransPayment;

use App\Models\NotificationPayment as NotificationPayment_m;

use \Carbon\Carbon;
use Validator;
use DB;
use Auth;
use View;
use Route;
use Storage;

class TrxAdsClassicController extends CoreController
{
    use MidtransPayment;

    public function __construct(TrxAdsClassicRepository $trx_ads_classic_repository, AgentRepository $agent_repository)
    {
        parent::__construct();
        $this->trx_order_m = new TrxOrder_m;
        $this->trx_order_repository = new Repository(new TrxOrder_m, resolve(\Gdevilbat\SpardaCMS\Modules\Role\Repositories\Contract\AuthenticationRepository::class));
        $this->trx_ads_classic_repository = $trx_ads_classic_repository;
        $this->agent_repository = $agent_repository;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if(Auth::user()->can('read-all-transaction-classic-ads') || Auth::user()->can('create-transaction-classic-ads') || Auth::user()->can('approving-payment-classic-ads') || Auth::user()->can('approving-content-classic-ads') || Auth::user()->can('layouting-content-classic-ads'))
        {
            $this->data['agents_report'] = \Gdevilbat\SpardaCMS\Modules\Core\Entities\User::whereHas('role', function($query){
                                                    $query->where('slug', 'agen-iklan')
                                                        ->orWhere('slug', 'staff-iklan');
                                        })->get();

            return view('ads::admin.'.$this->data['theme_cms']->value.'.content.TrxAdsClassic.master', $this->data);
        }
        else
        {
            abort(403);
        }

    }

     public function serviceMaster(Request $request)
    {
        $column = ['', 'order_id', 'tipe', 'kategori', 'trx_ad_content','trx_order_quantity','trx_order_price', 'trx_ad_publish_date','trx_order_discount','trx_order_total', 'author','status_name','order_created_at'];

        $length = !empty($request->input('length')) ? $request->input('length') : 10 ;
        $column = !empty($request->input('order.0.column')) ? $column[$request->input('order.0.column')] : TrxAdsClassic_m::getTableName().'.'.TrxAdsClassic_m::getPrimaryKey() ;
        $dir = !empty($request->input('order.0.dir')) ? $request->input('order.0.dir') : 'DESC' ;
        $searchValue = $request->input('search')['value'];

        config()->set('database.connections.mysql.strict', false);
        \DB::reconnect();

        $query = $this->trx_ads_classic_repository->with([
                                                        'trxOrder' => function($query){
                                                            $query->select(\App\Models\TrxOrder::getPrimaryKey(), 'order_status_id', 'order_number');
                                                        },
                                                        'trxOrder.mstAdStatus' => function($query){
                                                            $query->select(\App\Models\MstOrderStatus::getPrimaryKey(), 'status_name');
                                                        },
                                                        'author',
                                                        'trxOrder.members',
                                                        'trxOrder.trxAdClassics.rltAdCategory.mstAd',
                                                        'trxOrder.trxAdClassics.rltAdCategory.mstAdsCategory',
                                                        'mstAdType',
                                                ])
                                              ->setShowByPublishDate()
                                              ->allWithBuilder()
                                              ->orderBy($column, $dir);

        $recordsTotal = $query->count(DB::raw("DISTINCT order_id"));
        $filtered = $query;

        if($searchValue)
        {
            $filtered->where(function($query) use ($searchValue){
                    $query->where(DB::raw("CONCAT(order_id,'-',ifnull(order_number, ''),'-', trx_order_price,'-', status_name,'-',trx_ad_content,'-',"."'-',".TrxOrder_m::getTableName().".created_at)"), 'like', "%".$searchValue."%")
                        ->orWhereRaw('(trx_order_price * trx_order_quantity * (100 - trx_order_discount) / 100) like '."'%".$searchValue."%'")
                        ->orWhereHas('mstAdType', function($query) use ($searchValue){
                            $query->where('mst_ad_type_name', 'LIKE', '%'.$searchValue.'%');
                        })
                        ->orWhereHas('author', function($query) use ($searchValue){
                            $query->where('name', 'LIKE', '%'.$searchValue.'%');
                        })
                        ->orWhereHas('trxOrder.meta', function($query) use ($searchValue){
                            $query->where('meta_value', 'LIKE', '%'.$searchValue.'%');
                        })
                        ->orWhereHas('trxOrder.members', function($query) use ($searchValue){
                            $query->where('name', 'LIKE', '%'.$searchValue.'%');
                        })
                        ->orWhereHas('trxOrder.trxAdClassics.rltAdCategory', function($query) use ($searchValue){
                            $query->whereHas('mstAdsCategory', function($query) use ($searchValue){
                                        $query->where('mst_ad_cat_name', 'LIKE', '%'.$searchValue.'%');
                                    })
                                    ->orWhereHas('mstAd', function($query) use ($searchValue){
                                            $query->where('mst_ad_name', 'LIKE', '%'.$searchValue.'%');
                                    });
                        });
                });
        }

        $filteredTotal = $filtered->count(DB::raw("DISTINCT order_id"));

        $this->data['length'] = $length;
        $this->data['column'] = $column;
        $this->data['dir'] = $dir;
        $this->data['trx_ads_classic'] = $filtered->offset($request->input('start'))->limit($length)->groupBy('order_id')->get();

        config()->set('database.connections.mysql.strict', true);
        \DB::reconnect();

        //return $this->print_r($this->data['trx_ads_classic']->toArray());

        /*=========================================
        =            Parsing Datatable            =
        =========================================*/
            
            $data = array();
            $i = 0;
            foreach ($this->data['trx_ads_classic'] as $key_user => $trx_ad_classic) 
            {
                if($trx_ad_classic->trxOrder->mstAdStatus->status_name == TrxOrder_m::TRX_APPROVED)
                {
                    $data[$i][] = sprintf('<input type="checkbox" name="id[]" value="%s" class="checkitem">', $trx_ad_classic->order_id);
                }
                else
                {
                    $data[$i][] = '';
                }

                $data[$i][] = $trx_ad_classic->trxOrder->invoice_number;
                $data[$i][] = $trx_ad_classic->mstAdType->mst_ad_type_name;

                $data[$i][] = '';

                foreach ($trx_ad_classic->trxOrder->trxAdClassics as $key => $trxAdClassic) {
                     $data[$i][count($data[$i]) - 1] .= sprintf('%s, <br/><br/>',$trxAdClassic->rltAdCategory->mstAdsCategory->mst_ad_cat_name);
                }

                if($trx_ad_classic->trxOrder->trxAdClassics->count() > 1)
                {
                    $data[$i][] = '';
                    foreach ($trx_ad_classic->trxOrder->trxAdClassics as $key => $trxAdClassic) {
                        $data[$i][count($data[$i]) - 1] .= sprintf('%s, <br/><br/>', preg_replace('/^[^\s]+\s+[^\s]+|^[^\s]+/', '<span style="text-transform: uppercase;"><b>$0</b></span>', str_replace('&nbsp;', " " , $trxAdClassic->trx_ad_content)));
                    }

                    $data[$i][] = '';
                    foreach ($trx_ad_classic->trxOrder->trxAdClassics as $key => $trxAdClassic) {
                        $data[$i][count($data[$i]) - 1] .= sprintf('%s, <br/><br/>',1);
                    }

                    $data[$i][] = '';
                    foreach ($trx_ad_classic->trxOrder->trxAdClassics as $key => $trxAdClassic) {
                        $data[$i][count($data[$i]) - 1] .= sprintf('Rp. %s, <br/><br/>', number_format($trxAdClassic->trx_ad_order_price));
                    }

                    $data[$i][] = Carbon::createFromFormat('Y-m-d H:i:s' ,$trx_ad_classic->trxOrder->trxAdClassics->first()->schedules()->first()->trx_ad_publish_date)->format('Y-m-d');
                }
                else
                {
                    $data[$i][] = preg_replace('/^[^\s]+\s+[^\s]+|^[^\s]+/', '<span style="text-transform: uppercase;"><b>$0</b></span>', str_replace('&nbsp;', " " , $trx_ad_classic->trx_ad_content));
                    $data[$i][] = '';
                    $data[$i][count($data[$i]) - 1] = $trx_ad_classic->trxOrder->trxAdClassics->first()->schedules()->count();
                    $data[$i][] = 'Rp. '.number_format($trx_ad_classic->trx_order_price);

                    $data[$i][] = '';
                    foreach ($trx_ad_classic->trxOrder->trxAdClassics as $key => $trxAdClassic) {
                        foreach ($trxAdClassic->schedules as $key => $schedule) {
                            $data[$i][count($data[$i]) - 1] .= sprintf('%s, <br/><br/>', Carbon::createFromFormat('Y-m-d H:i:s' ,$schedule->trx_ad_publish_date)->format('Y-m-d'));
                        }
                    }
                }


                $data[$i][] = $trx_ad_classic->trx_order_discount. '%';
                $data[$i][] = 'Rp. '.number_format($trx_ad_classic->trx_order_total);

                if($trx_ad_classic->trxOrder->members->count() > 0)
                {
                    $data[$i][] = $trx_ad_classic->author->name.' / '.$trx_ad_classic->trxOrder->members->first()->name;
                }
                else
                {
                    $data[$i][] = $trx_ad_classic->author->name;
                }
                $data[$i][] = $trx_ad_classic->trxOrder->mstAdStatus->status_name;
                $data[$i][] = $trx_ad_classic->order_created_at;
                $data[$i][] = $this->getActionTable($trx_ad_classic);
                $i++;
            }
        
        /*=====  End of Parsing Datatable  ======*/

        return ['data' => $data, 'draw' => (integer)$request->input('draw'), 'recordsTotal' => $recordsTotal, 'recordsFiltered' => $filteredTotal];
    }

    private function getActionTable($trx_ad_classic)
    {
        $view = View::make('ads::admin.'.$this->data['theme_cms']->value.'.content.TrxAdsClassic.service_master', [
            'trx_ad_classic' => $trx_ad_classic
        ]);

        $html = $view->render();
       
       return $html;
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create(Request $request)
    {
        $this->data['method'] = method_field('POST');
        $this->data['user'] = \Gdevilbat\SpardaCMS\Modules\Core\Entities\User::findOrFail(Auth::id());

        if(isset($_GET['code']))
        {
            /*try {
                $this->configMidtransPayment();

                $status = \Midtrans\Transaction::status(decrypt($_GET['code']));

                if(!empty($status))
                    return redirect(action('\\'.get_class(Route::current()->getController()).'@payment').'?code='.$_GET['code']);
            } catch(\Exception $e){
                if($e->getCode() != 404)
                    throw new \Exception($e->getMessage());
            }*/

            config()->set('database.connections.mysql.strict', false);
            \DB::reconnect();

            $this->data['trx_ad_classics'] = $this->trx_ads_classic_repository->with([
                                                                'trxOrder' => function($query){
                                                                    $query->select(\App\Models\TrxOrder::getPrimaryKey(), 'order_status_id', 'order_number');
                                                                },
                                                                'trxOrder.mstAdStatus' => function($query){
                                                                    $query->select(\App\Models\MstOrderStatus::getPrimaryKey(), 'status_name');
                                                                },
                                                                'author',
                                                                'trxOrder.trxAdClassics.schedules',
                                                                'trxOrder.trxAdClassics.city',
                                                                'trxOrder.trxAdClassics.rltAdCategory',
                                                                'trxOrder.trxAdClassics.rltTaxonomyAds.adTaxonomy.term',
                                                                'rltAdCategory',
                                                                'mstAdType'
                                                        ])
                                                        ->allWithBuilder()
                                                        ->where('order_id', decrypt($_GET['code']))
                                                        ->groupBy('order_id')
                                                        ->get();

            config()->set('database.connections.mysql.strict', true);
            \DB::reconnect();

            try {
                $this->data['type'] = MstAds_m::with(['categories.allChildrens' => function($query){
                    $query->orderBy('mst_ad_cat_name', 'ASC');
                }])->where(MstAds_m::getPrimaryKey(), $this->data['trx_ad_classics']->first()->rltAdCategory->ads_id)->firstOrFail();
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                throw new \Exception($e->getMessage());
            }

            $this->data['original_trx_ad_classics'] = $this->data['trx_ad_classics'];
            $this->data['method'] = method_field('PUT');
            $this->authorize('create-transaction-classic-ads');
        }
        else
        {
            try {
                $this->data['type'] = MstAds_m::with(['categories.allChildrens' => function($query){
                    $query->orderBy('mst_ad_cat_name', 'ASC');
                }])->where('mst_ad_slug', decrypt($request->input('ads')))->firstOrFail();
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                throw new \Exception($e->getMessage());
            }
        }

        return view('ads::admin.'.$this->data['theme_cms']->value.'.content.TrxAdsClassic.form', $this->data);
    }

    public function validation(Request $request)
    {
        $validator = $this->trx_ads_classic_repository->validateAdsContent($request);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        return response()->json([
                'status' => true,
                'code' => 200
        ]);
    }

    public function store(Request $request)
    {
        $validator = $this->trx_ads_classic_repository->validateAdsContent($request);

        if ($validator->fails()) {
            return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
        }

        $ads = $this->trx_ads_classic_repository->saveAds($request, Auth::user());

        if($ads->status)
        {
             return redirect(action('\Modules\Ads\Http\Controllers\TrxAdsClassicController@payment').'?code='.encrypt($ads->order_key))->with('global_message', array('status' => 200, 'message' => $ads->message));
        }
        else
        {
            return redirect()->back()->with('global_message', array('status' => 400, 'message' => $ads->message));
        }
    }

    public function payment(Request $request)
    {
        if($request->isMethod('GET'))
        {
            $this->data['trx_ad_classics'] = $this->trx_ads_classic_repository->with([
                                                                                    'trxOrder' => function($query){
                                                                                        $query->select(\App\Models\TrxOrder::getPrimaryKey(), 'order_status_id', 'order_number');
                                                                                    },
                                                                                    'trxOrder.mstAdStatus' => function($query){
                                                                                        $query->select(\App\Models\MstOrderStatus::getPrimaryKey(), 'status_name');
                                                                                    },
                                                                                    'rltAdCategory' => function($query){
                                                                                        $query->select(RltAdsCategories_m::getPrimaryKey(), 'ads_id', 'category_id');
                                                                                    },
                                                                                    'trxOrder.meta',
                                                                                    'mstAdType',
                                                                                    'rltAdCategory.mstAd',
                                                                                    'rltAdCategory.mstAdsCategory'
                                                                            ])
                                                                        ->allWithBuilder()
                                                                        ->where('order_id', decrypt($_GET['code']))
                                                                        ->get();

            return view('ads::admin.'.$this->data['theme_cms']->value.'.content.TrxAdsClassic.payment', $this->data);
        }
        else
        {
            $order = TrxOrder_m::findOrFail(decrypt($request->input('code')));

            $meta = TrxOrderMeta_m::where(['meta_key' => TrxOrderMeta_m::PAYMENT_INFO, 'trx_order_id' => $order->getKey()])->first();

            if(empty($meta))
                $meta = new TrxOrderMeta_m;

            $meta->meta_key = TrxOrderMeta_m::PAYMENT_INFO;
            $meta->meta_value = $request->input('payment_info');
            $meta->trx_order_id = $order->getKey();
            $meta->save();

            return redirect(action('\Modules\Payment\Http\Controllers\PaymentController@inCash').'?'.http_build_query(['code' => $request->input('code'), 'repository' => encrypt('\Modules\Ads\Repositories\PaymentAdsClassicRepository')]));
        }
    }

    public function invoice(Request $request)
    {
        $trx_order = TrxOrder_m::findOrFail(decrypt($request->input('code')));

       /* try {
            // Set your Merchant Server Key
            $this->configMidtransPayment();

            $status = \Midtrans\Transaction::status(decrypt($request->input('code')));


            try {
                $mst_order_status = MstOrderStatus_m::where('status_name', TrxOrder_m::TRX_PENDING)->firstOrFail();

                if($trx_order->order_status_id == $mst_order_status->getKey())
                {
                    if(($status->transaction_status == 'capture' || $status->transaction_status == 'settlement') && $status->fraud_status == 'accept')
                    {
                        $mst_order_status = MstOrderStatus_m::where('status_name', TrxOrder_m::TRX_CONFIRM)->firstOrFail();
                        $trx_order->order_status_id = $mst_order_status->getKey();
                        $trx_order->save();
                    }
                    elseif($status->transaction_status == 'pending')
                    {
                      if($status->payment_type == 'bank_transfer')
                      {
                          $message = 'Mohon Selesai kan Pembayaran Berikut : ';

                          foreach ($status->va_numbers as $key => $va_number) 
                          {
                            $message.= "<ul>";
                            $message.= "<li>Bank Name : ".$va_number->bank."</li>";
                            $message.= "<li>Virtual Number : ".$va_number->va_number."</li>";
                            $message.= "<li>Total : ".sprintf('%s %s', $status->currency, number_format($status->gross_amount))."</li>";
                            $message.= "</ul>";
                          }

                          $this->data['pending_message'] = $message;
                      }
                      elseif($status->payment_type == 'cstore')
                      {
                          $message = 'Mohon Selesai kan Pembayaran Berikut : ';

                          $message.= "<ul>";
                          $message.= "<li>Merchant : ".$status->store."</li>";
                          $message.= "<li>Payment Code : ".$status->payment_code."</li>";
                          $message.= "<li>Total : ".sprintf('%s %s', $status->currency, number_format($status->gross_amount))."</li>";
                          $message.= "</ul>";

                          $this->data['pending_message'] = $message;
                      }
                    }
                }
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                throw new \Exception($e->getMessage());
            }

        } catch (\Exception $e) {
            if($e->getCode() != 404)
                throw new \Exception($e->getMessage());
        }*/

        $this->data['trx_ad_classics'] = $this->trx_ads_classic_repository->with([
                                                                                'trxOrder' => function($query){
                                                                                    $query->select(\App\Models\TrxOrder::getPrimaryKey(), 'order_status_id', 'order_number');
                                                                                },
                                                                                'trxOrder.mstAdStatus' => function($query){
                                                                                    $query->select(\App\Models\MstOrderStatus::getPrimaryKey(), 'status_name');
                                                                                },
                                                                                'rltAdCategory' => function($query){
                                                                                    $query->select(RltAdsCategories_m::getPrimaryKey(), 'ads_id', 'category_id');
                                                                                },
                                                                                'trxOrder.meta',
                                                                                'mstAdType',
                                                                                'rltAdCategory.mstAd',
                                                                                'rltAdCategory.mstAdsCategory'
                                                                        ])
                                                                    ->allWithBuilder()
                                                                    ->where('order_id', decrypt($_GET['code']))
                                                                    ->withTrashed()
                                                                    ->get();

        //return $this->print_r($this->data['trx_ad_classics']->first()->trxOrder->meta->where('meta_key', 'confirmation')->first()->meta_value);

        return view('ads::admin.'.$this->data['theme_cms']->value.'.content.TrxAdsClassic.invoice', $this->data);
    }

    public function report(Request $request)
    {
        $this->validate($request, [
            'date_start' => 'date:Y-m-d|before_or_equal:'.$request->input('date_end'),
            'date_end' => 'date:Y-m-d',
            'agent' => 'required'
        ]);

        config()->set('database.connections.mysql.strict', false);
        \DB::reconnect();

         if($request->input('status') == 'all')
         {
            $status_id = MstOrderStatus_m::whereIn('status_name', [TrxOrder_m::TRX_DONE, TrxOrder_m::TRX_APPROVED, TrxOrder_m::TRX_EXPIRE, TrxOrder_m::TRX_CANCEL])->pluck(MstOrderStatus_m::getPrimaryKey());
         }
         else
         {
            $status_id = MstOrderStatus_m::whereIn('status_name', [$request->input('status')])->pluck(MstOrderStatus_m::getPrimaryKey());
         }

         $query = $this->trx_ads_classic_repository->with([
                            'trxOrder' => function($query){
                                $query->select(\App\Models\TrxOrder::getPrimaryKey(), 'order_status_id', 'order_number');
                            },
                            'trxOrder.mstAdStatus' => function($query){
                                $query->select(\App\Models\MstOrderStatus::getPrimaryKey(), 'status_name');
                            },
                            'trxOrder.trxAdClassics' => function($query){
                                $query->leftJoin(\Modules\Ads\Entities\ClassicAdsSchedule::getTableName(), \Modules\Ads\Entities\TrxAdsClassic::getTableName().'.'.\Modules\Ads\Entities\TrxAdsClassic::getPrimaryKey(), '=', \Modules\Ads\Entities\ClassicAdsSchedule::getTableName().'.trx_ad_classic_id')
                                      ->leftJoin(\App\Models\TrxOrder::getTableName(), \App\Models\TrxOrder::getTableName().'.'.\App\Models\TrxOrder::getPrimaryKey(), '=', \Modules\Ads\Entities\TrxAdsClassic::getTableName().'.order_id')
                                      ->select('*')
                                      ->addSelect(DB::Raw(\Modules\Ads\Entities\TrxAdsClassic::getTableName().'.'.'deleted_at as content_deleted_at, '.\Modules\Ads\Entities\ClassicAdsSchedule::getTableName().'.deleted_at as schedule_deleted_at,'.\App\Models\TrxOrder::getTableName().'.created_at as order_created_at,trx_order_price * trx_order_quantity * (100 - trx_order_discount) / 100 as trx_order_total'))
                                      ->withTrashed();
                            },
                            'author',
                            'trxOrder.meta',
                            'trxOrder.trxAdClassics.mstAdType',
                            'trxOrder.trxAdClassics.rltAdCategory',
                            'trxOrder.trxAdClassics.rltAdCategory.mstAd',
                            'trxOrder.trxAdClassics.rltAdCategory.mstAdsCategory',
                    ])
                ->allWithBuilder()
                ->whereIn('order_status_id', $status_id)
                ->where(function($query) use ($request){
                    $query->whereDate(TrxOrder_m::getTableName().'.created_at', '>=', $request->input('date_start'))
                        ->WhereDate(TrxOrder_m::getTableName().'.created_at', '<=', $request->input('date_end'));
                })
                ->withTrashed();

        if(Auth::user()->can('read-all-transaction-classic-ads') && $request->input('agent') != 'all')
        {
            $query = $query->where('created_by', decrypt($request->input('agent')));
        }

         $this->data['total_ads'] = $query->count();
         $this->data['agents'] = \App\User::whereIn('id', $query->pluck('created_by'))->get();
         $this->data['trx_ad_classics'] = $query->groupBy('order_id')->get();

        config()->set('database.connections.mysql.strict', true);
        \DB::reconnect();

        return view('ads::admin.'.$this->data['theme_cms']->value.'.content.TrxAdsClassic.report', $this->data);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy(Request $request)
    {
        $query = $this->trx_order_m->findOrFail(decrypt($request->input('order_id')));
        $this->authorize('create-transaction-classic-ads', $query);

        try {
            $mst_order_status = MstOrderStatus_m::where('status_name', TrxOrder_m::TRX_PENDING)->firstOrFail();

            if($query->order_status_id != $mst_order_status->getKey())
            {
                return redirect(action('\Modules\Ads\Http\Controllers\TrxAdsClassicController@index'))->with('global_message', array('status' => 200,'message' => 'Failed Delete Transaction, It\'s Has Been Paid!'));
            }
            else
            {
                try {
                    if($query->delete())
                    {
                        return redirect(action('\\'.get_class($this).'@index'))->with('global_message', array('status' => 200,'message' => 'Successfully Delete Transaction!'));
                    }
                    
                } catch (\Exception $e) {
                    return redirect(action('\\'.get_class($this).'@index'))->with('global_message', array('status' => 200,'message' => 'Failed Delete Transaction, It\'s Has Been Used!'));
                }
            }
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new \Exception($e->getMessage());
        }

    }

    public function forceDelete(Request $request)
    {
        $query = $this->trx_order_m->findOrFail(decrypt($request->input('order_id')));

        try {
            try {
                if($query->delete())
                {
                    return redirect(action('\\'.get_class($this).'@index'))->with('global_message', array('status' => 200,'message' => 'Successfully Delete Transaction!'));
                }
                
            } catch (\Exception $e) {
                return redirect(action('\\'.get_class($this).'@index'))->with('global_message', array('status' => 200,'message' => 'Failed Delete Transaction, It\'s Has Been Used!'));
            }
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function deleteContent(Request $request)
    {
        $trx_ad_classic = TrxAdsClassic_m::where(TrxAdsClassic_m::getPrimaryKey(), decrypt($request->input('id')))->first();

        if(empty($trx_ad_classic))
        {
            return response()->json(['code' => 400]);
        }
        else
        {
            $total = TrxAdsClassic_m::where('order_id', $trx_ad_classic->order_id)->count();

            if($total > 1)
            {
                if($trx_ad_classic->delete())
                    return response()->json(['code' => 200]);
            }
        }


        return response()->json(['code' => 503]);
    }

    public function deleteSchedule(Request $request)
    {
        $schedule = ClassicAdsSchedule_m::where(ClassicAdsSchedule_m::getPrimaryKey(), decrypt($request->input('id')))->first();

        if(empty($schedule))
        {
            return response()->json(['code' => 400]);
        }
        else
        {
            $total = ClassicAdsSchedule_m::where('trx_ad_classic_id', $schedule->trx_ad_classic_id)->count();

            if($total > 1)
            {
                if($schedule->delete())
                    return response()->json(['code' => 200]);
            }
        }


        return response()->json(['code' => 503]);
    }

    public function approvingPayment(Request $request)
    {
        $this->authorize('approving-payment-classic-ads');

        if($request->isMethod('GET'))
        {
            $this->data['trx_ad_classics'] = $this->trx_ads_classic_repository->with([
                                                                                'trxOrder' => function($query){
                                                                                    $query->select(\App\Models\TrxOrder::getPrimaryKey(), 'order_status_id', 'order_number');
                                                                                },
                                                                                'trxOrder.mstAdStatus' => function($query){
                                                                                    $query->select(\App\Models\MstOrderStatus::getPrimaryKey(), 'status_name');
                                                                                },
                                                                                'rltAdCategory' => function($query){
                                                                                    $query->select(RltAdsCategories_m::getPrimaryKey(), 'ads_id', 'category_id');
                                                                                },
                                                                                'trxOrder.meta',
                                                                                'mstAdType',
                                                                                'rltAdCategory.mstAd',
                                                                                'rltAdCategory.mstAdsCategory'
                                                                        ])
                                                                    ->allWithBuilder()
                                                                    ->where('order_id', decrypt($_GET['code']))
                                                                    ->get();

            return view('ads::admin.'.$this->data['theme_cms']->value.'.content.TrxAdsClassic.approving_payment', $this->data);
        }
        else
        {
           $trx_ad_classic = $this->trx_ads_classic_repository->allWithBuilder()
                                                    ->where('order_id', decrypt($request->input('code')))
                                                    ->firstOrFail();

           $trx_ad = TrxOrder_m::findOrFail($trx_ad_classic->order_id);

            if($request->isMethod('POST'))
            {

               try {
                    $mst_order_status = MstOrderStatus_m::where('status_name', TrxOrder_m::TRX_DONE)->firstOrFail();
                } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                    throw new \Exception($e->getMessage());
                }

                $trx_ad->order_status_id = $mst_order_status->getKey();
                $trx_ad->trx_done_date = Carbon::now();

                if($trx_ad->save())
                {
                    return redirect()->action('\Modules\Ads\Http\Controllers\TrxAdsClassicController@index')->with('global_message', array('status' => 200, 'message' => 'Success to Approving Ads Payment!'));
                }
                else
                {
                    return redirect()->action('\Modules\Ads\Http\Controllers\TrxAdsClassicController@index')->with('global_message', array('status' => 400, 'message' => 'Failed to Approving Ads Payment!'));
                }
            }
            elseif($request->isMethod('PUT'))
            {
                try {
                    $mst_order_status = MstOrderStatus_m::where('status_name', TrxOrder_m::TRX_CONFIRM)->firstOrFail();
                } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                    throw new \Exception($e->getMessage());
                }

                $trx_ad->order_status_id = $mst_order_status->getKey();
                $trx_ad->trx_done_date = null;

                if($trx_ad->save())
                {
                    return redirect()->action('\Modules\Ads\Http\Controllers\TrxAdsClassicController@index')->with('global_message', array('status' => 200, 'message' => 'Success to Cancel Ads Payment!'));
                }
                else
                {
                    return redirect()->action('\Modules\Ads\Http\Controllers\TrxAdsClassicController@index')->with('global_message', array('status' => 400, 'message' => 'Failed to Cancel Ads Payment!'));
                }
            }
            else
            {
                throw new \Exception("Method Not Defined");
            }
        }
    }

    public function approvingContent(Request $request)
    {
        $this->authorize('approving-content-classic-ads');

        if($request->isMethod('GET'))
        {
            config()->set('database.connections.mysql.strict', false);
            \DB::reconnect();

            $this->data['trx_ad_classics'] = $this->trx_ads_classic_repository->with([
                                                                            'trxOrder' => function($query){
                                                                                $query->select(\App\Models\TrxOrder::getPrimaryKey(), 'order_status_id', 'order_number');
                                                                            },
                                                                            'trxOrder.mstAdStatus' => function($query){
                                                                                $query->select(\App\Models\MstOrderStatus::getPrimaryKey(), 'status_name');
                                                                            },
                                                                            'author',
                                                                            'trxOrder.trxAdClassics.schedules',
                                                                            'trxOrder.trxAdClassics.city',
                                                                            'trxOrder.trxAdClassics.rltAdCategory.mstAd',
                                                                            'trxOrder.trxAdClassics.rltAdCategory.mstAdsCategory',
                                                                            'trxOrder.trxAdClassics.rltTaxonomyAds.adTaxonomy.term',
                                                                            'mstAdType',
                                                                    ])
                                                                ->allWithBuilder()
                                                                ->where('order_id', decrypt($_GET['code']))
                                                                ->groupBy('order_id')
                                                                ->get();

            config()->set('database.connections.mysql.strict', true);
            \DB::reconnect();

            return view('ads::admin.'.$this->data['theme_cms']->value.'.content.TrxAdsClassic.approving_content', $this->data);
        }
        else
        {
            $trx_order = $this->trx_order_m->findOrFail(decrypt($request->input(TrxOrder_m::getPrimaryKey())));

            if($request->isMethod('POST'))
            {
                $validator = Validator::make($request->all(), [
                    'ads.*.id' => 'required',
                    'ads.*.trx_ad_content' => 'required',
                ]);

                $validator->addRules([
                    'ads' => [
                        function ($attribute, $value, $fail) use ($request) {
                            foreach ($value as $key => $ad) {
                                $trx = TrxAdsClassic_m::with('rltAdCategory')->where(TrxAdsClassic_m::getPrimaryKey(), decrypt($request->input('ads')[$key][TrxAdsClassic_m::getPrimaryKey()]))->first();
                                $line = ceil($trx->trx_ad_order_total_char/$trx->rltAdCategory->char_on_line);
                                $line = $line > $trx->rltAdCategory->min_line ? $line : $trx->rltAdCategory->min_line;

                                if (ceil(strlen(utf8_decode(str_replace("\r\n", ' ', $ad['trx_ad_content'])))/$trx->rltAdCategory->char_on_line) > $line) {
                                    $fail($attribute.' '.($key+1).' Must Be Smaller Than '.$line.' Line');
                                }
                            }
                        },
                    ]
                ]);

                if ($validator->fails()) {
                    return redirect()->back()
                                ->withErrors($validator)
                                ->withInput();
                }


                try {
                    $mst_order_status = MstOrderStatus_m::where('status_name', TrxOrder_m::TRX_APPROVED)->firstOrFail();
                } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                    throw new \Exception($e->getMessage());
                }

                $trx_order->order_status_id = $mst_order_status->getKey();

                if($trx_order->save())
                {
                    foreach ($request->input('ads') as $key => $ads) {
                        $trx_ad_classic = TrxAdsClassic_m::where(TrxAdsClassic_m::getPrimaryKey(), decrypt($ads[TrxAdsClassic_m::getPrimaryKey()]))->firstOrFail();
                        $trx_ad_classic->trx_ad_order_total_char = strlen(utf8_decode(str_replace("\r\n", ' ', $ads['trx_ad_content'])));
                        $trx_ad_classic->trx_ad_content = $ads['trx_ad_content'];
                        if($trx_ad_classic->save())
                        {
                            $status = true;
                        }
                        else
                        {
                            $status = false;
                        }
                    }
                }

                if($status)
                {
                    return redirect(action('\\'.get_class($this).'@index'))->with('global_message', array('status' => 200,'message' => 'Successfully Editing Ads Content!'));
                }
                else
                {
                    return redirect()->back()->with('global_message', array('status' => 400, 'message' => 'Failed To Editing Ads Content!'));
                }

            }
            elseif ($request->isMethod('PUT')) 
            {
                try {
                    $mst_order_status = MstOrderStatus_m::where('status_name', TrxOrder_m::TRX_DONE)->firstOrFail();
                } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                    throw new \Exception($e->getMessage());
                }

                $trx_order->order_status_id = $mst_order_status->getKey();

                if($trx_order->save())
                {
                    return redirect()->action('\Modules\Ads\Http\Controllers\TrxAdsClassicController@index')->with('global_message', array('status' => 200, 'message' => 'Success to Cancel Content Approval!'));
                }
                else
                {
                    return redirect()->action('\Modules\Ads\Http\Controllers\TrxAdsClassicController@index')->with('global_message', array('status' => 400, 'message' => 'Failed to Cancel Content Approval!'));
                }
            }
            else
            {
                throw new \Exception("Method Not Defined");
            }
        }
    }

    public function layotingContent(Request $request)
    {
        $this->authorize('layouting-content-classic-ads');

        if($request->isMethod('GET'))
        {
            $this->data['trx_ad_classics'] = $this->trx_ads_classic_repository->with([
                                                        'trxOrder' => function($query){
                                                            $query->select(\App\Models\TrxOrder::getPrimaryKey(), 'order_status_id', 'order_number');
                                                        },
                                                        'trxOrder.trxAdClassics' => function($query){
                                                            $query->select(TrxAdsClassic_m::getPrimaryKey(), 'order_id');
                                                        },
                                                        'trxOrder.trxAdClassics.schedules',
                                                        'trxOrder.mstAdStatus' => function($query){
                                                            $query->select(\App\Models\MstOrderStatus::getPrimaryKey(), 'status_name');
                                                        },
                                                        'rltAdCategory' => function($query){
                                                            $query->select(RltAdsCategories_m::getPrimaryKey(), 'ads_id', 'category_id');
                                                        },
                                                        'mstAdType',
                                                        'rltAdCategory.mstAd',
                                                        'rltAdCategory.mstAdsCategory'
                                                ])
                                            ->allWithBuilder()
                                            ->where('order_id', decrypt($_GET['code']))
                                            ->whereNull(ClassicAdsSchedule_m::getTableName().'.deleted_at')
                                            ->addSelect(DB::raw(ClassicAdsSchedule_m::getTableName().'.'.ClassicAdsSchedule_m::getPrimaryKey().' as id_schedule'))
                                            ->get();

            return view('ads::admin.'.$this->data['theme_cms']->value.'.content.TrxAdsClassic.layouting_content', $this->data);
        }
        else
        {
            $trx_order = $this->trx_order_m->findOrFail(decrypt($request->input(TrxOrder_m::getPrimaryKey())));

            if($request->isMethod('POST'))
            {
                try {
                    $mst_order_status = MstOrderStatus_m::where('status_name', TrxOrder_m::TRX_EXPIRE)->firstOrFail();
                } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                    throw new \Exception($e->getMessage());
                }

                $trx_order->order_status_id = $mst_order_status->getKey();

                if($trx_order->save())
                {
                    return redirect(action('\\'.get_class($this).'@index'))->with('global_message', array('status' => 200,'message' => 'Successfully Closing Ads Session!'));
                }
                else
                {
                    return redirect()->back()->with('global_message', array('status' => 400, 'message' => 'Failed To Closing Ads Session!'));
                }
            }
            elseif($request->isMethod('PUT'))
            {
                try {
                    $mst_order_status = MstOrderStatus_m::where('status_name', TrxOrder_m::TRX_CANCEL)->firstOrFail();
                } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                    throw new \Exception($e->getMessage());
                }

                $trx_order->order_status_id = $mst_order_status->getKey();

                if($trx_order->save())
                {
                    return redirect(action('\\'.get_class($this).'@index'))->with('global_message', array('status' => 200,'message' => 'Successfully Cancel Ads!'));
                }
                else
                {
                    return redirect()->back()->with('global_message', array('status' => 400, 'message' => 'Failed To Cancel Ads!'));
                }
            }
            else
            {
                throw new \Exception("Method Not Defined");
            }

        }
    }

    public function layotingPrint()
    {
    	$mst_order_status = MstOrderStatus_m::whereIn('status_name', [TrxOrder_m::TRX_EXPIRE])->get()->pluck(MstOrderStatus_m::getPrimaryKey());

    	$this->data['ad_categories'] = RltAdsCategories_m::with([
    												'trxAdClassics' => function($query) use ($mst_order_status){
    													$query->whereHas('trxOrder', function($query) use ($mst_order_status){
		    														$query->whereIn('order_status_id', $mst_order_status);
		    													})
                                                                ->whereHas('schedules', function($query){
                                                                            $query->whereDate('trx_ad_publish_date', \Carbon\Carbon::now()->addDay()->format('Y-m-d'));
                                                                })
                                                                ->orderBy('trx_ad_content', 'ASC')
    															->select(TrxAdsClassic_m::getPrimaryKey(), 'order_id', 'trx_ad_content', 'rlt_trx_ads_category_id', 'mst_city_id');
    												},
                                                    'trxAdClassics.city',
                                                    'trxAdClassics.rltAdCategory.mstAd',
                                                    'trxAdClassics.rltTaxonomyAds.adTaxonomy.term'
											])
    										->whereHas('trxAdClassics', function($query) use ($mst_order_status){
	    										$query->whereHas('trxOrder', function($query) use ($mst_order_status){
                                                            $query->whereIn('order_status_id', $mst_order_status);
                                                        })
                                                      ->whereHas('schedules', function($query){
                                                            $query->whereDate('trx_ad_publish_date', \Carbon\Carbon::now()->addDay()->format('Y-m-d'));
                                                      });
	    									})
                                            ->leftJoin(MstAdCategories_m::getTableName(), function($join){
                                            	$join->on(MstAdCategories_m::getTableName().'.'.MstAdCategories_m::getPrimaryKey(), '=', RltAdsCategories_m::getTableName().'.category_id');
                                            })
                                            ->leftJoin(MstAds_m::getTableName(), function($join){
                                            	$join->on(MstAds_m::getTableName().'.'.MstAds_m::getPrimaryKey(), '=', RltAdsCategories_m::getTableName().'.ads_id');
                                            })
                                            ->select(RltAdsCategories_m::getPrimaryKey(), 'mst_ad_cat_name', 'mst_ad_name')
                                            ->orderBy('mst_ad_cat_name', 'ASC')
	    									->get();

        return view('ads::admin.'.$this->data['theme_cms']->value.'.content.TrxAdsClassic.layouting_print', $this->data); 
    }

    public function bulkApprovingContent(Request $request)
    {
        $this->validate($request, [
            'code' => 'required'
        ]);

        try {
            $order_approved_status = MstOrderStatus_m::where('status_name', TrxOrder_m::TRX_APPROVED)->firstOrFail();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new \Exception($e->getMessage());
        }

        try {
            $order_expire_status = MstOrderStatus_m::where('status_name', TrxOrder_m::TRX_EXPIRE)->firstOrFail();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new \Exception($e->getMessage());
        }

        $id = TrxOrder_m::whereIn(TrxOrder_m::getPrimaryKey(), explode(',', $request->input('code')))
                         ->where('order_status_id', $order_approved_status->getKey())
                         ->pluck(TrxOrder_m::getPrimaryKey());

        $status = DB::table(TrxOrder_m::getTableName())
                        ->whereIn(TrxOrder_m::getPrimaryKey(), $id)
                        ->update(['order_status_id' => $order_expire_status->getKey()]);

        return redirect(action('\\'.get_class($this).'@index'))->with('global_message', array('status' => 200,'message' => 'Successfully Close Session Ads!'));
    }
}
