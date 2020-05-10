<div class="col-11 border">
    <div id="body_content">
        <div id="invoice">
            <div class="col-12 row">
                <div class="col-sm-2 px-0">
                    <img src="{{url('public/img/logo.png')}}" alt="Poskota" class="company-logo w-100" />           
                </div>
                <div class="col px-0">
                    <div class="logo-info"> 
                        <div class="minifont" > 
                            <div class="fn org"><strong>PT. Media Antarkota Jaya (POSKOTA)</strong></div> 
                            <div class="adr"> 
                                <div class="street-address">Jl. Gajah Mada No. 98 - 100, Jakarta Barat 11140, INDONESIA</div> 
                            </div> <br>
                            <!-- adr --> 
                            <div class="email">Email : iklan@poskotanews.com</div> 
                            <div id="sales-phone-number">Telepon : +62-21-634 6417 / +62-21-633 3128 / +62-21-634 5824</div> 
                        </div>  
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <br/> 
        <div class="row">
             <!-- #invoice-header  -->
            <div class="col-sm-6 ">
                <!-- #invoice-info -->  
                    <div class="vcard" id="client-details"> 
                        <div class="tagihan"> <strong>Sudah Terima Dari</strong></div>
                        <table width="100%" border="0">
                            <tr>
                                <td width="25%">Nama Agen </td>
                                <td>: 
                                    {{AgentRepository::isAgent($trx_ad_classics->first()->author) ? 'Agen' : 'Biro Pusat'}} / {{$trx_ad_classics->first()->author->name}}
                                </td>
                            </tr>
                            <tr>
                                <td width="25%" valign="top">Alamat </td>
                                <td>: {{AgentRepository::isAgent($trx_ad_classics->first()->author) ? 'Agen' : 'Biro Pusat'}}   </td>
                            </tr>
                            <tr>
                                <td width="25%">Telp </td>
                                <td>:                       / /                 </td>
                            </tr>
                            <tr>
                                <td>Email </td>
                                <td>: {{$trx_ad_classics->first()->author->email}}   </td></td>
                            </tr>
                        </table>
                        @if(($trx_ad_classics->first()->trxOrder->meta->where('meta_key', \App\Models\TrxOrderMeta::KEY_CONFIRMATION)->count() > 0))
                            <div class="my-2">
                                <img src="{{generate_storage_url(json_decode(json_encode($trx_ad_classics->first()->trxOrder->meta->where('meta_key', \App\Models\TrxOrderMeta::KEY_CONFIRMATION)->first()->meta_value))->confirmation)}}" class="img-fluid" alt="">
                            </div>
                        @endif
                    </div>
            </div>
             <div class="col mt-3 mt-md-0  d-md-flex d-print-flex justify-content-end">
                <div id="invoice-info"> 
                    <h4>No. KW : <strong>#{{$trx_ad_classics->first()->trxOrder->invoice_number}}</strong></h4> 
                    <p id="payment-due">
                        Tanggal Pesan : 
                        {{$trx_ad_classics->first()->order_created_at}}        
                    </p> 
                    <p style="font-weight:bold;">
                        Status : {{$trx_ad_classics->first()->trxOrder->mstAdStatus->status_name}}          
                    </p> 
                    <p>
                        @if(($trx_ad_classics->first()->trxOrder->meta->where('meta_key', \App\Models\TrxOrderMeta::PAYMENT_INFO)->count() > 0))
                            Keterangan :
                            <ul>
                                @foreach ($trx_ad_classics->first()->trxOrder->meta->where('meta_key', \App\Models\TrxOrderMeta::PAYMENT_INFO)->first()->meta_value as $meta_value)
                                    <li>
                                        @if(strpos($meta_value, 'Rp') !== false)
                                            Rp. {{number_format((integer) preg_replace('/[Rp.,_]/', '',$meta_value))}}
                                        @else
                                            @if(empty($meta_value))
                                                -
                                            @else
                                                {{$meta_value}}
                                            @endif
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                        @if(($trx_ad_classics->first()->trxOrder->meta->where('meta_key', \App\Models\TrxOrderMeta::KEY_CONFIRMATION)->count() > 0))
                            Keterangan :
                            @php
                                $info_confirmations = json_decode(json_encode($trx_ad_classics->first()->trxOrder->meta->where('meta_key', \App\Models\TrxOrderMeta::KEY_CONFIRMATION)->first()->meta_value))
                            @endphp
                            <ul>
                                <li>
                                    {{property_exists($info_confirmations, 'sender_name') ? $info_confirmations->sender_name : '-'}}
                                </li>
                                <li>
                                    {{property_exists($info_confirmations, 'sender_bank') ? $info_confirmations->sender_bank : '-'}}
                                </li>
                                <li>
                                    {{property_exists($info_confirmations, 'sender_account') ? $info_confirmations->sender_account : '-'}}
                                </li>
                                <li>
                                    Rp. {{property_exists($info_confirmations, 'sender_nominal') ? $info_confirmations->sender_nominal : '-'}}
                                </li>
                                <li>
                                    {{property_exists($info_confirmations, 'message') ? $info_confirmations->message : '-'}}
                                </li>
                            </ul>
                        @endif
                    </p>
                </div> 
            </div>
        </div>
       
        
        <br/>
        <div class="table-responsive">
            <table class="table table-stripped" id="invoice-amount"> 
                <thead> 
                    <tr class="text-white bg-dar" id="header_row"> 
                        <th class="jenis_th" style="background-color: #343a40 !important;color: #fff !important;">Jenis</th> 
                        <th class="konten_th" style="background-color: #343a40 !important;color: #fff !important;">Tipe</th> 
                        <th class="tanggal_th" style="background-color: #343a40 !important;color: #fff !important;">Kategori</th> 
                        <th class="tanggal_th" style="background-color: #343a40 !important;color: #fff !important;">Kar</th> 
                        <th class="tanggal_th" style="background-color: #343a40 !important;color: #fff !important;">Isi Iklan</th> 
                        <th class="tanggal_th" style="background-color: #343a40 !important;color: #fff !important;">Tgl Terbit</th> 
                        <th class="harga_th" style="background-color: #343a40 !important;color: #fff !important;">Harga (Rp)</th> 
                    </tr> 
                </thead> 
                <tbody>
                    @foreach ($trx_ad_classics as $trx_ad_classic)
                        <tr  class="item odd" > 
                            <td class="item_l">{{$trx_ad_classic->rltAdCategory->mstAd->mst_ad_name}}</td> 
                            <td class="item_l">{{$trx_ad_classic->mstAdType->mst_ad_type_name}}</td> 
                            <td class="item_l">{{$trx_ad_classic->rltAdCategory->mstAdsCategory->mst_ad_cat_name}}</td> 
                            <td class="item_l">{{$trx_ad_classic->trx_ad_order_total_char}}</td> 
                            <td class="item_detail">
                                {!!preg_replace('/^[^\s]+\s+[^\s]+|^[^\s]+/', '<span style="text-transform: uppercase;"><b>$0</b></span>', str_replace('&nbsp;', " " , $trx_ad_classic->trx_ad_content))!!}<br>
                                @if(!empty($trx_ad_classic->content_deleted_at))
                                    <em>Konten Dihapus {{$trx_ad_classic->content_deleted_at}}</em>
                                @endif
                                 @if(!empty($trx_ad_classic->schedule_deleted_at))
                                    <em>Konten Dihapus {{$trx_ad_classic->schedule_deleted_at}}</em>
                                @endif
                            </td>
                            <td class="item_detail">{{\Carbon\Carbon::createFromFormat('Y-m-d H:i:s',$trx_ad_classic->trx_ad_publish_date)->format('d-m-Y')}}</td> 
                            <td class="item_r">{{number_format($trx_ad_classic->trx_ad_order_price)}}</td> 
                        </tr> 
                    @endforeach
                </tbody>            
                <tr > <td colspan="7" style="background-color:#cccccc !important;" >&nbsp;</td></tr>
                <tr id="net_total_tr"> 
                    <td colspan="4" class="item_l">Jumlah Iklan : <b>{{count($trx_ad_classics)}}</b></td> 
                    <td colspan="2" class="item_r">Subtotal</td> 
                    <td class="item_r">Rp. {{number_format($trx_ad_classic->trx_order_price * $trx_ad_classic->trx_order_quantity)}}</td> 
                </tr> 
                <tr id="discount_tr"> 
                    <td colspan="4">&nbsp;</td> 
                    <td colspan="2" class="item_r">Diskon &nbsp; <b> {{$trx_ad_classics->first()->trx_order_discount}} % </b> </td> 
                    <td class="item_r">- Rp. {{number_format($trx_ad_classic->trx_order_price*$trx_ad_classics->first()->trx_order_discount/100)}}</td> 
                </tr>
                <tr id="total_tr"> 
                    <td colspan="4">&nbsp;</td> 
                    <td colspan="2" class="total">
                        <b>Total Bayar Rp. </b> 
                    </td> 
                    <td class="total">{{number_format($trx_ad_classic->trx_order_total)}}</td> 
                </tr>
                <tfoot> 
                    <tr > <td colspan="7" style="font-size:0.7em;" ><br>Tanggal Cetak : {{\Carbon\Carbon::now()->format('d-m-Y H:i:s')}} - BACKUP</td></tr>
                </tfoot> 
            </table>
        </div>
        <!-- invoice-amount --> 
    </div> 
</div>