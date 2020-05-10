<?php

namespace Modules\Ads\Repositories;

use Gdevilbat\SpardaCMS\Modules\Core\Repositories;
use Gdevilbat\SpardaCMS\Modules\Core\Repositories\AbstractRepository;

use App\Models\MstOrderStatus as MstOrderStatus_m;
use Modules\Ads\Entities\TrxAdsWeb as TrxAdsWeb_m;

use Auth;

/**
 * Class EloquentCoreRepository
 *
 * @package Gdevilbat\SpardaCMS\Modules\Core\Repositories\Eloquent
 */
class TrxAdsWebRepository extends AbstractRepository
{
    /**
     * @inheritdoc
     */
    public function all()
    {
        $this->model = $this->getTrxOrder($this->model);

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
        $this->model = $this->getTrxOrder($this->model);

        /*==========================================
        =            User Authorization            =
        ==========================================*/
        
            if(Auth::user()->can('read-all-transaction-web-ads'))
            {
                $this->model = $this->model;
            }
            else
            {

                /*$this->model = $this->model->where(function($query){
                                            $query->where(TrxAdsWeb_m::getTableName().'.created_by', Auth::id())
                                                    ->orWhereHas('author.agent.users', function($query){
                                                        $query->where(\Gdevilbat\SpardaCMS\Modules\Core\Entities\User::getTableName().'.id', Auth::id());
                                                    }); /* Get Data By Created User 
                });*/

                if(Auth::user()->can('create-transaction-web-ads'))
                {
                    $this->model = $this->model = $this->model->where(function($query){
                                            $query->where(TrxAdsWeb_m::getTableName().'.created_by', Auth::id())
                                                    ->orWhereHas('author.agent.users', function($query){
                                                        $query->where(\Gdevilbat\SpardaCMS\Modules\Core\Entities\User::getTableName().'.id', Auth::id());
                                                    }); /* Get Data By Created User */
	                });
                }
                else
                {
                    $this->model = $this->model->where(function($query){
				                        if(Auth::user()->can('approving-payment-web-ads'))
				                        {
				                            try {
				                                $mst_order_status = MstOrderStatus_m::where('status_name', 'Pembayaran Dikonfirmasi')->firstOrFail();
				                            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
				                                throw new \Exception($e->getMessage());
				                            }

				                            $query = $query->orWhere('order_status_id', $mst_order_status->getKey());
				                        }
				                        
				                        if(Auth::user()->can('approving-content-web-ads'))
				                        {
				                            try {
				                                $mst_order_status = MstOrderStatus_m::where('status_name', 'Lunas')->firstOrFail();
				                            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
				                                throw new \Exception($e->getMessage());
				                            }

				                            $query = $query->orWhere('order_status_id', $mst_order_status->getKey());
				                        }

				                        if(Auth::user()->can('layouting-content-web-ads'))
				                        {
				                            try {
				                                $mst_order_status = MstOrderStatus_m::where('status_name', 'Approved Editor')->firstOrFail();
				                            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
				                                throw new \Exception($e->getMessage());
				                            }

				                            $query = $query->orWhere('order_status_id', $mst_order_status->getKey());
				                        }

				                        return $query;

                    });
                }
                
            }
        
        /*=====  End of User Authorization  ======*/
        

        if (method_exists($this->model, 'translations')) {
            return $this->model->with('translations');
        }

        return $this->model;
    }

    private function getTrxOrder($model)
    {
        return $model->leftJoin(\App\Models\TrxOrder::getTableName(), \App\Models\TrxOrder::getTableName().'.'.\App\Models\TrxOrder::getPrimaryKey(), '=', \Modules\Ads\Entities\TrxAdsWeb::getTableName().'.order_id')
                        ->leftJoin(\Modules\Ads\Entities\MstAdsPosition::getTableName(), \Modules\Ads\Entities\TrxAdsWeb::getTableName().'.'.'mst_ads_position_id', '=', \Modules\Ads\Entities\MstAdsPosition::getTableName().'.'.\Modules\Ads\Entities\MstAdsPosition::getPrimaryKey())
                        ->leftJoin(\App\Models\MstOrderStatus::getTableName(), \App\Models\TrxOrder::getTableName().'.'.'order_status_id', '=', \App\Models\MstOrderStatus::getTableName().'.'.\App\Models\MstOrderStatus::getPrimaryKey())
                        ->selectRaw(\Modules\Ads\Entities\TrxAdsWeb::getTableName().'.*, '.\App\Models\TrxOrder::getTableName().'.*, '.\Modules\Ads\Entities\TrxAdsWeb::getTableName().'.'.\Modules\Ads\Entities\TrxAdsWeb::getPrimaryKey().' as id_web_ads, '.\App\Models\TrxOrder::getTableName().'.created_at as order_created_at,trx_order_price * trx_order_quantity * (100 - trx_order_discount) / 100 as trx_order_total');
    }
}
