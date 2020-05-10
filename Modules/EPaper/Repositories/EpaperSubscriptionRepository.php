<?php

namespace Modules\EPaper\Repositories;

use Gdevilbat\SpardaCMS\Modules\Core\Repositories;
use Gdevilbat\SpardaCMS\Modules\Core\Repositories\AbstractRepository;

use Auth;

/**
 * Class EloquentCoreRepository
 *
 * @package Gdevilbat\SpardaCMS\Modules\Core\Repositories\Eloquent
 */
class EpaperSubscriptionRepository extends AbstractRepository
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

        if (method_exists($this->model, 'translations')) {
            return $this->model->with('translations');
        }

        return $this->model;
    }

    private function getTrxOrder($model)
    {
        return $model->leftJoin(\App\Models\TrxOrder::getTableName(), \App\Models\TrxOrder::getTableName().'.'.\App\Models\TrxOrder::getPrimaryKey(), '=', \Modules\EPaper\Entities\Subscription::getTableName().'.order_id')
                        ->leftJoin(\Modules\EPaper\Entities\Package::getTableName(), \Modules\EPaper\Entities\Package::getTableName().'.'.\Modules\EPaper\Entities\Package::getPrimaryKey(), '=', \Modules\EPaper\Entities\Subscription::getTableName().'.package_id')
                        ->leftJoin(\App\Models\MstOrderStatus::getTableName(), \App\Models\TrxOrder::getTableName().'.'.'order_status_id', '=', \App\Models\MstOrderStatus::getTableName().'.'.\App\Models\MstOrderStatus::getPrimaryKey())
                        ->leftJoin(\App\Models\Member::getTableName(), \Modules\EPaper\Entities\Subscription::getTableName().'.'.'member_id', '=', \App\Models\Member::getTableName().'.'.\App\Models\Member::getPrimaryKey())
                        ->selectRaw(\Modules\EPaper\Entities\Subscription::getTableName().'.*, '.\App\Models\TrxOrder::getTableName().'.*, '.\Modules\EPaper\Entities\Subscription::getTableName().'.'.\Modules\EPaper\Entities\Subscription::getPrimaryKey().' as id_subscription, '.\Modules\EPaper\Entities\Package::getTableName().'.package_name, '.\App\Models\Member::getTableName().'.name, '.\App\Models\TrxOrder::getTableName().'.created_at as order_created_at,trx_order_price * trx_order_quantity * (100 - trx_order_discount) / 100 as trx_order_total');
    }
}
