@extends('core::admin.'.$theme_cms->value.'.templates.parent')

@section('title_dashboard', 'Ads Category')

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
                    <span class="m-nav__link-text">Ads Category</span>
                </a>
            </li>
        </ul>
@endsection

@section('content')

<div class="row">
    <div class="col-sm-12">

        <!--begin::Portlet-->
        <div class="m-portlet m-portlet--tab">
            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <div class="m-portlet__head-title">
                        <span class="m-portlet__head-icon m--hide">
                            <i class="fa fa-gear"></i>
                        </span>
                        <h3 class="m-portlet__head-text">
                            Ads Category Form
                        </h3>
                    </div>
                </div>
            </div>

            <!--begin::Form-->
            <form class="m-form m-form--fit m-form--label-align-right" action="{{action('\Modules\Ads\Http\Controllers\MstAdCategoriesController@store')}}" method="post">
                <div class="m-portlet__body">
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
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                    <div class="form-group m-form__group d-flex">
                        <div class="col-md-4 d-flex justify-content-end py-3">
                            <label for="exampleInputEmail1">Category Name :<span class="ml-1 m--font-danger" aria-required="true">*</span></label>
                        </div>
                        <div class="col-md-8">
                            <input type="text" class="form-control m-input slugify" data-target="slug" name="mst_ad_cat_name" placeholder="Category Name" value="{{old('mst_ad_cat_name') ? old('mst_ad_cat_name') : (!empty($mst_ad_category) ? $mst_ad_category->mst_ad_cat_name : '')}}">
                        </div>
                    </div>
                    <div class="form-group m-form__group d-flex">
                        <div class="col-md-4 d-flex justify-content-end py-3">
                            <label for="exampleInputEmail1">Category Slug :<span class="ml-1 m--font-danger" aria-required="true">*</span></label>
                        </div>
                        <div class="col-md-8">
                            <input type="text" class="form-control m-input" name="mst_ad_cat_slug" id="slug" placeholder="Category Slug" value="{{old('mst_ad_cat_slug') ? old('mst_ad_cat_slug') : (!empty($mst_ad_category) ? $mst_ad_category->mst_ad_cat_slug : '')}}">
                        </div>
                    </div>
                    <div class="form-group m-form__group d-flex">
                        <div class="col-md-4 d-flex justify-content-end py-3">
                            <label for="exampleInputEmail1">Category Parent :</label>
                        </div>
                        <div class="col-md-8">
                            <select name="mst_parent_id" class="form-control m-input m-input--solid select2">
                                <option value="" selected>-- Non Parent --</option>
                                @foreach ($parents as $parent)
                                    @if(old('mst_parent_id'))
                                        <option value="{{$parent->getKey()}}" {{old('mst_parent_id') && old('mst_parent_id') == $parent->getKey() ? 'selected' : ''}}>-- {{ucfirst($parent->mst_ad_cat_name)}} --</option>
                                    @else
                                        <option value="{{$parent->getKey()}}" {{!empty($mst_ad_category) && $mst_ad_category->mst_parent_id == $parent->getKey() ? 'selected' : ''}}>-- {{ucfirst($parent->mst_ad_cat_name)}} --</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                {{csrf_field()}}
                @if(isset($_GET['code']))
                    <input type="hidden" name="{{\Modules\Ads\Entities\MstAds::getPrimaryKey()}}" value="{{$_GET['code']}}">
                @endif
                {{$method}}
                <div class="m-portlet__foot m-portlet__foot--fit">
                    <div class="m-form__actions">
                        <div class="offset-md-4">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </div>
            </form>

            <!--end::Form-->
        </div>

        <!--end::Portlet-->

    </div>
</div>
{{-- End of Row --}}

@endsection

@section('page_level_js')
    {{Html::script(module_asset_url('core:assets/js/autosize.min.js'))}}
    {{Html::script(module_asset_url('core:assets/js/slugify.js'))}}
@endsection