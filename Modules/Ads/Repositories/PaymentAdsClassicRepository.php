<?php

namespace Modules\Ads\Repositories;

use Gdevilbat\SpardaCMS\Modules\Core\Repositories;

/**
 * Class EloquentCoreRepository
 *
 * @package Gdevilbat\SpardaCMS\Modules\Core\Repositories\Eloquent
 */
class PaymentAdsClassicRepository implements \Modules\Payment\Repositories\Contract\TransactionPayment
{
    public function __construct(\App\Models\TrxOrder $model)
    {
        $this->model = $model;
    }

    public function getTrxOrder()
    {
        $this->model->getKey();
    }

    public function getRedirectUrl()
    {
        return action('\Modules\Ads\Http\Controllers\TrxAdsClassicController@invoice').'?code='.encrypt($this->model->getKey());
    }

    public function getErrorPendingUrl()
    {
        return action('\Modules\Ads\Http\Controllers\TrxAdsClassicController@invoice').'?code='.encrypt($this->model->getKey());
    }

    public function getErrorSettlement()
    {
        return action('\Modules\Ads\Http\Controllers\TrxAdsClassicController@invoice').'?code='.encrypt($this->model->getKey());
    }

    public function getErrorExpireUrl()
    {
        return action('\Modules\Ads\Http\Controllers\TrxAdsClassicController@index');
    }
}
