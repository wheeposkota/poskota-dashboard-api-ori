<?php

namespace Modules\Ads\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

use App\Models\TrxOrder as TrxOrder_m;
use App\Models\MstOrderStatus as MstOrderStatus_m;
use Modules\Ads\Entities\TrxAdsWeb as TrxAdsWeb_m;

use Route;

class TransactionAds
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if($request->has('code'))
        {
            try {
                $mst_order_status = MstOrderStatus_m::where('status_name', 'Menunggu')->firstOrFail();
                $trx = TrxOrder_m::findOrFail(decrypt($request->input('code')));

                if($trx->order_status_id != $mst_order_status->getKey())
                    return redirect(action('\\'.get_class(Route::current()->getController()).'@invoice').'?code='.$request->input('code'));
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException  $e) {
                throw new \Exception($e->getMessage());
            }
        }

        return $next($request);
    }
}
