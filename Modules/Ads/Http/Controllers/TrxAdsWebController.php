<?php

namespace Modules\Ads\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

use Gdevilbat\SpardaCMS\Modules\Core\Http\Controllers\CoreController;
use Gdevilbat\SpardaCMS\Modules\Core\Repositories\Repository;
use Modules\Ads\Repositories\TrxAdsWebRepository;
use Modules\Ads\Repositories\TrxOrderRepository;

use Modules\Ads\Entities\MstAdsPosition as MstAdsPosition_m;
use Modules\Ads\Entities\TrxAdsWeb as TrxAdsWeb_m;

use App\Models\TrxOrder as TrxOrder_m;
use App\Models\MstOrderStatus as MstOrderStatus_m;

use Modules\Payment\Traits\MidtransPayment;

use App\Models\NotificationPayment as NotificationPayment_m;

use \Carbon\Carbon;
use Validator;
use DB;
use Auth;
use View;
use Route;
use Storage;
use Str;

class TrxAdsWebController extends CoreController
{
    use MidtransPayment;

    public function __construct(TrxAdsWebRepository $trx_ads_web_repository, TrxOrderRepository $trx_order_repository)
    {
        parent::__construct();
        $this->trx_order_m = new TrxOrder_m;
        $this->trx_order_repository = $trx_order_repository;
        $this->trx_ads_web_repository = $trx_ads_web_repository;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if(Auth::user()->can('read-all-transaction-web-ads') || Auth::user()->can('create-transaction-web-ads') || Auth::user()->can('approving-payment-web-ads') || Auth::user()->can('approving-content-web-ads') || Auth::user()->can('layouting-content-web-ads'))
        {
            $this->data['agents_report'] = \Gdevilbat\SpardaCMS\Modules\Core\Entities\User::whereHas('role', function($query){
                                                    $query->where('slug', 'agen-iklan')
                                                        ->orWhere('slug', 'staff-iklan');
                                        })->get();
            return view('ads::admin.'.$this->data['theme_cms']->value.'.content.TrxAdsWeb.master', $this->data);
        }
        else
        {
            abort(403);
        }

    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $this->data['method'] = method_field('POST');
        $this->data['mst_ads_position'] = MstAdsPosition_m::get();
        if(isset($_GET['code']))
        {
             try {
                $this->configMidtransPayment();

                $status = \Midtrans\Transaction::status(decrypt($_GET['code']));

                if(!empty($status))
                    return redirect(action('\\'.get_class(Route::current()->getController()).'@payment').'?code='.$_GET['code']);
            } catch(\Exception $e){
                if($e->getCode() != 404)
                    throw new \Exception($e->getMessage());
            }

            $this->data['trx_ad_web'] = $this->trx_ads_web_repository->with('mstAdPosition')
                                                                    ->allWithBuilder()
                                                                    ->where('order_id', decrypt($_GET['code']))
                                                                    ->firstOrFail();
            $this->data['method'] = method_field('PUT');
            $this->authorize('create-transaction-web-ads', $this->data['trx_ad_web']);
        }

        return view('ads::admin.'.$this->data['theme_cms']->value.'.content.TrxAdsWeb.form', $this->data);
    }

