@extends('core::admin.'.$theme_cms->value.'.templates.parent')

@section('page_style_css')
    <style>
        @page {
           size: landscape; ;
        }
    </style>
@endsection

@section('title_dashboard', 'Web Ads Transaction')

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
                                Ads Payment
                            </h3>
                        </div>
                    </div>
                </div>

                <!--end: Portlet Head-->

                <!--begin: Form Wizard-->
                <div class="m-wizard m-wizard--2 m-wizard--success d-print-none" id="m_wizard">

                    <!--begin: Message container -->
                    <div class="m-portlet__padding-x">

                        <!-- Here you can put a message or alert -->
                    </div>

                    <!--end: Message container -->

                    <!--begin: Form Wizard Head -->
                    <div class="m-wizard__head m-portlet__padding-x d-print-none">

                        <!--begin: Form Wizard Progress -->
                        @include('ads::admin.v_1.partials.wizard_transaction')
                        </div>

                        <!--end: Form Wizard Nav -->
                    </div>

                    <!--end: Form Wizard Head -->

                    <!--begin: Form Wizard Form-->
                    <div class="m-wizard__form">
                        <form class="m-form m-form--label-align-left- m-form--state-" id="m_form" action="{{action('\Modules\Ads\Http\Controllers\TrxAdsWebController@store')}}" method="post">

                            <!--begin: Form Body -->
                            <div class="m-portlet__body">

                                <!--begin: Form Wizard Step 1-->
                                <div class="m-wizard__form-step m-wizard__form-step--current" id="m_wizard_form_step_1">
                                    <div class="row justify-content-center">
                                        <div class="col-md-8">
                                            @if (!empty(session('global_message')))
                                                <div class="alert {{session('global_message')['status'] == 200 ? 'alert-info' : 'alert-warning' }}">
                                                    {!!session('global_message')['message']!!}
                                                </div>
                                            @endif
                                            @if(!empty($pending_message))
                                                <div class="alert alert-danger d-print-none">
                                                    {!!$pending_message!!}
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
                                        @include('ads::admin.'.$theme_cms->value.'.partials.invoice_ads_web', ['trx_ad_web' => $trx_ad_web])
                                    </div>
                                </div>

                                <!--end: Form Wizard Step 1-->

                            </div>

                            <!--end: Form Body -->

                            <!--begin: Form Actions -->
                            <div class="m-portlet__foot m-portlet__foot--fit m--margin-top-40 d-print-none">
                                <div class="m-form__actions">
                                    <div class="row justify-content-end">
                                        <div class="m--align-right">
                                            <a href="javascript:print()" class="btn btn-warning m-btn m-btn--custom m-btn--icon">
                                                <span>
                                                    Print Invoice
                                                </span>
                                            </a>
                                        </div>
                                        <div class="ml-2">
                                            <a href="{{action('\Modules\Ads\Http\Controllers\TrxAdsWebController@index')}}" class="btn btn-danger m-btn m-btn--custom m-btn--icon">
                                                <span>
                                                    Finish
                                                </span>
                                            </a>
                                        </div>
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