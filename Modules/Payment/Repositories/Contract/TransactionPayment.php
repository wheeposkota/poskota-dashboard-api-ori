<?php

namespace  Modules\Payment\Repositories\Contract;

use Illuminate\Database\Eloquent\Builder;

/**
 * Interface CoreRepository
 * @package Modules\Core\Repositories
 */
interface TransactionPayment
{
    public function __construct(\App\Models\TrxOrder $model);
    public function getTrxOrder();
    public function getRedirectUrl();
    public function getErrorPendingUrl();
    public function getErrorExpireUrl();
    public function getErrorSettlement();
}
