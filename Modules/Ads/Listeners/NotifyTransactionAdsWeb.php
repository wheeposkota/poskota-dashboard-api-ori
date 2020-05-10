<?php

namespace Modules\Ads\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Models\MstOrderStatus as MstOrderStatus_m;
use App\Models\TrxOrder as TrxOrder_m;
use Modules\Ads\Entities\TrxAdsWeb as TrxAdsWeb_m;

class NotifyTransactionAdsWeb
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $trx_web = TrxAdsWeb_m::where('order_id', $event->notification->order_id)->first(['order_id']);

        if(!empty($trx_web))
        {
            $trx_order = TrxOrder_m::findOrFail($trx_web->order_id);

            try {
                $mst_order_status = MstOrderStatus_m::where('status_name', 'Menunggu')->firstOrFail();

                if($trx_order->order_status_id == $mst_order_status->getKey())
                {
                    if(($event->notification->transaction_status == 'capture' || $event->notification->transaction_status == 'settlement') && $event->notification->fraud_status == 'accept')
                    {
                        $mst_order_status = MstOrderStatus_m::where('status_name', 'Pembayaran Dikonfirmasi')->firstOrFail();
                        $trx_order->order_status_id = $mst_order_status->getKey();
                        $trx_order->save();
                    }
                }
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                throw new \Exception($e->getMessage());
            }
        }
    }
}
