<div class="col">
    <div class="btn-group">
        <a href="javascript:void(0)" class="btn btn-outline-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Pilihan
        </a>
        <div class="dropdown-menu dropdown-menu-right">
            @can('create-transaction-classic-ads', $trx_ad_classic)
                <button class="dropdown-item" type="button">
                    <a class="m-link m-link--state m-link--info" href="{{action('\Modules\Ads\Http\Controllers\TrxAdsClassicController@create').'?code='.encrypt($trx_ad_classic->order_id)}}"><i class="fa fa-edit"> Sunting</i></a>
                </button>
            @endcan
            @if($trx_ad_classic->trxOrder->mstAdStatus->status_name == \App\Models\TrxOrder::TRX_PENDING)
                @canany(['create-transaction-classic-ads', 'approving-payment-classic-ads'])
                    <button class="dropdown-item" type="button">
                            <a class="m-link m-link--state m-link--success" href="{{action('\Modules\Ads\Http\Controllers\TrxAdsClassicController@payment').'?code='.encrypt($trx_ad_classic->order_id)}}"><i class="fa fa-money-bill-wave"> Lakukan Pembayaran</i></a>
                    </button>
                @endcan
            @endif
            @can('approving-payment-classic-ads')
                @if($trx_ad_classic->trxOrder->mstAdStatus->status_name == \App\Models\TrxOrder::TRX_CONFIRM)
                    <button class="dropdown-item" type="button">
                            <a class="m-link m-link--state m-link--danger" href="{{action('\Modules\Ads\Http\Controllers\TrxAdsClassicController@approvingPayment').'?code='.encrypt($trx_ad_classic->order_id)}}"><i class="fa fa-dollar-sign"> Setujui Pembayaran</i></a>
                    </button>
                @endif
                @if($trx_ad_classic->trxOrder->mstAdStatus->status_name == \App\Models\TrxOrder::TRX_DONE)
                    <form action="{{action('\Modules\Ads\Http\Controllers\TrxAdsClassicController@approvingPayment')}}" method="post" accept-charset="utf-8">
                        {{csrf_field()}}
                        {{method_field('PUT')}}
                        <input type="hidden" name="code" value="{{encrypt($trx_ad_classic->order_id)}}">
                        <button class="dropdown-item" type="submit">
                            <span class="m-link m-link--state m-link--danger" style="cursor: pointer;">
                                <i class="fa fa-times"> Batalkan Pembayaran</i>
                            </span>
                        </button>  
                    </form>
                @endif
            @endcan
            @can('approving-content-classic-ads')
                @if($trx_ad_classic->trxOrder->mstAdStatus->status_name == \App\Models\TrxOrder::TRX_DONE)
                        <button class="dropdown-item" type="button">
                                <a class="m-link m-link--state m-link--warning" href="{{action('\Modules\Ads\Http\Controllers\TrxAdsClassicController@approvingContent').'?code='.encrypt($trx_ad_classic->order_id)}}"><i class="fa fa-check"> Setujui Konten Iklan</i></a>
                        </button>
                @endif
                @if($trx_ad_classic->trxOrder->mstAdStatus->status_name == \App\Models\TrxOrder::TRX_APPROVED)
                    <form action="{{action('\Modules\Ads\Http\Controllers\TrxAdsClassicController@approvingContent')}}" method="post" accept-charset="utf-8">
                        {{csrf_field()}}
                        {{method_field('PUT')}}
                        <input type="hidden" name="{{\App\Models\TrxOrder::getPrimaryKey()}}" value="{{encrypt($trx_ad_classic->order_id)}}">
                        <button class="dropdown-item" type="submit">
                            <span class="m-link m-link--state m-link--danger" style="cursor: pointer;">
                                <i class="fa fa-times"> Batalkan Status yang Disetujui Editor</i>
                            </a>
                        </button>  
                    </form>
                @endif
            @endcan
            @can('layouting-content-classic-ads')
                @if($trx_ad_classic->trxOrder->mstAdStatus->status_name == \App\Models\TrxOrder::TRX_APPROVED)
                        <button class="dropdown-item" type="button">
                                <a class="m-link m-link--state m-link--focus" href="{{action('\Modules\Ads\Http\Controllers\TrxAdsClassicController@layotingContent').'?code='.encrypt($trx_ad_classic->order_id)}}"><i class="fa fa-eye"> Layout</i></a>
                        </button>
                @endif
                @if($trx_ad_classic->trxOrder->mstAdStatus->status_name == \App\Models\TrxOrder::TRX_EXPIRE)
                    <form action="{{action('\Modules\Ads\Http\Controllers\TrxAdsClassicController@layotingContent')}}" method="post" accept-charset="utf-8">
                        {{csrf_field()}}
                        {{method_field('PUT')}}
                        <input type="hidden" name="{{\App\Models\TrxOrder::getPrimaryKey()}}" value="{{encrypt($trx_ad_classic->order_id)}}">
                        <button class="dropdown-item" type="submit" onclick="return window.confirm('Apakah anda yakin ingin membatalkan iklan ini ?')">
                            <span class="m-link m-link--state m-link--danger" style="cursor: pointer;">
                                <i class="fa fa-times"> Batalkan Iklan</i>
                            </a>
                        </button>  
                    </form>
                @endif
            @endcan
            @if($trx_ad_classic->trxOrder->mstAdStatus->status_name != \App\Models\TrxOrder::TRX_PENDING)
                @canany(['approving-payment-classic-ads', 'create-transaction-classic-ads'])
                    <button class="dropdown-item" type="button">
                            <a class="m-link m-link--state m-link--primary" href="{{action('\Modules\Ads\Http\Controllers\TrxAdsClassicController@invoice').'?code='.encrypt($trx_ad_classic->order_id)}}"><i class="fa fa-file-invoice-dollar"> Cetak Invoice</i></a>
                    </button>
                @endcan
            @endif
            @can('create-transaction-classic-ads', $trx_ad_classic)
                <form action="{{action('\Modules\Ads\Http\Controllers\TrxAdsClassicController@destroy')}}" method="post" accept-charset="utf-8">
                    {{method_field('DELETE')}}
                    {{csrf_field()}}
                    <input type="hidden" name="order_id" value="{{encrypt($trx_ad_classic->order_id)}}">
                </form>
                <button class="dropdown-item confirm-delete" type="button"><a class="m-link m-link--state m-link--accent" data-toggle="modal" href="#small"><i class="fa fa-trash"> Hapus</i></a></button>
            @endcan
            @can('super-access')
                <form action="{{action('\Modules\Ads\Http\Controllers\TrxAdsClassicController@forceDelete')}}" method="post" accept-charset="utf-8">
                    {{method_field('DELETE')}}
                    {{csrf_field()}}
                    <input type="hidden" name="order_id" value="{{encrypt($trx_ad_classic->order_id)}}">
                </form>
                <button class="dropdown-item confirm-delete" type="button"><a class="m-link m-link--state m-link--danger" data-toggle="modal" href="#small"><i class="fa fa-times"> Hapus Paksa</i></a></button>
            @endcan
        </div>
    </div>
</div>