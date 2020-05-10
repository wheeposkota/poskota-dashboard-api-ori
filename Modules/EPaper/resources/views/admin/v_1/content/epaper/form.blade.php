@extends('core::admin.'.$theme_cms->value.'.templates.parent')

@section('page_level_css')
    {{Html::style(module_asset_url('core:assets/metronic-v5/global/plugins/bootstrap-tagsinput/bootstrap-tagsinput.css'))}}
    {{Html::style(module_asset_url('core:assets/metronic-v5/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css'))}}
    {{Html::style(module_asset_url('core:assets/metronic-v5/global/plugins/typeahead/typeaheadjs.css'))}}
@endsection

@section('title_dashboard', 'E-Paper')

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
                    <span class="m-nav__link-text">E-Paper</span>
                </a>
            </li>
        </ul>
@endsection

@section('content')

<div class="row">
    <div class="col-sm-12">

        <form class="m-form m-form--fit m-form--label-align-right" action="{{route('epaper.resource.store')}}" method="post" enctype="multipart/form-data">
            <!--begin::Portlet-->
            <div class="row">
                <div class="col-md-8">
                    <div class="m-portlet m-portlet--last m-portlet--head-lg m-portlet--responsive-mobile" id="main_portlet">
                        <div class="m-portlet__head">
                            <div class="m-portlet__head-wrapper">
                                <div class="m-portlet__head-caption">
                                    <div class="m-portlet__head-title">
                                        <h3 class="m-portlet__head-text">
                                            E-Paper Form
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
                                        <label for="exampleInputEmail1">E-Paper Title :<span class="ml-1 m--font-danger" aria-required="true">*</span></label>
                                    </div>
                                    <div class="col-9">
                                        <input type="text" class="form-control m-input slugify" data-target="slug" placeholder="E-Paper Title" name="epaper[epaper_title]" value="{{old('epaper.epaper_title') ? old('epaper.epaper_title') : (!empty($epaper) ? $epaper->epaper_title : '')}}">
                                    </div>
                                </div>
                                <div class="form-group m-form__group d-flex px-0">
                                    <div class="col-3 d-flex justify-content-end py-3">
                                        <label for="exampleInputEmail1">E-Paper Slug :<span class="ml-1 m--font-danger" aria-required="true">*</span></label>
                                    </div>
                                    <div class="col-9">
                                        <input type="text" class="form-control m-input" id="slug" placeholder="E-Paper Slug" name="epaper[epaper_slug]" value="{{old('epaper.epaper_slug') ? old('epaper.epaper_slug') : (!empty($epaper) ? $epaper->epaper_slug : '')}}">
                                    </div>
                                </div>
                                <div class="form-group m-form__group d-flex px-0">
                                        <label class="col-md-3 control-label">E-Paper Edition :<span class="required" aria-required="true">*</span></label>
                                        <div class="col-md-9">
                                            <div class="input-group input-append date date-picker" data-date-format="yyyy-mm-dd" >
                                                <span class="input-group-addon add-on"><i class="glyphicon glyphicon-calendar"></i></span>
                                                <input class="form-control m-input" type="text" name="epaper[epaper_edition]" value="{{old('epaper.epaper') ? old('epaper.epaper') : (!empty($epaper) ? $epaper->epaper_edition : \Carbon\Carbon::now()->format('Y-m-d'))}}" required readonly/>
                                            </div>
                                        </div>
                                    </div>
                                <div class="form-group m-form__group d-flex px-0">
                                    <div class="col-3 d-flex justify-content-end py-3">
                                        <label for="exampleInputEmail1">E-Paper File :<span class="ml-1 m--font-danger" aria-required="true">*</span></label>
                                    </div>
                                    <div class="col-9">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" name="epaper[epaper_file]">
                                            <label class="custom-file-label" for="customFile">{{old('epaper.epaper_file') ? old('epaper.epaper_file') : (!empty($epaper) ? $epaper->epaper_file : '')}}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{csrf_field()}}
                            @if(isset($_GET['code']))
                                <input type="hidden" name="{{\Modules\EPaper\Entities\EPaper::getPrimaryKey()}}" value="{{$_GET['code']}}">
                            @endif
                            {{$method}}

                        <!--end::Form-->
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="col-12 px-0">
                        <div class="m-portlet m-portlet--tab">
                            <!--begin::Form-->
                                <div class="m-portlet__head">
                                    <div class="m-portlet__head-caption">
                                        <div class="m-portlet__head-title">
                                            <span class="m-portlet__head-icon m--hide">
                                                <i class="fa fa-gear"></i>
                                            </span>
                                            <h3 class="m-portlet__head-text">
                                                Options
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="m-portlet__body px-0">
                                    <div class="form-group m-form__group d-flex px-0 flex-wrap">
                                        <div class="col-7 d-flex">
                                            <label for="exampleInputEmail1">Publish E-Paper<span class="ml-1 m--font-danger" aria-required="true">*</span></label>
                                        </div>
                                        <div class="col-5">
                                            <span class="m-switch m-switch--icon m-switch--danger">
                                                <label>
                                                    <input type="checkbox" {{old('epaper.epaper_status') ? 'checked' : ((!empty($epaper) && $epaper->epaper_status == 'publish' ? 'checked' : ''))}} name="epaper[epaper_status]">
                                                    <span></span>
                                                </label>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            <!--end::Form-->
                        </div>
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