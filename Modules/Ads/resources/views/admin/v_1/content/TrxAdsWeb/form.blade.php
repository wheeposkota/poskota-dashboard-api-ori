@extends('core::admin.'.$theme_cms->value.'.templates.parent')

@section('title_dashboard', 'Web Ads Transaction')

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
                    <span class="m-nav__link-text">Web Ads Transaction</span>
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
                        <form class="m-form m-form--label-align-left- m-form--state-" id="m_form" action="{{action('\Modules\Ads\Http\Controllers\TrxAdsWebController@store')}}" method="post" enctype="multipart/form-data">

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
                                                </div>
                                                <div class="form-group m-form__group d-md-flex">
                                                    <div class="col-md-4 d-md-flex justify-content-end py-3">
                                                        <label for="exampleInputEmail1">Ads Title :<span class="ml-1 m--font-danger" aria-required="true">*</span></label>
                                                    </div>
                                                    <div class="col">
                                                        <input type="text" class="form-control" name="ads_title" value="{{old('ads_title') ? old('ads_title') : (!empty($trx_ad_web) ? $trx_ad_web->ads_title : '')}}" placeholder="Ads Title">
                                                    </div>
                                                </div>
                                                <div class="form-group m-form__group d-md-flex">
                                                    <div class="col-md-4 d-md-flex justify-content-end py-3">
                                                        <label for="exampleInputEmail1">Ads Position :<span class="ml-1 m--font-danger" aria-required="true">*</span></label>
                                                    </div>
                                                    <div class="col">
                                                        <select name="mst_ads_position_id" class="form-control  m-input select2">
                                                            <option value="" disabled selected>-- Select Ads Position --</option>
                                                            @foreach ($mst_ads_position as $mst_ad_position)
                                                                @if(old('mst_ads_position_id'))
                                                                    <option value="{{$mst_ad_position->getKey()}}" {{old('mst_ads_position_id') == $mst_ad_position->getKey() ? 'selected' : ''}}>{{$mst_ad_position->ads_position}} -- Rp. {{number_format($mst_ad_position->price)}}</option>
                                                                @else
                                                                    <option value="{{$mst_ad_position->getKey()}}" {{!empty($trx_ad_web) && $trx_ad_web->mst_ads_position_id == $mst_ad_position->getKey() ? 'selected' : ''}}>{{$mst_ad_position->ads_position}} -- Rp. {{number_format($mst_ad_position->price)}}</option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group m-form__group d-md-flex">
                                                    <div class="col-md-4 d-md-flex justify-content-end py-3">
                                                        <label for="exampleInputEmail1">Ads Publish Date :<span class="ml-1 m--font-danger" aria-required="true">*</span></label>
                                                    </div>
                                                    <div class="col">
                                                        <div class="input-group input-append date date-picker" data-date-format="yyyy-mm-dd" comment-data-date-start-date="{{!empty($trx_ad_web) ? \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $trx_ad_web->trx_ad_publish_date)->format('Y-m-d') : \Carbon\Carbon::now()->addDays(1)->format('Y-m-d')}}">
                                                            <span class="input-group-addon add-on"><i class="glyphicon glyphicon-calendar"></i></span>
                                                            <input class="form-control m-input" type="text" name="trx_ad_publish_date" value="{{old('trx_ad_publish_date') ? old('trx_ad_publish_date') : (!empty($trx_ad_web) ? \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $trx_ad_web->trx_ad_publish_date)->format('Y-m-d') : \Carbon\Carbon::now()->addDays(1)->format('Y-m-d'))}}" required readonly/>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group m-form__group d-md-flex">
                                                    <div class="col-md-4 d-md-flex justify-content-end py-3">
                                                        <label for="exampleInputEmail1">Ads End Date :<span class="ml-1 m--font-danger" aria-required="true">*</span></label>
                                                    </div>
                                                    <div class="col">
                                                        <div class="input-group input-append date date-picker" data-date-format="yyyy-mm-dd" comment-data-date-start-date="{{!empty($trx_ad_web) ? \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $trx_ad_web->trx_ad_end_date)->format('Y-m-d') : \Carbon\Carbon::now()->addDays(1)->format('Y-m-d')}}">
                                                            <span class="input-group-addon add-on"><i class="glyphicon glyphicon-calendar"></i></span>
                                                            <input class="form-control m-input" type="text" name="trx_ad_end_date" value="{{old('trx_ad_end_date') ? old('trx_ad_end_date') : (!empty($trx_ad_web) ? \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $trx_ad_web->trx_ad_end_date)->format('Y-m-d') : \Carbon\Carbon::now()->addDays(1)->format('Y-m-d'))}}" required readonly/>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group m-form__group d-md-flex">
                                                    <div class="col-md-4 d-md-flex justify-content-end py-3">
                                                        <label for="exampleInputEmail1">Ads File :<span class="ml-1 m--font-danger" aria-required="true">*</span></label>
                                                    </div>
                                                    <div class="col">
                                                        <div class="fileinput fileinput-new" data-provides="fileinput">
                                                            <div class="fileinput-new thumbnail">
                                                                @if(!empty($trx_ad_web) && $trx_ad_web->path_ads != null)
                                                                    <img class="img-fluid" src="{{generate_storage_url($trx_ad_web->path_ads)}}" alt=""> 
                                                                @else
                                                                    <img class="img-fluid" src="http://www.placehold.it/200x150/EFEFEF/AAAAAA&amp;text=no+image" alt=""> 
                                                                @endif
                                                            </div>
                                                            <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 150px;"> </div>
                                                            <div class="d-block">
                                                                <span class="btn btn-file btn-accent m-btn m-btn--air m-btn--custom">
                                                                    <span class="fileinput-new"> Select image </span>
                                                                    <span class="fileinput-exists"> Change </span>
                                                                    <input type="file" name="path_ads"> </span>
                                                                <a href="javascript:;" class="btn default fileinput-exists" data-dismiss="fileinput"> Remove </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group m-form__group d-md-flex">
                                                    <div class="col-md-4 d-md-flex justify-content-end py-3">
                                                        <label for="exampleInputEmail1">URL Action :<span class="ml-1 m--font-danger" aria-required="true">*</span></label>
                                                    </div>
                                                    <div class="col">
                                                        <input type="text" class="form-control" name="ads_action" value="{{old('ads_action') ? old('ads_action') : (!empty($trx_ad_web) ? $trx_ad_web->ads_action : '')}}" placeholder="https://google.com">
                                                    </div>
                                                </div>
                                                {{csrf_field()}}
                                                @if(isset($_GET['code']))
                                                    <input type="hidden" name="{{\App\Models\TrxOrder::getPrimaryKey()}}" value="{{encrypt($trx_ad_web->order_id)}}">
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
                                            <button type="submit" class="btn btn-warning m-btn m-btn--custom m-btn--icon">
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