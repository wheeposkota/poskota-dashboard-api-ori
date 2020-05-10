<?php

namespace App\Listeners;

use Exception;
use App\Events\TransactionSettled;
use App\Models\EPaperSubscription;
use App\Models\TrxOrder;
use App\Models\TrxOrderMeta;

class ProcessSettledTransaction
{
    public function handle(TransactionSettled $event)
    {
        $trx = TrxOrder::findOrFail($event->order_id);

        $is_epaper = TrxOrderMeta::query()
            ->where('trx_order_id', $trx->getKey())
            ->where('meta_key', TrxOrderMeta::KEY_TYPE)
            ->where('meta_value', json_encode(TrxOrderMeta::TYPE_EPAPER))
            ->first();

        if ($is_epaper)
            $this->processEpaperSubs($trx);

        //other types
    }

    private function processEpaperSubs(TrxOrder $trx)
    {
        $payload = TrxOrderMeta::query()
            ->where('trx_order_id', $trx->getKey())
            ->where('meta_key', TrxOrderMeta::PAYLOAD_EPAPER)
            ->firstOrFail();

        try {
            EPaperSubscription::create($payload->meta_value);
        } catch (Exception $e) {
            report($e);
        }
    }
}