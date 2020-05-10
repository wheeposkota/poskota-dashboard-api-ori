@extends('core::admin.'.$theme_cms->value.'.templates.parent')

@section('title_dashboard', 'Ads Package')

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
                    <span class="m-nav__link-text">Ads Package</span>
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
                            Ads Package Form
                        </h3>
                    </div>
                </div>
            </div>

            <!--begin::Form-->
            <form class="m-form m-form--fit m-form--label-align-right" action="{{action('\Modules\Ads\Http\Controllers\RltAdsCategoriesController@store')}}" method="post">
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
                                        <li>{!! $error !!}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                    <div class="form-group m-form__group d-md-flex">
                        <div class="col-md-4 d-md-flex justify-content-end py-3">
                            <label for="exampleInputEmail1">Ads Category :<span class="ml-1 m--font-danger" aria-required="true">*</span></label>
                        </div>
                        <div class="col">
                            <select class="form-control select2" name="category_id">
                                <option value="" disabled selected>-- Select Ads Category --</option>
                                @foreach ($mst_ad_categories as $mst_ad_category)
                                    @if(old('category_id'))
                                        <option value="{{$mst_ad_category->getKey()}}" {{old('category_id') && old('category_id') == $mst_ad_category->getKey() ? 'selected' : ''}}>{{$mst_ad_category->mst_ad_cat_name}}</option>
                                    @else
                                        <option value="{{$mst_ad_category->getKey()}}" {{!empty($rlt_ad_category) && $rlt_ad_category->category_id == $mst_ad_category->getKey() ? 'selected' : ''}}>{{$mst_ad_category->mst_ad_cat_name}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group m-form__group d-md-flex">
                        <div class="col-md-4 d-md-flex justify-content-end py-3">
                            <label for="exampleInputEmail1">Ads Type :<span class="ml-1 m--font-danger" aria-required="true">*</span></label>
                        </div>
                        <div class="col">
                            <select class="form-control select2" name="ads_id">
                                <option value="" disabled selected>-- Select Ads Category --</option>
                                @foreach ($mst_ads as $mst_ad)
                                    @if(old('ads_id'))
                                        <option value="{{$mst_ad->getKey()}}" {{old('ads_id') && old('ads_id') == $mst_ad->getKey() ? 'selected' : ''}}>{{$mst_ad->mst_ad_name}}</option>
                                    @else
                                        <option value="{{$mst_ad->getKey()}}" {{!empty($rlt_ad_category) && $rlt_ad_category->ads_id == $mst_ad->getKey() ? 'selected' : ''}}>{{$mst_ad->mst_ad_name}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group m-form__group d-md-flex">
                        <div class="col-md-4 d-md-flex justify-content-end py-3">
                            <label for="exampleInputEmail1">Minimum Line :</label>
                        </div>
                        <div class="col">
                            <input type="number" min="1" class="form-control m-input" name="min_line" placeholder="Minimum Line On Ads Allowed" value="{{old('min_line') ? old('min_line') : (!empty($rlt_ad_category) ? $rlt_ad_category->min_line : '')}}">
                        </div>
                    </div>
                    <div class="form-group m-form__group d-md-flex">
                        <div class="col-md-4 d-md-flex justify-content-end py-3">
                            <label for="exampleInputEmail1">Maximal Line :<span class="ml-1 m--font-danger" aria-required="true">*</span></label>
                        </div>
                        <div class="col">
                            <input type="number" min="1" class="form-control m-input" name="max_line" placeholder="Maximal Line On Ads Allowed" value="{{old('max_line') ? old('max_line') : (!empty($rlt_ad_category) ? $rlt_ad_category->max_line : '')}}">
                        </div>
                    </div>
                    <div class="form-group m-form__group d-md-flex">
                        <div class="col-md-4 d-md-flex justify-content-end py-3">
                            <label for="exampleInputEmail1">Character Each Line :<span class="ml-1 m--font-danger" aria-required="true">*</span></label>
                        </div>
                        <div class="col">
                            <input type="number" min="1" class="form-control m-input" name="char_on_line" placeholder="Character Count on Each Line" value="{{old('char_on_line') ? old('char_on_line') : (!empty($rlt_ad_category) ? $rlt_ad_category->char_on_line : '')}}">
                        </div>
                    </div>
                    <div class="form-group m-form__group d-md-flex">
                        <div class="col-md-4 d-md-flex justify-content-end py-3">
                            <label for="exampleInputEmail1">Ads Price :<span class="ml-1 m--font-danger" aria-required="true">*</span></label>
                        </div>
                        <div class="col">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon1">
                                       Rp. 
                                    </span>
                                </div>
                                <input type="text" min="0" class="form-control m-input money-masking" placeholder="Ads Price" name="price" value="{{old('price') ? old('price') : (!empty($rlt_ad_category) ? $rlt_ad_category->price : '')}}">
                            </div>
                        </div>
                    </div>
                </div>
                {{csrf_field()}}
                @if(isset($_GET['code']))
                    <input type="hidden" name="{{\Modules\Ads\Entities\RltAdsCategories::getPrimaryKey()}}" value="{{$_GET['code']}}">
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

@section('page_script_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $(".money-masking").inputmask('999,999,999', {
                numericInput: true,
            });
        });
    </script>
@endsection