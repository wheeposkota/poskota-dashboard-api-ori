@extends('core::admin.'.$theme_cms->value.'.templates.parent')

@section('title_dashboard', 'Iklan Baris')

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
                                Penerimaan Pembayaran
                            </h3>
                        </div>
                    </div>
                </div>

                <!--end: Portlet Head-->

                <!--begin: Form Wizard-->
                <div class="m-wizard m-wizard--success" id="m_wizard">

                    <!--begin: Form Wizard Form-->
                    <div class="m-wizard__form">
                        <!--begin: Form Body -->
                        <div class="m-portlet__body">

                            <!--begin: Form Wizard Step 1-->
                            <div class="m-wizard__form-step m-wizard__form-step--current" id="m_wizard_form_step_1">
                                <div class="row justify-content-center">
                                    <div class="col-11">
                                        <div class="m-form__section m-form__section--first">
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
                                            @include('ads::admin.'.$theme_cms->value.'.partials.invoice_ads_classic', ['trx_ad_classics' => $trx_ad_classics])
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!--end: Form Wizard Step 1-->

                        </div>

                        <!--end: Form Body -->

                        <!--begin: Form Actions -->
                        <div class="m-portlet__foot m-portlet__foot--fit m--margin-top-40">
                            <div class="m-form__actions py-4">
                                <div class="row justify-content-end">
                                    <div class="m--align-right">
                                        <form action="{{action('\Modules\Ads\Http\Controllers\TrxAdsClassicController@approvingPayment')}}" method="post" accept-charset="utf-8">
                                            <button type="submit" class="btn btn-success m-btn m-btn--custom m-btn--icon">
                                                <span>Terima Pembayaran</span>
                                            </button>
                                            <input type="hidden" name="code" value="{{Request::input('code')}}">
                                            {{csrf_field()}}
                                        </form>
                                    </div>
                                    <div class="col-2"></div>
                                </div>
                            </div>
                        </div>
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