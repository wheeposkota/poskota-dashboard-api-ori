<?php

namespace Modules\EPaper\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Gdevilbat\SpardaCMS\Modules\Core\Http\Controllers\CoreController;

use Modules\EPaper\Entities\Subscription as Subscription_m;
use App\Models\TrxOrder as TrxOrder_m;

use \Carbon\Carbon;
use View;
use DB;

class SubscriptionController extends CoreController
{
    public function __construct(\Modules\EPaper\Repositories\EpaperSubscriptionRepository $subscription)
    {
        parent::__construct();
        $this->subscription = $subscription;   
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('epaper::admin.'.$this->data['theme_cms']->value.'.content.Subscription.master', $this->data);
    }

     public function serviceMaster(Request $request)
    {
        $column = ['order_id', 'name', 'package_name', 'trx_order_total', 'start_date','end_date','status_name','order_created_at'];

        $length = !empty($request->input('length')) ? $request->input('length') : 10 ;
        $column = !empty($request->input('order.0.column')) ? $column[$request->input('order.0.column')] : Subscription_m::getTableName().'.'.Subscription_m::getPrimaryKey() ;
        $dir = !empty($request->input('order.0.dir')) ? $request->input('order.0.dir') : 'DESC' ;
        $searchValue = $request->input('search')['value'];

        $query = $this->subscription->with([
                                                'trxOrder' => function($query){
                                                    $query->select(\App\Models\TrxOrder::getPrimaryKey(), 'order_status_id');
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
            $filtered->where(DB::raw("CONCAT(order_id,'-',name,'-',package_name,'-',start_date,'-',end_date,'-',status_name,"."'-',".TrxOrder_m::getTableName().".created_at)"), 'like', "%".$searchValue."%")
                    ->orWhereRaw('(trx_order_price * trx_order_quantity * (100 - trx_order_discount) / 100) like '."'%".$searchValue."%'");
        }

        $filteredTotal = $filtered->count();

        $this->data['length'] = $length;
        $this->data['column'] = $column;
        $this->data['dir'] = $dir;
        $this->data['subscriptions'] = $filtered->offset($request->input('start'))->limit($length)->get();


        /*=========================================
        =            Parsing Datatable            =
        =========================================*/
            
            $data = array();
            $i = 0;
            foreach ($this->data['subscriptions'] as $key_user => $subscription) 
            {
                $data[$i][] = sprintf('<a class=".subscription" href="javascript:void(0)" onclick="Subscription.getData(\'%s\')">%s</a>', $subscription->order_id, $subscription->order_id);
                $data[$i][] = $subscription->name;
                $data[$i][] = $subscription->package_name;
                $data[$i][] = 'Rp. '.number_format($subscription->trx_order_price);

                $data[$i][] = $subscription->trxOrder->mstAdStatus->status_name;
                $data[$i][] = $subscription->order_created_at;
                $i++;
            }
        
        /*=====  End of Parsing Datatable  ======*/

        return ['data' => $data, 'draw' => (integer)$request->input('draw'), 'recordsTotal' => $recordsTotal, 'recordsFiltered' => $filteredTotal];
    }

    private function getActionTable($subscription)
    {
        $view = View::make('epaper::admin.'.$this->data['theme_cms']->value.'.content.Subscription.service_master', [
            'subscription' => $subscription
        ]);

        $html = $view->render();
       
       return $html;
    }

    public function getData(Request $request)
    {
        $data = $this->subscription->with([
                    'trxOrder' => function($query){
                        $query->select(\App\Models\TrxOrder::getPrimaryKey(), 'order_status_id');
                    },
                    'trxOrder.mstAdStatus' => function($query){
                        $query->select(\App\Models\MstOrderStatus::getPrimaryKey(), 'status_name');
                    }
                ])
              ->allWithBuilder()
              ->where('order_id', $request->input('order_id'))
              ->first();

        if(!empty($data))
        {
            $data->trx_order_total = number_format($data->trx_order_total);
            $data->start_date = Carbon::createFromFormat('Y-m-d H:i:s', $data->start_date)->format('Y-m-d');
            $data->end_date = Carbon::createFromFormat('Y-m-d H:i:s', $data->end_date)->format('Y-m-d');

            $response = [
                'status' => 200,
                'data' => $data
            ];
        }
        else
        {
            $response = [
                'status' => 400,
                'data' => []
            ];
        }

        return $response;
    }
}
