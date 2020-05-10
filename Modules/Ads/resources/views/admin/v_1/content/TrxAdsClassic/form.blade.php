@extends('core::admin.'.$theme_cms->value.'.templates.parent')

@section('title_dashboard', 'Classic Ads Transaction')

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
                    <span class="m-nav__link-text">Classic Ads Transaction</span>
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
                                Ads Registration
                            </h3>
                        </div>
                    </div>
                </div>

                <!--end: Portlet Head-->

                <!--begin: Form Wizard-->
                <div class="m-wizard m-wizard--2 m-wizard--success" id="m_wizard">

                    <!--begin: Message container -->
                    <div class="m-portlet__padding-x">

                        <!-- Here you can put a message or alert -->
                    </div>

                    <!--end: Message container -->

                    <!--begin: Form Wizard Head -->
                    <div class="m-wizard__head m-portlet__padding-x">

                        <!--begin: Form Wizard Progress -->
                        @include('ads::admin.v_1.partials.wizard_transaction')

                        <!--end: Form Wizard Nav -->
                    </div>

                    <!--end: Form Wizard Head -->

                    <!--begin: Form Wizard Form-->
                    <div class="m-wizard__form">
                        <form class="m-form m-form--label-align-left- m-form--state-" id="ads-form" action="{{action('\Modules\Ads\Http\Controllers\TrxAdsClassicController@store')}}" method="post" enctype="multipart/form-data">

                            <!--begin: Form Body -->
                            <div class="m-portlet__body">

                                <!--begin: Form Wizard Step 1-->
                                <div class="m-wizard__form-step m-wizard__form-step--current" id="m_wizard_form_step_1">
                                    <div class="row">
                                        <div class="col-xl-8 offset-xl-2">
                                            <div class="m-form__section m-form__section--first">
                                                <div class="m-form__heading">
                                                    <h3 class="m-form__heading-title">Ads Details</h3>
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
                                                    <div class="alert alert-danger" id="form-error" v-cloak v-if="window.Object.keys(errors).length > 0">
                                                            <ul v-for="error in errors">
                                                                <li v-for="message in error">
                                                                    @{{message}}
                                                                </li>
                                                            </ul>
                                                    </div>
                                                </div>
                                                <div class="form-group m-form__group row">
                                                    <div class="col-md-4 d-md-flex justify-content-end py-3">
                                                        <label for="exampleInputEmail1">Tipe Iklan :<span class="ml-1 m--font-danger" aria-required="true">*</span></label>
                                                    </div>
                                                    <div class="col d-flex align-items-center">
                                                        <div class="m-radio-inline">
                                                            <label class="m-radio">
                                                                <input type="radio" name="tipe" value="kontrak"  required> Kontrak
                                                                <span></span>
                                                            </label>
                                                            <label class="m-radio">
                                                                <input type="radio" name="tipe" value="non-kontrak" required checked> Non Kontrak
                                                                <span></span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group m-form__group row" id="formkriteria" style="display:none !important">
                                                    <div class="col-md-4 d-md-flex justify-content-end py-3">
                                                        <label for="exampleInputEmail1">Kriteria Iklan :<span class="ml-1 m--font-danger" aria-required="true">*</span></label>
                                                    </div>
                                                    <div class="col d-flex align-items-center">
                                                        <div class="m-radio-list">
                                                            <label class="m-radio">
                                                                <input type="radio" name="kriteria" value="1" required checked> Satu Tanggal Banyak Iklan
                                                                <span></span>
                                                            </label>
                                                            <label class="m-radio">
                                                                <input type="radio" name="kriteria" value="2" required> Banyak Tanggal Satu Iklan
                                                                <span></span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group m-form__group row">
                                                    <div class="col-md-4 d-md-flex justify-content-end py-3">
                                                        <label for="exampleInputEmail1">Ads Publish Date :<span class="ml-1 m--font-danger" aria-required="true">*</span></label>
                                                    </div>
                                                    <div class="col">
                                                        @if(empty($trx_ad_classics))
                                                            <div class="col-12" id="edisi">
                                                                <div class="input-group input-append date date-picker my-1 formedisi" data-date-format="yyyy-mm-dd" data-date-start-date="{{\Carbon\Carbon::now()->addDays(1)->format('Y-m-d')}}" data-date-end="{{\Carbon\Carbon::now()->addDays(1)->format('Y-m-d')}}" data-date-end-date="{{\Carbon\Carbon::now()->addDays(1)->format('Y-m-d')}}">
                                                                    <span class="input-group-addon add-on"><i class="glyphicon glyphicon-calendar"></i></span>
                                                                    <input class="form-control m-input" type="text" name="trx_ad_publish_date[]" value="{{(\Carbon\Carbon::now()->addDays(1)->format('Y-m-d'))}}" required readonly/>
                                                                </div>
                                                            </div>
                                                            <div class="col-12" id="more_terbit">
                                                            </div>
                                                        @else
                                                            @if($trx_ad_classics->first()->trxOrder->trxAdClassics->count() > 1)
                                                                <div class="col-12" id="edisi">
                                                                    <div class="input-group input-append date date-picker my-1 formedisi" data-date-format="yyyy-mm-dd" data-date-start-date="{{\Carbon\Carbon::now()->addDays(1)->format('Y-m-d')}}" data-date-end="{{\Carbon\Carbon::now()->addDays(1)->format('Y-m-d')}}" data-date-end-date="{{\Carbon\Carbon::now()->addDays(1)->format('Y-m-d')}}">
                                                                        <span class="input-group-addon add-on"><i class="glyphicon glyphicon-calendar"></i></span>
                                                                        <input class="form-control m-input" type="text" name="trx_ad_publish_date[]" value="{{\Carbon\Carbon::createFromFormat('Y-m-d H:i:s',$trx_ad_classics->first()->trxOrder->trxAdClassics->first()->schedules->first()->trx_ad_publish_date)->format('Y-m-d')}}" required readonly/>
                                                                    </div>
                                                                </div>
                                                                <div class="col-12" id="more_terbit">
                                                                </div>
                                                            @else
                                                                @php 
                                                                        $schedules = $trx_ad_classics->first()->trxOrder->trxAdClassics->first()->schedules;
                                                                @endphp
                                                                <div class="col-12" id="edisi">
                                                                    @php
                                                                         $init_schedule = $schedules->shift();
                                                                    @endphp
                                                                    <div class="input-group input-append date date-picker my-1 formedisi" data-date-format="yyyy-mm-dd" data-date-format="yyyy-mm-dd" data-date-start-date="{{\Carbon\Carbon::now()->addDays(1)->format('Y-m-d')}}" data-date-end="{{\Carbon\Carbon::now()->addDays(1)->format('Y-m-d')}}">
                                                                        <span class="input-group-addon add-on"><i class="glyphicon glyphicon-calendar"></i></span>
                                                                        <input class="form-control m-input" type="text" name="trx_ad_publish_date[]" value="{{\Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $init_schedule->trx_ad_publish_date)->format('Y-m-d')}}" required readonly/>
                                                                    </div>
                                                                </div>
                                                                <div class="col-12" id="more_terbit">
                                                                    @foreach ($schedules as $schedule)
                                                                        <div class="input-group input-append date date-picker my-1 formedisi" data-date-format="yyyy-mm-dd" data-date-format="yyyy-mm-dd" data-date-start-date="{{\Carbon\Carbon::now()->addDays(1)->format('Y-m-d')}}" data-date-end="{{\Carbon\Carbon::now()->addDays(1)->format('Y-m-d')}}">
                                                                            <span class="input-group-addon add-on"><i class="glyphicon glyphicon-calendar"></i></span>
                                                                            <input class="form-control m-input" type="text" name="trx_ad_publish_date[]" value="{{\Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $schedule->trx_ad_publish_date)->format('Y-m-d')}}" required readonly/>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            @endif
                                                        @endif
                                                        <div class="col-12 mt-2 text-right" id="btnaddtgl" style="display:none">
                                                            <a href="javascript:void(0)" id="addtgl" class="btn btn-primary">+</a>
                                                            <a href="javascript:void(0)" id="mintgl" class="btn btn-primary">-</a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="form-group m-form__group row">
                                                    <div class="col-4 d-md-flex justify-content-end py-3">
                                                        <label for="exampleInputEmail1">Agen/Biro :<span class="ml-1 m--font-danger" aria-required="true">*</span></label>
                                                    </div>
                                                    <div class="col d-flex align-items-center">
                                                        <label for="exampleInputEmail1">{{AgentRepository::isAgent(Auth::user())? 'Agen' : 'Biro Pusat'}}</label>
                                                    </div>
                                                </div>
                                                <div class="form-group m-form__group row">
                                                    <div class="col-4 d-md-flex justify-content-end py-3">
                                                        <label for="exampleInputEmail1">Nama Pemasang :<span class="ml-1 m--font-danger" aria-required="true">*</span></label>
                                                    </div>
                                                    <div class="col d-flex align-items-center">
                                                        <label for="exampleInputEmail1">{{Auth::user()->name}}</label>
                                                    </div>
                                                </div>
                                                <div class="form-group m-form__group row">
                                                    <div class="col-4 d-md-flex justify-content-end py-3">
                                                        <label for="exampleInputEmail1">Diskon :<span class="ml-1 m--font-danger" aria-required="true">*</span></label>
                                                    </div>
                                                    <div class="col d-flex align-items-center">
                                                        <label for="exampleInputEmail1">{{AgentRepository::getUserDiscount(Auth::user())}} %</label>
                                                    </div>
                                                </div>
                                                <div class="form-group m-form__group row">
                                                    <div class="col-md-4 d-md-flex justify-content-end py-3">
                                                        <label for="exampleInputEmail1">Konten :<span class="ml-1 m--font-danger" aria-required="true">*</span></label>
                                                    </div>
                                                    <div class="col px-0">
                                                        <div class="col-12">
                                                            @if(empty($trx_ad_classics))
                                                                <div id="konten" class="my-3">
                                                                    <div class="col-12">
                                                                        <select name="rlt_trx_ads_category_id[]" class="form-control kategori" required>
                                                                            <option value="" selected disabled>-- Please Select Ad Category --</option>
                                                                            @foreach ($type->categories as $category)
                                                                                <option data-char="{{$category->pivot->max_line * $category->pivot->char_on_line}}" value="{{encrypt($category->pivot[\Modules\Ads\Entities\RltAdsCategories::getPrimaryKey()])}}" data-taxonomy-id="{{encrypt($category->last_parent_id)}}" data-char-on-line="{{$category->pivot->char_on_line}}" data-min-line="{{$category->pivot->min_line}}" data-price="{{$category->pivot->price}}">{{sprintf('%s', $category->mst_ad_cat_name, $category->pivot->max_line * $category->pivot->char_on_line, number_format($category->pivot->price))}}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                        <div class="opsisubs" style="margin-top:10px;">
                                                                            <input type="hidden" name="ad_taxonomy_id[]">
                                                                            <input type="hidden" name="mst_city_id[]">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-12">
                                                                        <textarea name="trx_ad_content[]" class="form-control formkonten my-1 autosize" maxlength="0" data-maxlength="0" required></textarea>
                                                                        <p class="countchar">0 dari 0 karakter</p>
                                                                        <div class="previewtext" style="background:#F0F0F0; padding:5px"></div>
                                                                        <br>
                                                                         <!-- delete by index -->
                                                                        <a href="javascript:void(0)" class="btn btn-sm btn-danger deletebyindex">Hapus</a> 
                                                                        <!-- delete by index -->
                                                                    </div>
                                                                    <hr>
                                                                </div>
                                                                <div id="more_konten">
                                                                    
                                                                </div>
                                                            @else
                                                                @if($trx_ad_classics->first()->trxOrder->trxAdClassics->count() < 2)
                                                                    <div id="konten" class="my-3">
                                                                        <div class="col-12">
                                                                            <select name="rlt_trx_ads_category_id[]" class="form-control kategori" required>
                                                                                <option value="" selected disabled>-- Please Select Ad Category --</option>
                                                                                @foreach ($type->categories as $category)
                                                                                    <option data-char="{{$category->pivot->max_line * $category->pivot->char_on_line}}" value="{{encrypt($category->pivot[\Modules\Ads\Entities\RltAdsCategories::getPrimaryKey()])}}" {{$trx_ad_classics->first()->trxOrder->trxAdClassics->first()->rlt_trx_ads_category_id == $category->pivot[\Modules\Ads\Entities\RltAdsCategories::getPrimaryKey()] ? 'selected' : ''}} data-taxonomy-id="{{encrypt($category->last_parent_id)}}" data-char-on-line="{{$category->pivot->char_on_line}}" data-min-line="{{$category->pivot->min_line}}" data-price="{{$category->pivot->price}}">{{sprintf('%s', $category->mst_ad_cat_name, $category->pivot->max_line * $category->pivot->char_on_line, number_format($category->pivot->price))}}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                            <div class="opsisubs" style="margin-top:10px;">
                                                                                @if(!empty($trx_ad_classics->first()->trxOrder->trxAdClassics->first()->rltTaxonomyAds))
                                                                                    <select name="ad_taxonomy_id[]" class="form-control">
                                                                                        <option value="{{encrypt($trx_ad_classics->first()->trxOrder->trxAdClassics->first()->rltTaxonomyAds->ad_taxonomy_id)}}">{{$trx_ad_classics->first()->trxOrder->trxAdClassics->first()->rltTaxonomyAds->adTaxonomy->term->name}}</option>
                                                                                    </select>
                                                                                @else
                                                                                    <input type="hidden" name="ad_taxonomy_id[]">
                                                                                @endif

                                                                                @if(!empty($trx_ad_classics->first()->trxOrder->trxAdClassics->first()->city))
                                                                                    <select name="mst_city_id[]" class="form-control">
                                                                                        <option value="{{encrypt($trx_ad_classics->first()->trxOrder->trxAdClassics->first()->mst_city_id)}}">{{$trx_ad_classics->first()->trxOrder->trxAdClassics->first()->city->name}}</option>
                                                                                    </select>
                                                                                @else
                                                                                    <input type="hidden" name="mst_city_id[]">
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-12">
                                                                            <textarea name="trx_ad_content[]" class="form-control formkonten my-1 autosize" maxlength="{{$trx_ad_classics->first()->rltAdCategory->max_line * $trx_ad_classics->first()->rltAdCategory->char_on_line}}"  data-maxlength="{{$trx_ad_classics->first()->rltAdCategory->max_line * $trx_ad_classics->first()->rltAdCategory->char_on_line}}" data-min-line="{{$trx_ad_classics->first()->rltAdCategory->min_line}}" data-char-on-line="{{$trx_ad_classics->first()->rltAdCategory->char_on_line}}" data-price="{{$trx_ad_classics->first()->rltAdCategory->price}}" required>{{$trx_ad_classics->first()->trxOrder->trxAdClassics->first()->trx_ad_content}}</textarea>
                                                                            <p class="countchar"></p>
                                                                            <div class="previewtext" style="background:#F0F0F0; padding:5px"></div>
                                                                            <br>
                                                                            <!-- delete by index -->
                                                                            <a href="javascript:void(0)" class="btn btn-sm btn-danger deletebyindex">Hapus</a> 
                                                                            <!-- delete by index -->
                                                                        </div>
                                                                        <hr>
                                                                    </div>
                                                                    <div id="more_konten">
                                                                        
                                                                    </div>
                                                                @else
                                                                    @php
                                                                        $ads = $trx_ad_classics->first()->trxOrder->trxAdClassics;
                                                                    @endphp
                                                                    <div id="konten" class="my-3">
                                                                        @php
                                                                            $ad = $ads->first();
                                                                        @endphp
                                                                        <div class="col-12">
                                                                            <select name="rlt_trx_ads_category_id[]" class="form-control kategori" required>
                                                                                <option value="" selected disabled>-- Please Select Ad Category --</option>
                                                                                @foreach ($type->categories as $category)
                                                                                    <option data-char="{{$category->pivot->max_line * $category->pivot->char_on_line}}" value="{{encrypt($category->pivot[\Modules\Ads\Entities\RltAdsCategories::getPrimaryKey()])}}" {{$ad->rlt_trx_ads_category_id == $category->pivot[\Modules\Ads\Entities\RltAdsCategories::getPrimaryKey()] ? 'selected' : ''}} data-taxonomy-id="{{encrypt($category->last_parent_id)}}" data-char-on-line="{{$category->pivot->char_on_line}}" data-min-line="{{$category->pivot->min_line}}" data-price="{{$category->pivot->price}}">{{sprintf('%s', $category->mst_ad_cat_name, $category->pivot->max_line * $category->pivot->char_on_line, number_format($category->pivot->price))}}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                            <div class="opsisubs" style="margin-top:10px;">
                                                                                @if(!empty($ad->rltTaxonomyAds))
                                                                                    <select name="ad_taxonomy_id[]" class="form-control">
                                                                                        <option value="{{encrypt($ad->rltTaxonomyAds->ad_taxonomy_id)}}">{{$ad->rltTaxonomyAds->adTaxonomy->term->name}}</option>
                                                                                    </select>
                                                                                @else
                                                                                    <input type="hidden" name="ad_taxonomy_id[]">
                                                                                @endif

                                                                                @if(!empty($ad->city))
                                                                                    <select name="mst_city_id[]" class="form-control">
                                                                                        <option value="{{encrypt($ad->mst_city_id)}}">{{$ad->city->name}}</option>
                                                                                    </select>
                                                                                @else
                                                                                    <input type="hidden" name="mst_city_id[]">
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-12">
                                                                            <textarea name="trx_ad_content[]" class="form-control formkonten my-1 autosize" maxlength="{{$ad->rltAdCategory->max_line * $ad->rltAdCategory->char_on_line}}" data-maxlength="{{$ad->rltAdCategory->max_line * $ad->rltAdCategory->char_on_line}}" data-min-line="{{$ad->rltAdCategory->min_line}}" data-char-on-line="{{$ad->rltAdCategory->char_on_line}}" data-price="{{$ad->rltAdCategory->price}}" required>{{$ad->trx_ad_content}}</textarea>
                                                                            <p class="countchar"></p>
                                                                            <div class="previewtext" style="background:#F0F0F0; padding:5px"></div>
                                                                            <br>
                                                                            <!-- delete by index -->
                                                                            <a href="javascript:void(0)" class="btn btn-sm btn-danger deletebyindex">Hapus</a> 
                                                                            <!-- delete by index -->
                                                                        </div>
                                                                        <hr>
                                                                    </div>
                                                                    <div id="more_konten">
                                                                        @php
                                                                            $ads = $ads->slice(1);
                                                                        @endphp
                                                                        @foreach ($ads as $ad)
                                                                            <div id="konten" class="my-3">
                                                                                <div class="col-12">
                                                                                    <select name="rlt_trx_ads_category_id[]" class="form-control kategori" required>
                                                                                        <option value="" selected disabled>-- Please Select Ad Category --</option>
                                                                                        @foreach ($type->categories as $category)
                                                                                            <option data-char="{{$category->pivot->max_line * $category->pivot->char_on_line}}" value="{{encrypt($category->pivot[\Modules\Ads\Entities\RltAdsCategories::getPrimaryKey()])}}" {{$ad->rlt_trx_ads_category_id == $category->pivot[\Modules\Ads\Entities\RltAdsCategories::getPrimaryKey()] ? 'selected' : ''}} data-taxonomy-id="{{encrypt($category->last_parent_id)}}" data-char-on-line="{{$category->pivot->char_on_line}}" data-min-line="{{$category->pivot->min_line}}" data-price="{{$category->pivot->price}}">{{sprintf('%s', $category->mst_ad_cat_name, $category->pivot->max_line * $category->pivot->char_on_line, number_format($category->pivot->price))}}
                                                                                            </option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                    <div class="opsisubs" style="margin-top:10px;">
                                                                                        @if(!empty($ad->rltTaxonomyAds))
                                                                                            <select name="ad_taxonomy_id[]" class="form-control">
                                                                                                <option value="{{encrypt($ad->rltTaxonomyAds->ad_taxonomy_id)}}">{{$ad->rltTaxonomyAds->adTaxonomy->term->name}}</option>
                                                                                            </select>
                                                                                        @else
                                                                                            <input type="hidden" name="ad_taxonomy_id[]">
                                                                                        @endif

                                                                                        @if(!empty($ad->city))
                                                                                            <select name="mst_city_id[]" class="form-control">
                                                                                                <option value="{{encrypt($ad->mst_city_id)}}">{{$ad->city->name}}</option>
                                                                                            </select>
                                                                                        @else
                                                                                            <input type="hidden" name="mst_city_id[]">
                                                                                        @endif
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-12">
                                                                                    <textarea name="trx_ad_content[]" class="form-control formkonten my-1 autosize" maxlength="{{$ad->rltAdCategory->max_line * $ad->rltAdCategory->char_on_line}}" data-maxlength="{{$ad->rltAdCategory->max_line * $ad->rltAdCategory->char_on_line}}" data-min-line="{{$ad->rltAdCategory->min_line}}" data-char-on-line="{{$ad->rltAdCategory->char_on_line}}" data-price="{{$ad->rltAdCategory->price}}" required>{{$ad->trx_ad_content}}</textarea>
                                                                                    <p class="countchar"></p>
                                                                                    <div class="previewtext" style="background:#F0F0F0; padding:5px"></div>
                                                                                    <br>
                                                                                    <!-- delete by index -->
                                                                                    <a href="javascript:void(0)" class="btn btn-sm btn-danger deletebyindex">Hapus</a> 
                                                                                    <!-- delete by index -->
                                                                                </div>
                                                                                <hr>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                @endif
                                                            @endif
                                                        </div>
                                                        <div class="col-12 text-right mt-2" id="btnaddkonten">
                                                            <a href="javascript:void(0)" id="addkonten" class="btn btn-primary">+</a> 
                                                            <a href="javascript:void(0)" id="minkonten" class="btn btn-primary">-</a>
                                                        </div>
                                                    </div>
                                                </div>
                                                {{csrf_field()}}
                                                @if(isset($_GET['code']))
                                                    <input type="hidden" name="{{\App\Models\TrxOrder::getPrimaryKey()}}" value="{{encrypt($trx_ad_classics->first()->order_id)}}">
                                                @endif
                                                {{$method}}
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
                                            <button type="submit" class="btn btn-warning m-btn m-btn--custom m-btn--icon" disabled id="buttonlanjut">
                                                <span>
                                                    <span>Save & Continue</span>&nbsp;&nbsp;
                                                    <i class="la la-arrow-right"></i>
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

            $(document).on('change', 'input[name="tipe"]', function() {
                var tipe = $(this).val();
                if (tipe == 'kontrak') {
                    $('#formkriteria').css('display', 'flex');
                    $('#edisi input').attr('readonly', false);
                    $('input[name="kriteria"]').eq(0).prop('checked', true);
                    $('#more_konten').html('');
                    $('#btnaddkonten').css('display', 'block');

                    $("#edisi .date-picker").each(function(index, el) {
                        $(this).removeAttr('data-date-end-date');                       
                        $(this).datepicker('setEndDate', 0);
                    });
                } else {
                    $('#formkriteria').attr('style', 'display: none !important');
                    $('#edisi input').attr('readonly', true);
                    $('#more_terbit').html('');
                    $('#btnaddtgl').css('display', 'none');
                    $('#more_konten').html('');
                    $('#btnaddkonten').css('display', 'block');

                    $("#edisi .date-picker").each(function(index, el) {
                        $(this).attr('data-date-end-date', $(this).attr('data-date-end'));   
                        $(this).datepicker('setEndDate', $(this).attr('data-date-end-date'));                    
                        $(this).datepicker('setDate', $(this).attr('data-date-start-date'));                    
                    });
                }
            });

            $(document).on('change', 'input[name="kriteria"]', function() {
                var tipe = $(this).val();
                if (tipe == '2') {
                    $('#btnaddtgl').css('display', 'block');
                    $('#btnaddkonten').css('display', 'none');
                    $('#more_konten').html('');
                } else {
                    $('#btnaddtgl').css('display', 'none');
                    $('#more_terbit').html('');
                    $('#btnaddkonten').css('display', 'block');
                }
            });

            $(document).on('click', '#addtgl', function() {
                $('#edisi .date-picker').clone().appendTo("#more_terbit");
                var indextgl = $('.formedisi').length - 1;
                $('.formedisi').eq(indextgl).val('');
                $(".date-picker").datepicker();
            });
            $(document).on('click', '#mintgl', function() {
                var indextgl = $('.formedisi').length - 1;
                if (indextgl > 0) {
                    $('.formedisi').eq(indextgl).remove();
                }
            });

            $(document).on('click', '#addkonten', function() {
                $('#konten').clone().appendTo("#more_konten");
                var indexkonten = $('.formkonten').length - 1;
                $('.formkonten').eq(indexkonten).val('');
                $('.formkonten').eq(indexkonten).attr('maxlength', '0');
                $('.formkonten').eq(indexkonten).attr('data-maxlength', '0');
                $('.kategori').eq(indexkonten).val('');

                // hitung char
                $('.countchar').eq(indexkonten).text('0 dari 0 karakter');
                // hitung char
                
                //sub
                $('.opsisubs').eq(indexkonten).html('');
                //sub
                
                $('.previewtext').eq(indexkonten).html('');

                if($(".autosize").length)
                {
                    autosize($(".autosize"));
                }
            });
            $(document).on('click', '#minkonten', function() {
                var indexkonten = $('.formkonten').length - 1;
                if (indexkonten > 0) {
                    // hitung char
                        $('.countchar').eq(indexkonten).text('0 dari 0 karakter');
                    // hitung char
                    $('.formkonten').eq(indexkonten).parent().parent().remove();
                }
            });
            $(document).on("change", ".kategori", function(e){
                var maxxx = $("option:selected", this).attr('data-char');

                // Set Price
                var price = $("option:selected", this).attr('data-price');
                $(this).parent().parent().find('textarea').attr('data-price', price);
                var charonline = $("option:selected", this).attr('data-char-on-line');
                $(this).parent().parent().find('textarea').attr('data-char-on-line', charonline);
                var minline = $("option:selected", this).attr('data-min-line');
                $(this).parent().parent().find('textarea').attr('data-min-line', minline);
                // Set Price
                
                $(this).parent().parent().find('textarea').attr('maxlength', maxxx);
               
                //cek char
                $(this).parent().parent().find('textarea').attr('data-maxlength', maxxx);
                //cek char
                
                var currentval = $(this).parent().parent().find('textarea').val().slice(0, maxxx);
                $(this).parent().parent().find('textarea').val(currentval);
                var countchar = currentval.length;

                //sub 
                var subs = $("option:selected", this).attr('data-sub');
                var self = $(this);

                $.ajax({
                    url: "{{action('\Modules\Ads\Http\Controllers\MstAdCategoriesController@subCategory')}}",
                    data: {
                        category_id: $("option:selected", this).attr('data-taxonomy-id'),
                        id_rlt: $(self).val()
                    },
                })
                .done(function(data) {
                    self.parent().find('.opsisubs').html(data);
                })
                .fail(function() {
                    //self.parent().find('.opsisubs').html('');
                });

                var indexxx = $('.kategori').index(this);
                checkcharkonten(e, currentval, indexxx);
                
            });

            //sub
            $(document).on("change", ".opsisubs select", function(e) {
                var textvalue = $("option:selected", this).text();
                var indexxx = $('.opsisubs select').index(this);

                var formkonten = $(this).parent().parent().parent().find('textarea');
                formkonten.val(textvalue);
                var countchar = textvalue.length;
                var maxchar = formkonten.attr('data-maxlength');
                var currentval = formkonten.val().slice(0, maxchar);

                checkcharkonten(e, currentval, indexxx);
            });
            //sub

            //cek char
            window.checkContent();
            //cek char
            
            //detele by index
            $(document).on("click", ".deletebyindex", function(){
                var indexbtn = $('.deletebyindex').index(this);
                var contentcount = $('.deletebyindex').length;
                console.log(indexbtn);

                if(contentcount != 1){
                    $('.countchar').eq(indexbtn).text('0 dari 0 karakter');
                    $('.formkonten').eq(indexbtn).parent().parent().remove();
                }
            });
            //detele by index

            @if(!empty($trx_ad_classics))
                @if($trx_ad_classics->first()->mstAdType->mst_ad_type_slug == 'kontrak')
                    $('input[name="tipe"]').eq(0).prop('checked', true);
                    $('#formkriteria').css('display', 'flex');
                    @if($trx_ad_classics->first()->trxOrder->trxAdClassics->count() < 2)
                        $('input[name="kriteria"]').eq(1).prop('checked', true).change();
                    @endif
                @endif
                $('#buttonlanjut').attr('disabled',false);

                $(".formkonten").each(function(index, el) {
                 $(this).keyup();   
                });

                $(".opsisubs select[name='ad_taxonomy_id[]']").each(function(index, el) {
                    let self = $(this);

                    $.ajax({
                        url: "{{action('\Modules\Ads\Http\Controllers\MstAdCategoriesController@subCategory')}}",
                        data: {ad_taxonomy_id: $(this).val()},
                    })
                    .done(function(data) {
                        $(self).html(data);
                    })
                    .fail(function() {
                        //self.parent().find('.opsisubs').html('');
                    });                    
                });

                $(".opsisubs select[name='mst_city_id[]']").each(function(index, el) {
                    let self = $(this);

                    $.ajax({
                        url: "{{action('\Modules\Ads\Http\Controllers\MstAdCategoriesController@subCategory')}}",
                        data: {mst_city_id: $(this).val()},
                    })
                    .done(function(data) {
                        $(self).html(data);
                    })
                    .fail(function() {
                        //self.parent().find('.opsisubs').html('');
                    });                    
                });
            @endif

            $("#buttonlanjut").click(function(event) {
                event.preventDefault();
                $.ajax({
                    url: '{{action("\Modules\Ads\Http\Controllers\TrxAdsClassicController@validation")}}',
                    type: 'POST',
                    data: $("#ads-form").serialize(),
                })
                .done(function(response) {
                    FormError.errors = [];
                    $("#ads-form").submit();
                })
                .fail(function(xhr) {
                    FormError.errors = xhr.responseJSON;
                    $('html, body').animate({scrollTop: $("#ads-form").offset().top - 50}, 100);
                });
            });
        });
    </script>

    <script type="text/javascript">
        var FormError = new Vue({
            mixins: [componentMixin],
            el: "#form-error",
            data: {
                errors: [],
            },
        });
    </script>
@endsection