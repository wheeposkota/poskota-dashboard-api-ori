@extends('core::admin.'.$theme_cms->value.'.templates.parent')

@section('page_level_css')
    {{Html::style(module_asset_url('core:assets/metronic-v5/global/plugins/bootstrap-tagsinput/bootstrap-tagsinput.css'))}}
    {{Html::style(module_asset_url('core:assets/metronic-v5/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css'))}}
    {{Html::style(module_asset_url('core:assets/metronic-v5/global/plugins/typeahead/typeaheadjs.css'))}}
@endsection

@section('title_dashboard', 'Package')

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
                    <span class="m-nav__link-text">Package</span>
                </a>
            </li>
        </ul>
@endsection

@section('content')

<div class="row">
    <div class="col-sm-12">

        <form class="m-form m-form--fit m-form--label-align-right" action="{{route('epaper-package.resource.store')}}" method="post" enctype="multipart/form-data">
            <!--begin::Portlet-->
            <div class="row">
                <div class="col-md-12">
                    <div class="m-portlet m-portlet--last m-portlet--head-lg m-portlet--responsive-mobile" id="main_portlet">
                        <div class="m-portlet__head">
                            <div class="m-portlet__head-wrapper">
                                <div class="m-portlet__head-caption">
                                    <div class="m-portlet__head-title">
                                        <h3 class="m-portlet__head-text">
                                            Package Form
                                        </h3>
                                    </div>
                                </div>
                                <div class="m-portlet__head-tools">
                                    <div class="row justify-content-end">
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!--begin::Form-->
                            <div class="m-portlet__body">
                                <div class="col-md-9 offset-md-3">
                                    @if (!empty(session('global_message')))
                                        <div class="alert {{session('global_message')['status'] == 200 ? 'alert-info' : 'alert-warning' }}">
                                            {{session('global_message')['message']}}
                                        </div>
                                    @endif
                                    @if (count($errors) > 0)
                                        <div class="alert alert-danger">
                                            <ul>
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group m-form__group d-flex px-0">
                                    <div class="col-3 d-flex justify-content-end py-3">
                                        <label for="exampleInputEmail1">Package Name :<span class="ml-1 m--font-danger" aria-required="true">*</span></label>
                                    </div>
                                    <div class="col-9">
                                        <input type="text" class="form-control m-input" placeholder="Package Name" name="package[package_name]" value="{{old('package.package_name') ? old('package.package_name') : (!empty($package) ? $package->package_name : '')}}">
                                    </div>
                                </div>
                                <div class="form-group m-form__group d-flex px-0">
                                    <div class="col-3 d-flex justify-content-end py-3">
                                        <label for="exampleInputEmail1">Package Period :<span class="ml-1 m--font-danger" aria-required="true">*</span></label>
                                    </div>
                                    <div class="col-9">
                                        <div class="input-group">
                                            <input type="number" class="form-control m-input" placeholder="Package Period" name="package[package_period]" value="{{old('package.package_period') ? old('package.package_period') : (!empty($package) ? $package->package_period : '')}}" min="1">
                                            <div class="input-group-append">
                                                <span class="input-group-text" id="basic-addon1">
                                                   Days
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group m-form__group d-flex px-0">
                                    <div class="col-3 d-flex justify-content-end py-3">
                                        <label for="exampleInputEmail1">Package Price :<span class="ml-1 m--font-danger" aria-required="true">*</span></label>
                                    </div>
                                    <div class="col-9">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="basic-addon1">
                                                   Rp. 
                                                </span>
                                            </div>
                                            <input type="text" min="0" class="form-control m-input money-masking" placeholder="Package Price" name="package[package_price]" value="{{old('package.package_price') ? old('package.package_price') : (!empty($package) ? $package->package_price : '')}}">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group m-form__group d-flex px-0">
                                    <div class="col-3 d-flex justify-content-end py-3">
                                        <label for="exampleInputEmail1">Package Description :</label>
                                    </div>
                                    <div class="col-9">
                                        <textarea type="text" class="form-control m-input autosize" placeholder="Package Description" name="package[package_description]">{{old('package.package_description') ? old('package.package_description') : (!empty($package) ? $package->package_description : '')}}</textarea>
                                    </div>
                                </div>
                            </div>
                            {{csrf_field()}}
                            @if(isset($_GET['code']))
                                <input type="hidden" name="{{\Modules\EPaper\Entities\Package::getPrimaryKey()}}" value="{{$_GET['code']}}">
                            @endif
                            {{$method}}

                        <!--end::Form-->
                    </div>
                </div>
            </div>
            <!--end::Portlet-->
        </form>

    </div>
</div>
{{-- End of Row --}}

@endsection

@section('page_level_js')
    {{Html::script(module_asset_url('core:assets/js/autosize.min.js'))}}
    {{Html::script(module_asset_url('core:assets/js/slugify.js'))}}
    {{Html::script(module_asset_url('core:assets/metronic-v5/global/plugins/ckeditor_4/ckeditor.js'))}}
    {{Html::script(module_asset_url('core:assets/metronic-v5/global/plugins/bootstrap-tagsinput/bootstrap-tagsinput.min.js'))}}
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