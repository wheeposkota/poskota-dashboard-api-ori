<div class="col-11 border">
    <div class="m-form__section m-form__section--first">
        <div class="col-12">
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
                    <div class="d-flex justify-content-center col-md-6">
                        <img src="{{generate_storage_url($trx_ad_web->path_ads)}}" class="img-fluid d-print-none align-self-center">
                    </div>
                     <div class="col mt-2 mt-md-0">
                        <div id="invoice-info"> 
                            <h4>No. KW : <strong>#{{$trx_ad_web->trxOrder->invoice_number}}</strong></h4> 
                            <p id="payment-due">
                                Tanggal Pesan : 
                                {{$trx_ad_web->order_created_at}}        
                            </p> 
                            <p style="font-weight:bold;">
                                Status : {{$trx_ad_web->trxOrder->mstAdStatus->status_name}}
                            </p> 
                        </div> 
                    </div>
                </div>
               
                
                <br/>
                <div class="table-responsive">
                    <table class="table table-stripped" id="invoice-amount"> 
                        <thead> 
                            <tr class="text-white bg-dark" id="header_row"> 
                                <th class="jenis_th" style="background-color: #343a40 !important;color: #fff !important;">Jenis</th> 
                                <th class="konten_th" style="background-color: #343a40 !important;color: #fff !important;">File iklan</th> 
                                <th class="tanggal_th" style="background-color: #343a40 !important;color: #fff !important;">Tgl Terbit</th> 
                                <th class="harga_th" style="background-color: #343a40 !important;color: #fff !important;">Harga (Rp)</th> 
                            </tr>
                        </thead>
                        <tbody> 
                            <tr  class="item odd" > 
                                <td class="item_l">Iklan Website</td> 
                                <td class="item_detail">{{$trx_ad_web->file_path}}</td> 
                                <td class="item_detail">{{\Carbon\Carbon::createFromFormat('Y-m-d H:i:s',$trx_ad_web->trx_ad_publish_date)->format('d/m/Y')}} - {{\Carbon\Carbon::createFromFormat('Y-m-d H:i:s',$trx_ad_web->trx_ad_end_date)->format('d/m/Y')}}</td> 
                                <td class="item_r">{{number_format($trx_ad_web->trx_order_price)}}</td> 
                            </tr> 
                        </tbody>            
                        <tr > <td colspan="4" style="background-color:#cccccc !important;" >&nbsp;</td></tr>
                        <tr id="net_total_tr"> 
                            <td colspan="2" class="item_l">Jumlah Iklan : <b>{{$trx_ad_web->trx_order_quantity}}</b></td> 
                            <td class="item_r">Subtotal</td> 
                            <td class="item_r">Rp. {{number_format($trx_ad_web->trx_order_price)}}</td> 
                        </tr> 
                        <tr id="total_tr"> 
                            <td colspan="2">&nbsp;</td> 
                            <td class="total">
                                <b>Total Bayar </b> 
                            </td> 
                            <td class="total">Rp. {{number_format($trx_ad_web->trx_order_total)}}</td> 
                        </tr>

                        <tfoot> 
                            <tr > <td colspan="7" style="font-size:0.7em;" ><br>Tanggal Cetak : {{\Carbon\Carbon::now()->format('d-m-Y H:i:s')}} - BACKUP</td></tr>
                        </tfoot> 
                    </table>
                </div>
                <!-- invoice-amount --> 
            </div> 
        </div>
    </div>
</div>