     public function serviceMaster(Request $request)
    {
        $column = ['order_id', 'ads_position', 'trx_order_quantity', 'trx_order_price', 'trx_order_total', 'trx_ad_publish_date','status_name','order_created_at'];

        $length = !empty($request->input('length')) ? $request->input('length') : 10 ;
        $column = !empty($request->input('order.0.column')) ? $column[$request->input('order.0.column')] : TrxAdsWeb_m::getTableName().'.'.TrxAdsWeb_m::getPrimaryKey() ;
        $dir = !empty($request->input('order.0.dir')) ? $request->input('order.0.dir') : 'DESC' ;
        $searchValue = $request->input('search')['value'];

        $query = $this->trx_ads_web_repository->with([
                                                        'author',
                                                        'mstAdPosition' => function($query){
                                                            $query->select(MstAdsPosition_m::getPrimaryKey(),'ads_position');
                                                        },
                                                        'trxOrder' => function($query){
                                                            $query->select(\App\Models\TrxOrder::getPrimaryKey(), 'order_status_id', 'order_number');
                                                        },
                                                        'trxOrder.mstAdStatus' => function($query){
                                                            $query->select(\App\Models\MstOrderStatus::getPrimaryKey(), 'status_name');
                                                        }
                                                ])
                                              ->allWithBuilder()
                                              ->orderBy($column, $dir);

        $recordsTotal = $query->count();
        $filtered = $query;

        if($searchValue)
        {
            $filtered->where(function($query) use ($searchValue){
                    $query->where(DB::raw("CONCAT(order_id,'-',ifnull(order_number, ''),'-',ads_position,'-', trx_order_price,'-', status_name, trx_ad_publish_date,'-',"."'-',".TrxOrder_m::getTableName().".created_at)"), 'like', "%".$searchValue."%")
                        ->orWhereRaw('(trx_order_price * trx_order_quantity * (100 - trx_order_discount) / 100) like '."'%".$searchValue."%'");

            });
        }

        $filteredTotal = $filtered->count();

        $this->data['length'] = $length;
        $this->data['column'] = $column;
        $this->data['dir'] = $dir;
        $this->data['trx_ads_web'] = $filtered->offset($request->input('start'))->limit($length)->get();

        /*=========================================
        =            Parsing Datatable            =
        =========================================*/
            
            $data = array();
            $i = 0;
            foreach ($this->data['trx_ads_web'] as $key_user => $trx_ad_web) 
            {
                $data[$i][] = $trx_ad_web->trxOrder->invoice_number;
                $data[$i][] = $trx_ad_web->mstAdPosition->ads_position;
                $data[$i][] = $trx_ad_web->trx_order_quantity;
                $data[$i][] = 'Rp. '.number_format($trx_ad_web->trx_order_price);
                $data[$i][] = 'Rp. '.number_format($trx_ad_web->trx_order_total);

                $data[$i][] = Carbon::createFromFormat('Y-m-d H:i:s', $trx_ad_web->trx_ad_publish_date)->format('Y-m-d');
                $data[$i][count($data[$i]) - 1] .= "<br/> - <br/>";
                $data[$i][count($data[$i]) - 1] .= Carbon::createFromFormat('Y-m-d H:i:s', $trx_ad_web->trx_ad_end_date)->format('Y-m-d');

                $data[$i][] = $trx_ad_web->author->name;
                $data[$i][] = $trx_ad_web->trxOrder->mstAdStatus->status_name;
                $data[$i][] = $trx_ad_web->order_created_at;
                $data[$i][] = $this->getActionTable($trx_ad_web);
                $i++;
            }
        
        /*=====  End of Parsing Datatable  ======*/

        return ['data' => $data, 'draw' => (integer)$request->input('draw'), 'recordsTotal' => $recordsTotal, 'recordsFiltered' => $filteredTotal];
    }

