@extends('core::admin.'.$theme_cms->value.'.templates.parent')

@section('page_style_css')
    <style>
        @page {
           size: landscape; ;
        }
    </style>
@endsection

@section('title_dashboard', 'Report Transaction')

@section('breadcrumb')
        <ul class="m-subheader__breadcrumbs m-nav m-nav--inline">
            <li class="m-nav__item m-nav__item--home">
                <a href="#" class="m-nav__link m-nav__link--icon">
                    <i class="m-nav__link-icon la la-home"></i>
                </a>
            </li>
            <li class="m-nav__separator">-</li>
            <li class="m-nav__item">
                <a href="" class="m-nav__link">
                    <span class="m-nav__link-text">Home</span>
                </a>
            </li>
            <li class="m-nav__separator">-</li>
            <li class="m-nav__item">
                <a href="" class="m-nav__link">
                    <span class="m-nav__link-text">Invoice From {{Request::input('date_start')}} to {{Request::input('date_end')}}</span>
                </a>
            </li>
        </ul>
@endsection

@section('content')

<div class="row">
    <div class="col-sm-12">

        <!--Begin::Main Portlet-->
            <div class="m-portlet  m-portlet--tab">

                <div class="m-portlet__body">

                        <div class="row justify-content-center">
                                <div class="col-11 border">
                                    <div id="body_content">
                                        <div id="invoice">
                                            <div class="col-12 d-flex">
                                                <div class="col-2 px-0">
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
                                            <div class="col-6">
                                                <!-- #invoice-info -->
                                                    <div class="vcard" id="client-details"> 
                                                        <div class="tagihan"> <strong>Generated Report By</strong></div>
                                                        <table width="100%" border="0">
                                                            <tr>
                                                                <td width="25%">Nama Agen </td>
                                                                <td>: 
                                                                    {{AgentRepository::isAgent(Auth::user()) ? 'Agen' : 'Biro Pusat'}} / {{Auth::user()->name}}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td width="25%" valign="top">Alamat </td>
                                                                <td>: {{AgentRepository::isAgent(Auth::user()) ? 'Agen' : 'Biro Pusat'}}   </td>
                                                            </tr>
                                                            <tr>
                                                                <td width="25%">Telp </td>
                                                                <td>:                       / /                 </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Email </td>
                                                                <td>: {{Auth::user()->email}}   </td></td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                            </div>
                                        </div>
                                        <br>

                                        <table class="table table-stripped" id="invoice-amount"> 
                                            <thead> 
                                                <tr class="text-white bg-dark" id="header_row"> 
                                                    <th class="jenis_th" style="background-color: #343a40 !important;color: #fff !important;">Keterangan</th> 
                                                    <th class="konten_th" style="background-color: #343a40 !important;color: #fff !important;">Agent</th> 
                                                    <th class="konten_th" style="background-color: #343a40 !important;color: #fff !important;">Total Ads</th> 
                                                    <th class="tanggal_th" style="background-color: #343a40 !important;color: #fff !important;">Total Pembayaran yang Diterima</th> 
                                                </tr> 
                                            </thead> 
                                            <tbody>
                                                <tr  class="item odd" > 
                                                    <td class="item_l">Invoice From {{Request::input('date_start')}} to {{Request::input('date_end')}}</td>
                                                    <td>
                                                        @foreach ($agents as $agent)
                                                            <em>{{$agent->name}}</em>, &nbsp;
                                                        @endforeach
                                                    </td> 
                                                    <td class="item_l">{{$trx_ad_webs->count()}}</td> 
                                                    <td class="item_l">{{sprintf('Rp. %s', number_format($trx_ad_webs->sum('trx_order_total')))}}</td> 
                                                </tr> 
                                            </tbody>            
                                            <tfoot> 
                                                <tr > <td colspan="7" style="font-size:0.7em;" ><br>Tanggal Cetak : {{\Carbon\Carbon::now()->format('d-m-Y H:i:s')}} - BACKUP</td></tr>
                                            </tfoot> 
                                        </table>
                                        <!-- invoice-amount --> 
                                    </div> 
                                </div>
                        </div>
                       

                </div>

            </div>
             <div class="page-break w-100">
            </div>
            <div class="mt-5 w-100 d-print-none">
            </div>
            @foreach ($trx_ad_webs as $trx_ad_web)
                <div class="m-portlet  m-portlet--tab">

                    <div class="m-portlet__body">
                        <div class="row justify-content-center">
                            @include('ads::admin.'.$theme_cms->value.'.partials.invoice_ads_web', ['trx_ad_web' => $trx_ad_web])
                        </div>
                    </div>
                </div>
                

                 <div class="page-break w-100">
                </div>
                <div class="mt-5 w-100 d-print-none">
                </div>
            @endforeach

            <div class="row justify-content-end  d-print-none">
                <div class="col-lg-4 m--align-right">
                    <a href="javascript:print()" class="btn btn-warning m-btn m-btn--custom m-btn--icon">
                        <span>
                            Save as PDF or Print Invoice
                        </span>
                    </a>
                </div>
            </div>

        <!--End::Main Portlet-->

    </div>
</div>
{{-- End of Row --}}

@endsection

@section('page_level_js')
    {{Html::script(module_asset_url('core:assets/js/autosize.min.js'))}}
    {{Html::script(module_asset_url('core:assets/js/slugify.js'))}}
@endsection

@section('page_script_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $(".money-masking").inputmask('999,999,999', {
                numericInput: true,
            });
        });
    </script>
@endsection