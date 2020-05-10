@extends('core::admin.'.$theme_cms->value.'.templates.parent')

@section('title_dashboard', ' Categories')

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
                    <span class="m-nav__link-text">Categories</span>
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
                                Master Data of Categories
                            </h3>
                        </div>
                    </div>
                </div>

                <div class="m-portlet__body">
                    <div class="col-md-5">
                        @if (!empty(session('global_message')))
                            <div class="alert {{session('global_message')['status'] == 200 ? 'alert-info' : 'alert-warning' }} alert-dismissible fade show">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
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

                    @can('create-commodity')
                        <div class="row mb-4">
                            <div class="col-md-5">
                                <a href="{{action('\Modules\Commodity\Http\Controllers\CommodityCategoriesController@create')}}" class="btn btn-brand m-btn m-btn--custom m-btn--icon m-btn--pill m-btn--air">
                                    <span>
                                        <i class="la la-plus"></i>
                                        <span>Add New Category</span>
                                    </span>
                                </a>
                            </div>
                        </div>
                    @endcan

                    <!--begin: Datatable -->
                    <table class="table table-striped display responsive nowrap data-table-ajax" id="data-gallery" data-ajax="{{action('\Modules\Commodity\Http\Controllers\CommodityCategoriesController@serviceMaster')}}" width="100%">
                        <thead>
                            <tr>
                                <th data-priority="1">ID</th>
                                <th data-priority="2">Name</th>
                                <th>Created At</th>
                                <th class="no-sort" data-priority="3">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>

                    <!--end: Datatable -->
                </div>


            </div>

            <!--end::Portlet-->

        </div>
    </div>

@endsection