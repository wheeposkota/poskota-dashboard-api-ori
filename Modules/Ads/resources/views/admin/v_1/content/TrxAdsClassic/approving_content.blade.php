@extends('core::admin.'.$theme_cms->value.'.templates.parent')

@section('title_dashboard', 'Iklan Baris')

@section('page_level_css')
    {{Html::style(module_asset_url('core:assets/metronic-v5/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css'))}}
@endsection

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
                    <span class="m-nav__link-text">Transaksi Iklan Baris</span>
                </a>
            </li>
        </ul>
@endsection

@section('content')

<div class="row">
    <div class="col-sm-12">

        <!--Begin::Main Portlet-->

            <div class="m-portlet  m-portlet--tab">

                <!--begin: Portlet Head-->
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <h3 class="m-portlet__head-text">
                               Periksa Konten Iklan
                            </h3>
                        </div>
                    </div>
                </div>

                <!--end: Portlet Head-->

                <!--begin: Form Wizard-->
                <div class="m-wizard m-wizard--success" id="m_wizard">

                    <!--begin: Message container -->
                    <div class="m-portlet__padding-x">

                        <!-- Here you can put a message or alert -->
                    </div>

                    <!--end: Message container -->

                    <!--begin: Form Wizard Form-->
                    <div class="m-wizard__form">
                        <form class="m-form m-form--label-align-left- m-form--state-" id="m_form" action="{{action('\Modules\Ads\Http\Controllers\TrxAdsClassicController@approvingContent')}}" method="post" enctype="multipart/form-data">

                            <!--begin: Form Body -->
                            <div class="m-portlet__body">

                                <!--begin: Form Wizard Step 1-->
                                <div class="m-wizard__form-step m-wizard__form-step--current" id="m_wizard_form_step_1">
                                    <div class="row">
                                        <div class="col-xl-8 offset-xl-2">
                                            <div class="m-form__section m-form__section--first">
                                                <div class="m-form__heading">
                                                    <h3 class="m-form__heading-title">Informasi Iklan</h3>
                                                </div>
                                                <div class="col-md-5 offset-md-4">
                                                    @if (!empty(session('global_message')))
                                                        <div class="alert {{session('global_message')['status'] == 200 ? 'alert-info' : 'alert-warning' }}">
                                                            {{session('global_message')['message']}}
                                                        </div>
                                                    @endif
                                                    @if (count($errors) > 0)
                                                        <div class="alert alert-danger">
                                                            <ul>
                                                                @foreach ($errors->all() as $error)
                                                                    <li>{!! $error !!}</li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="form-group m-form__group row">
                                                    <div class="col-4 d-md-flex justify-content-end">
                                                        <label for="exampleInputEmail1">Nama Agen :</label>
                                                    </div>
                                                    <div class="col d-flex align-items-center">
                                                        <label for="exampleInputEmail1">{{$trx_ad_classics->first()->author->name}} / {{!empty($trx_ad_classics->first()->author->agent) &&  !empty($trx_ad_classics->first()->author->agent->first())  ? $trx_ad_classics->first()->author->agent->first()->mst_ptlagen_name : 'Biro'}}  </label>
                                                    </div>
                                                </div>
                                                <div class="form-group m-form__group row">
                                                    <div class="col-4 d-md-flex justify-content-end">
                                                        <label for="exampleInputEmail1">Alamat :</label>
                                                    </div>
                                                    <div class="col d-flex align-items-center">
                                                        <label for="exampleInputEmail1">{{!empty($trx_ad_classics->first()->author->agent) &&  !empty($trx_ad_classics->first()->author->agent->first()) ? $trx_ad_classics->first()->author->agent->first()->mst_ptlagen_address : 'Biro'}}</label>
                                                    </div>
                                                </div>
                                                <div class="form-group m-form__group row">
                                                    <div class="col-4 d-md-flex justify-content-end">
                                                        <label for="exampleInputEmail1">Jenis :</label>
                                                    </div>
                                                    <div class="col d-flex align-items-center">
                                                        <label for="exampleInputEmail1">{{$trx_ad_classics->first()->rltAdCategory->mstAd->mst_ad_name}}</label>
                                                    </div>
                                                </div>
                                                <div class="form-group m-form__group row">
                                                    <div class="col-4 d-md-flex justify-content-end">
                                                        <label for="exampleInputEmail1">Tipe :</label>
                                                    </div>
                                                    <div class="col d-flex align-items-center">
                                                        <label for="exampleInputEmail1">{{$trx_ad_classics->first()->mstAdType->mst_ad_type_name}}</label>
                                                    </div>
                                                </div>
                                                <div class="form-group m-form__group row">
                                                    <div class="col-md-4 d-md-flex justify-content-end">
                                                        <label for="exampleInputEmail1">Konten :<span class="ml-1 m--font-danger" aria-required="true">*</span></label>
                                                    </div>
                                                    <div class="col">
                                                        @foreach ($trx_ad_classics->first()->trxOrder->trxAdClassics as $key => $tmp_trx_ad_classic)
                                                            <div class="col-12">
                                                                    <div class="col-12 d-md-flex align-items-center">
                                                                        <label for="exampleInputEmail1">
                                                                            {{$tmp_trx_ad_classic->rltAdCategory->mstAdsCategory->mst_ad_cat_name}}
                                                                            @if(!empty($tmp_trx_ad_classic->rltTaxonomyAds))
                                                                               &nbsp;{{$tmp_trx_ad_classic->rltTaxonomyAds->adTaxonomy->term->name}}
                                                                            @endif
                                                                            @if(!empty($tmp_trx_ad_classic->city))
                                                                               &nbsp;{{$tmp_trx_ad_classic->city->name}}
                                                                            @endif
                                                                        </label>
                                                                    </div>
                                                                    @foreach ($tmp_trx_ad_classic->schedules as $schedule)
                                                                        <div class="row">
                                                                            <div class="col-7 col-sm-9">
                                                                                <input type="text" class="form-control" readonly value="{{\Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $schedule->trx_ad_publish_date)->format('Y-m-d')}}">
                                                                            </div>
                                                                            <div class="col">
                                                                                @if($tmp_trx_ad_classic->schedules->count() > 1)
                                                                                    <button class="btn btn-danger mb-4 delete-schedule" type="button" data-id="{{encrypt($schedule->getKey())}}" data-url="{{action('\Modules\Ads\Http\Controllers\TrxAdsClassicController@deleteSchedule')}}">Hapus</button>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    @endforeach
                                                                    @php
                                                                        $line = ceil($tmp_trx_ad_classic->trx_ad_order_total_char/$tmp_trx_ad_classic->rltAdCategory->char_on_line);
                                                                        $line = $line > $tmp_trx_ad_classic->rltAdCategory->min_line ? $line : $tmp_trx_ad_classic->rltAdCategory->min_line;
                                                                    @endphp
                                                                    <textarea class="form-control formkonten" data-original-maxlength="{{$tmp_trx_ad_classic->rltAdCategory->max_line * $tmp_trx_ad_classic->rltAdCategory->char_on_line}}" maxlength="{{$line * $tmp_trx_ad_classic->rltAdCategory->char_on_line}}" data-maxlength="{{$line * $tmp_trx_ad_classic->rltAdCategory->char_on_line}}" data-min-line="{{$tmp_trx_ad_classic->rltAdCategory->min_line}}" data-char-on-line="{{$tmp_trx_ad_classic->rltAdCategory->char_on_line}}" data-price="{{$tmp_trx_ad_classic->rltAdCategory->price}}" name="ads[{{$key}}][trx_ad_content]">{{$tmp_trx_ad_classic->trx_ad_content}}</textarea>
                                                                    <div class="previewtext" style="background:#F0F0F0; padding:5px"></div>
                                                                    <br>
                                                                    <p class="countchar mb-1"></p>
                                                                    <input type="hidden" name="ads[{{$key}}][{{\Modules\Ads\Entities\TrxAdsClassic::getPrimaryKey()}}]" value="{{encrypt($tmp_trx_ad_classic->getKey())}}">
                                                                    @if($trx_ad_classics->first()->trxOrder->trxAdClassics->count() > 1)
                                                                        <button class="btn btn-danger mb-4 delete-content" type="button" data-id="{{encrypt($tmp_trx_ad_classic->getKey())}}" data-url="{{action('\Modules\Ads\Http\Controllers\TrxAdsClassicController@deleteContent')}}">Hapus</button>
                                                                    @endif
                                                                    <hr>
                                                                    <br>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                                @if(isset($_GET['code']))
                                                    <input type="hidden" name="{{\App\Models\TrxOrder::getPrimaryKey()}}" value="{{encrypt($trx_ad_classics->first()->order_id)}}">
                                                @endif
                                                {{csrf_field()}}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!--end: Form Wizard Step 1-->

                            </div>

                            <!--end: Form Body -->

                            <!--begin: Form Actions -->
                            <div class="m-portlet__foot m-portlet__foot--fit m--margin-top-40">
                                <div class="m-form__actions">
                                    <div class="row justify-content-end">
                                        <div class="col-lg-4 m--align-right">
                                            <button type="submit" class="btn btn-success m-btn m-btn--custom m-btn--icon" id="buttonlanjut">
                                                <span>
                                                    <span>Setujui Konten Iklan</span>
                                                </span>
                                            </button>
                                        </div>
                                        <div class="col-lg-2"></div>
                                    </div>
                                </div>
                            </div>

                            <!--end: Form Actions -->
                        </form>
                    </div>

                    <!--end: Form Wizard Form-->
                </div>

                <!--end: Form Wizard-->
            </div>

            <!--End::Main Portlet-->

    </div>
</div>
{{-- End of Row --}}

@endsection

@section('page_level_js')
    {{Html::script(module_asset_url('core:assets/js/autosize.min.js'))}}
    {{Html::script(module_asset_url('core:assets/js/slugify.js'))}}
    {{Html::script(module_asset_url('core:assets/metronic-v5/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js'))}}
    {{Html::script(module_asset_url('Ads:resources/views/admin/'.$theme_cms->value.'/js/ads_classic.js').'?id='.filemtime(module_asset_path('Ads:resources/views/admin/'.$theme_cms->value.'/js/ads_classic.js')))}}
@endsection

@section('page_script_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $(".money-masking").inputmask('999,999,999', {
                numericInput: true,
            });

            // cek char
            checkContent();
            // cek char
            
            $(".formkonten").each(function(index, el) {
             $(this).keyup();   
            });
        });

        function getCapitalLenght(indexxx) {
            return $('.formkonten').eq(indexxx).attr('data-original-maxlength');
        }
    </script>
@endsection