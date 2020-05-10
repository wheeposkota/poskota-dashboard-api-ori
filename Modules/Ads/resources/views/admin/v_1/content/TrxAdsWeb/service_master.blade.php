<div class="col">
    <div class="btn-group">
        <a href="javascript:void(0)" class="btn btn-outline-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Actions
        </a>
        <div class="dropdown-menu dropdown-menu-left">
            @can('create-transaction-web-ads', $trx_ad_web)
                <button class="dropdown-item" type="button">
                    <a class="m-link m-link--state m-link--info" href="{{action('\Modules\Ads\Http\Controllers\TrxAdsWebController@create').'?code='.encrypt($trx_ad_web->order_id)}}"><i class="fa fa-edit"> Edit</i></a>
                </button>
            @endcan
            @if($trx_ad_web->trxOrder->mstAdStatus->status_name == 'Menunggu')
                @canany(['create-transaction-web-ads'])
                    <button class="dropdown-item" type="button">
                            <a class="m-link m-link--state m-link--danger" href="{{action('\Modules\Ads\Http\Controllers\TrxAdsWebController@payment').'?code='.encrypt($trx_ad_web->order_id)}}"><i class="fa fa-dollar-sign"> Make Payment</i></a>
                    </button>
                @endcan
            @endif
            @if($trx_ad_web->trxOrder->mstAdStatus->status_name == 'Pembayaran Dikonfirmasi')
                @can('approving-payment-web-ads')
                    <button class="dropdown-item" type="button">
                            <a class="m-link m-link--state m-link--danger" href="{{action('\Modules\Ads\Http\Controllers\TrxAdsWebController@approvingPayment').'?code='.encrypt($trx_ad_web->order_id)}}"><i class="fa fa-dollar-sign"> Approving Payment</i></a>
                    </button>
                @endcan
            @endif
            @if($trx_ad_web->trxOrder->mstAdStatus->status_name == 'Lunas')
                @can('approving-content-web-ads')
                    <button class="dropdown-item" type="button">
                            <a class="m-link m-link--state m-link--warning" href="{{action('\Modules\Ads\Http\Controllers\TrxAdsWebController@approvingContent').'?code='.encrypt($trx_ad_web->order_id)}}"><i class="fa fa-check"> Approving Content</i></a>
                    </button>
                @endcan
            @endif
            @if($trx_ad_web->trxOrder->mstAdStatus->status_name == 'Approved Editor')
                @can('layouting-content-web-ads')
                    <button class="dropdown-item" type="button">
                            <a class="m-link m-link--state m-link--focus" href="{{action('\Modules\Ads\Http\Controllers\TrxAdsWebController@layotingContent').'?code='.encrypt($trx_ad_web->order_id)}}"><i class="fa fa-eye"> Preview Content</i></a>
                    </button>
                @endcan
            @endif
            @if($trx_ad_web->trxOrder->mstAdStatus->status_name != 'Menunggu')
                @canany(['approving-payment-web-ads', 'create-transaction-web-ads'])
                    <button class="dropdown-item" type="button">
                            <a class="m-link m-link--state m-link--primary" href="{{action('\Modules\Ads\Http\Controllers\TrxAdsWebController@invoice').'?code='.encrypt($trx_ad_web->order_id)}}"><i class="fa fa-file-invoice-dollar"> Print Invoice</i></a>
                    </button>
                @endcan
            @endif
            @can('create-transaction-web-ads', $trx_ad_web)
                <form action="{{action('\Modules\Ads\Http\Controllers\TrxAdsWebController@destroy')}}" method="post" accept-charset="utf-8">
                    {{method_field('DELETE')}}
                    {{csrf_field()}}
                    <input type="hidden" name="order_id" value="{{encrypt($trx_ad_web->order_id)}}">
                </form>
                <button class="dropdown-item confirm-delete" type="button"><a class="m-link m-link--state m-link--accent" data-toggle="modal" href="#small"><i class="fa fa-trash"> Delete</i></a></button>
            @endcan
        </div>
    </div>
</div>