    private function getActionTable($trx_ad_web)
    {
        $view = View::make('ads::admin.'.$this->data['theme_cms']->value.'.content.TrxAdsWeb.service_master', [
            'trx_ad_web' => $trx_ad_web
        ]);

        $html = $view->render();
       
       return $html;
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ads_title' => 'required|max:191',
            'trx_ad_publish_date' => 'required|date_format:Y-m-d',
            'trx_ad_end_date' => 'required|date_format:Y-m-d|after_or_equal:trx_ad_publish_date',
            'ads_action' => 'required|url',
        ]);

        $validator->addRules([
            'path_ads' => 'max:500|mimes:jpeg,jpg,bmp,png,gif'
        ]);

        if($request->isMethod('POST'))
        {
            $validator->addRules([
                'path_ads' => 'required'
            ]);
        }

        if ($validator->fails()) {
            return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
        }

        if($request->isMethod('POST'))
        {
            $data = $request->except('_token', '_method');
            $trx_order = new $this->trx_order_m;
        }
        else
        {
            $data = $request->except('_token', '_method', TrxOrder_m::getPrimaryKey());
            $trx_order = $this->trx_order_m->findOrFail(decrypt($request->input(TrxOrder_m::getPrimaryKey())));
            $this->authorize('update-master-ads', $trx_order);
        }

        try {
            $ads_position = MstAdsPosition_m::findOrFail($request->input('mst_ads_position_id'));
            $mst_order_status = MstOrderStatus_m::where('status_name', 'Menunggu')->firstOrFail();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new \Exception($e->getMessage());
        }

        $publish_date = Carbon::createFromFormat('Y-m-d', $request->input('trx_ad_publish_date'));
        $end_date = Carbon::createFromFormat('Y-m-d', $request->input('trx_ad_end_date'));

        $trx_order->trx_order_quantity = $end_date->diff($publish_date)->days + 1;
        $trx_order->trx_order_price = $ads_position->price;
        $trx_order->order_status_id = $mst_order_status->getKey();

        if($request->isMethod('POST'))
        {
            $trx_order->trx_order_date = Carbon::now();
            $trx_order->order_number = $this->trx_order_repository->getOrderNumber();
        }

        if($trx_order->save())
        {

            $trx_ad_web = TrxAdsWeb_m::where('order_id', $trx_order->getKey())->first();

            if(empty($trx_ad_web))
                $trx_ad_web = new TrxAdsWeb_m;

            $trx_ad_web->order_id = $trx_order->getKey();
            $trx_ad_web->ads_title  = $request->input('ads_title');
            $trx_ad_web->trx_ad_publish_date  = $request->input('trx_ad_publish_date');
            $trx_ad_web->trx_ad_end_date  = $request->input('trx_ad_end_date');
            $trx_ad_web->mst_ads_position_id  = $request->input('mst_ads_position_id');
            $trx_ad_web->ads_action  = $request->input('ads_action');

            if($request->hasFile('path_ads'))
            {
                $file = $request->file('path_ads')->getClientOriginalName();
                $filename = pathinfo($file, PATHINFO_FILENAME);
                $extension = pathinfo($file, PATHINFO_EXTENSION);
                $path = $request->file('path_ads')->storeAs('Web/Ads'.'/'.Carbon::now()->format('Y/m'),  Str::slug(md5(microtime()).'-'.$filename, '-').'.'.$extension);

                if(!empty($trx_ad_web->path_ads) && $path != $trx_ad_web->path_ads)
                {
                    Storage::delete($trx_ad_web->path_ads);
                }

                $trx_ad_web->path_ads = $path;
            }

            if($request->isMethod('POST'))
            {
                $trx_ad_web->created_by = Auth::id();
                $trx_ad_web->modified_by = Auth::id();
            }
            else
            {
                $trx_ad_web->modified_by = Auth::id();
            }


            if($trx_order->trxAdWeb()->save($trx_ad_web))
            {
                if($request->isMethod('POST'))
                {
                    return redirect(action('\Modules\Ads\Http\Controllers\TrxAdsWebController@payment').'?code='.encrypt($trx_order->getKey()))->with('global_message', array('status' => 200,'message' => 'Successfully Add Web Ads Transaction!'));
                }
                else
                {
                    return redirect(action('\Modules\Ads\Http\Controllers\TrxAdsWebController@payment').'?code='.encrypt($trx_order->getKey()))->with('global_message', array('status' => 200,'message' => 'Successfully Update Web Ads Transaction!'));
                }
            }
            else
            {
                if($request->isMethod('POST'))
                {
                    return redirect()->back()->with('global_message', array('status' => 400, 'message' => 'Failed To Add Web Ads Transaction!'));
                }
                else
                {
                    return redirect()->back()->with('global_message', array('status' => 400, 'message' => 'Failed To Update Web Ads Transaction!'));
                }
            }
        }
        else
        {
            if($request->isMethod('POST'))
            {
                return redirect()->back()->with('global_message', array('status' => 400, 'message' => 'Failed To Add Ads Transaction!'));
            }
            else
            {
                return redirect()->back()->with('global_message', array('status' => 400, 'message' => 'Failed To Update Ads Transaction!'));
            }
        }
    }

    
    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy(Request $request)
    {
        $query = $this->trx_order_m->findOrFail(decrypt($request->input('order_id')));
        $this->authorize('create-transaction-web-ads', $query);

        try {
            $mst_order_status = MstOrderStatus_m::where('status_name', 'Menunggu')->firstOrFail();

            if($query->order_status_id != $mst_order_status->getKey() && false)
            {
                return redirect(action('\Modules\Ads\Http\Controllers\TrxAdsWebController@index'))->with('global_message', array('status' => 200,'message' => 'Failed Delete Transaction, It\'s Has Been Paid!'));
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

    public function payment(Request $request)
    {
        $this->data['trx_ad_web'] = $this->trx_ads_web_repository->with([
                                                                                'mstAdPosition' => function($query){
                                                                                    $query->select(MstAdsPosition_m::getPrimaryKey(),'ads_position');
                                                                                },
                                                                                'trxOrder' => function($query){
                                                                                    $query->select(\App\Models\TrxOrder::getPrimaryKey(), 'order_status_id', 'order_number');
                                                                                },
                                                                                'trxOrder.mstAdStatus' => function($query){
                                                                                    $query->select(\App\Models\MstOrderStatus::getPrimaryKey(), 'status_name');
                                                                                }
                                                                        ])
                                                                    ->allWithBuilder()
                                                                    ->where('order_id', decrypt($_GET['code']))
                                                                    ->firstOrFail();

        return view('ads::admin.'.$this->data['theme_cms']->value.'.content.TrxAdsWeb.payment', $this->data);
    }

    public function invoice(Request $request)
    {
        $trx_order = TrxOrder_m::findOrFail(decrypt($request->input('code')));

        try {
            // Set your Merchant Server Key
            $this->configMidtransPayment();

            $status = \Midtrans\Transaction::status(decrypt($request->input('code')));


            try {
                $mst_order_status = MstOrderStatus_m::where('status_name', 'Menunggu')->firstOrFail();

                if($trx_order->order_status_id == $mst_order_status->getKey())
                {
                    if(($status->transaction_status == 'capture' || $status->transaction_status == 'settlement') && $status->fraud_status == 'accept')
                    {
                        $mst_order_status = MstOrderStatus_m::where('status_name', 'Pembayaran Dikonfirmasi')->firstOrFail();
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
        }

        $this->data['trx_ad_web'] = $this->trx_ads_web_repository->with([
                                                                                'mstAdPosition' => function($query){
                                                                                    $query->select(MstAdsPosition_m::getPrimaryKey(),'ads_position');
                                                                                },
                                                                                'trxOrder' => function($query){
                                                                                    $query->select(\App\Models\TrxOrder::getPrimaryKey(), 'order_status_id', 'order_number');
                                                                                },
                                                                                'trxOrder.mstAdStatus' => function($query){
                                                                                    $query->select(\App\Models\MstOrderStatus::getPrimaryKey(), 'status_name');
                                                                                },
                                                                                'trxOrder.notificationPayments'
                                                                        ])
                                                                    ->allWithBuilder()
                                                                    ->where('order_id', $trx_order->getKey())
                                                                    ->firstOrFail();

        return view('ads::admin.'.$this->data['theme_cms']->value.'.content.TrxAdsWeb.invoice', $this->data);
    }

    public function approvingPayment(Request $request)
    {
        $this->authorize('approving-payment-web-ads');

        if($request->isMethod('GET'))
        {
            $this->data['trx_ad_web'] = $this->trx_ads_web_repository->with([
                                                                                'mstAdPosition' => function($query){
                                                                                    $query->select(MstAdsPosition_m::getPrimaryKey(),'ads_position');
                                                                                },
                                                                                'trxOrder' => function($query){
                                                                                    $query->select(\App\Models\TrxOrder::getPrimaryKey(), 'order_status_id', 'order_number');
                                                                                },
                                                                                'trxOrder.mstAdStatus' => function($query){
                                                                                    $query->select(\App\Models\MstOrderStatus::getPrimaryKey(), 'status_name');
                                                                                }
                                                                        ])
                                                                    ->allWithBuilder()
                                                                    ->where('order_id', decrypt($_GET['code']))
                                                                    ->firstOrFail();

            return view('ads::admin.'.$this->data['theme_cms']->value.'.content.TrxAdsWeb.approving_payment', $this->data);
        }
        else
        {
           $trx_ad_web = $this->trx_ads_web_repository->allWithBuilder()
                                                    ->where('order_id', decrypt($request->input('code')))
                                                    ->firstOrFail();

           $trx_ad = TrxOrder_m::findOrFail($trx_ad_web->order_id);
           $trx_ad->trx_done_date = Carbon::now();

           try {
                $mst_order_status = MstOrderStatus_m::where('status_name', 'Lunas')->firstOrFail();
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                throw new \Exception($e->getMessage());
            }

            $trx_ad->order_status_id = $mst_order_status->getKey();

            if($trx_ad->save())
            {
                return redirect()->action('\Modules\Ads\Http\Controllers\TrxAdsWebController@index')->with('global_message', array('status' => 200, 'message' => 'Success to Approving Ads Payment!'));
            }
            else
            {
                return redirect()->action('\Modules\Ads\Http\Controllers\TrxAdsWebController@index')->with('global_message', array('status' => 400, 'message' => 'Failed to Approving Ads Payment!'));
            }
        }
    }

    public function approvingContent(Request $request)
    {
        $this->authorize('approving-content-web-ads');

        if($request->isMethod('GET'))
        {
            $this->data['mst_ads_position'] = MstAdsPosition_m::get();
            $this->data['trx_ad_web'] = $this->trx_ads_web_repository->with([
                                                                                'mstAdPosition' => function($query){
                                                                                    $query->select(MstAdsPosition_m::getPrimaryKey(),'ads_position');
                                                                                },
                                                                                'trxOrder' => function($query){
                                                                                    $query->select(\App\Models\TrxOrder::getPrimaryKey(), 'order_status_id', 'order_number');
                                                                                },
                                                                                'trxOrder.mstAdStatus' => function($query){
                                                                                    $query->select(\App\Models\MstOrderStatus::getPrimaryKey(), 'status_name');
                                                                                }
                                                                        ])
                                                                    ->allWithBuilder()
                                                                    ->where('order_id', decrypt($_GET['code']))
                                                                    ->firstOrFail();

            return view('ads::admin.'.$this->data['theme_cms']->value.'.content.TrxAdsWeb.approving_content', $this->data);
        }
        else
        {
            $validator = Validator::make($request->all(), [
                'trx_ad_publish_date' => 'required|date_format:Y-m-d',
                'trx_ad_end_date' => 'required|date_format:Y-m-d|after_or_equal:trx_ad_publish_date',
                'path_ads' => [
                    function ($attribute, $value, $fail) use ($request) {
                            if (Storage::exists('Web/Ads'.'/'.Carbon::now()->format('Y/m').'/'.$request->file('path_ads')->getClientOriginalName())) {
                                $fail($attribute.' Is Exist. Please Use Another Filename.');
                            }
                    },
                ]
            ]);

            $validator->addRules([
                'path_ads' => 'max:500|mimes:jpeg,jpg,bmp,png,gif'
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                            ->withErrors($validator)
                            ->withInput();
            }

            $data = $request->except('_token', '_method', TrxOrder_m::getPrimaryKey());
            $trx_order = $this->trx_order_m->findOrFail(decrypt($request->input(TrxOrder_m::getPrimaryKey())));

            try {
                $mst_order_status = MstOrderStatus_m::where('status_name', 'Approved Editor')->firstOrFail();
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                throw new \Exception($e->getMessage());
            }

            $trx_order->order_status_id = $mst_order_status->getKey();

            if($trx_order->save())
            {

                $trx_ad_web = TrxAdsWeb_m::where('order_id', $trx_order->getKey())->firstOrFail();
                $trx_ad_web->order_id = $trx_order->getKey();
                $trx_ad_web->trx_ad_publish_date  = $request->input('trx_ad_publish_date');

                if($request->hasFile('path_ads'))
                {
                    $path = $request->file('path_ads')->storeAs('Web/Ads'.'/'.Carbon::now()->format('Y/m').'/', $request->file('path_ads')->getClientOriginalName());

                    if(!empty($trx_ad_web->path_ads) && $path != $trx_ad_web->path_ads)
                    {
                        Storage::delete($trx_ad_web->path_ads);
                    }

                    $trx_ad_web->path_ads = $path;
                }

                if($trx_ad_web->save())
                {
                    return redirect(action('\\'.get_class($this).'@index'))->with('global_message', array('status' => 200,'message' => 'Successfully Approving Ads Content!'));
                }
                else
                {
                    return redirect()->back()->with('global_message', array('status' => 400, 'message' => 'Failed To Approving Ads Content!'));
                }
            }
            else
            {
                return redirect()->back()->with('global_message', array('status' => 400, 'message' => 'Failed To Approving Ads Content!'));
            }
        }
    }

    public function layotingContent(Request $request)
    {
        $this->authorize('layouting-content-web-ads');

        if($request->isMethod('GET'))
        {
            $this->data['mst_ads_position'] = MstAdsPosition_m::get();
            $this->data['trx_ad_web'] = $this->trx_ads_web_repository->with([
                                                                                'mstAdPosition' => function($query){
                                                                                    $query->select(MstAdsPosition_m::getPrimaryKey(),'ads_position');
                                                                                },
                                                                                'trxOrder' => function($query){
                                                                                    $query->select(\App\Models\TrxOrder::getPrimaryKey(), 'order_status_id', 'order_number');
                                                                                },
                                                                                'trxOrder.mstAdStatus' => function($query){
                                                                                    $query->select(\App\Models\MstOrderStatus::getPrimaryKey(), 'status_name');
                                                                                },
                                                                                'author.agent'
                                                                        ])
                                                                    ->allWithBuilder()
                                                                    ->where('order_id', decrypt($_GET['code']))
                                                                    ->firstOrFail();

            return view('ads::admin.'.$this->data['theme_cms']->value.'.content.TrxAdsWeb.layouting_content', $this->data);
        }
        else
        {
            $data = $request->except('_token', '_method', TrxOrder_m::getPrimaryKey());
            $trx_order = $this->trx_order_m->findOrFail(decrypt($request->input(TrxOrder_m::getPrimaryKey())));

            try {
                $mst_order_status = MstOrderStatus_m::where('status_name', 'Sesi Ditutup')->firstOrFail();
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
    }

    public function report(Request $request)
    {
        $this->validate($request, [
            'date_start' => 'date:Y-m-d|before_or_equal:'.$request->input('date_end'),
            'date_end' => 'date:Y-m-|before:'.Carbon::now()->format('Y-m-d'),
            'agent' => 'required'
        ]);

        $status_id = MstOrderStatus_m::whereIn('status_name', ['Pembayaran Dikonfirmasi', 'Lunas', 'Approved Editor', 'Sesi Ditutup'])->pluck(MstOrderStatus_m::getPrimaryKey());

        config()->set('database.connections.mysql.strict', false);
        \DB::reconnect();

        $query = $this->trx_ads_web_repository->with([
                                                'mstAdPosition' => function($query){
                                                    $query->select(MstAdsPosition_m::getPrimaryKey(),'ads_position');
                                                },
                                                'trxOrder' => function($query){
                                                    $query->select(\App\Models\TrxOrder::getPrimaryKey(), 'order_status_id', 'order_number');
                                                },
                                                'trxOrder.mstAdStatus' => function($query){
                                                    $query->select(\App\Models\MstOrderStatus::getPrimaryKey(), 'status_name');
                                                },
                                                'trxOrder.notificationPayments'
                                        ])
                                    ->allWithBuilder()
                                    ->where(function($query) use ($request){
                                        $query->whereDate(TrxOrder_m::getTableName().'.created_at', '>=', $request->input('date_start'))
                                            ->WhereDate(TrxOrder_m::getTableName().'.created_at', '<=', $request->input('date_end'));
                                    });

        if(Auth::user()->can('read-all-transaction-classic-ads') && $request->input('agent') != 'all')
        {
            $query = $query->where('created_by', decrypt($request->input('agent')));
        }


        $this->data['agents'] = \App\User::whereIn('id', $query->pluck('created_by'))->get();

        config()->set('database.connections.mysql.strict', true);
        \DB::reconnect();

        $this->data['trx_ad_webs'] = $query->get();

        return view('ads::admin.'.$this->data['theme_cms']->value.'.content.TrxAdsWeb.report', $this->data);
    }
}
