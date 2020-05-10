@extends('core::admin.'.$theme_cms->value.'.templates.parent')

@section('page_level_css')
    {{Html::style(module_asset_url('core:assets/metronic-v5/global/plugins/bootstrap-tagsinput/bootstrap-tagsinput.css'))}}
@endsection

@section('title_dashboard', ' Web Ads Transaction')

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

        <!--begin::Portlet-->
        <div class="m-portlet m-portlet--tab">
            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <div class="m-portlet__head-title">
                        <span class="m-portlet__head-icon m--hide">
                            <i class="fa fa-gear"></i>
                        </span>
                        <h3 class="m-portlet__head-text">
                            Master Data of Web Ads Transaction
                        </h3>
                    </div>
                </div>
            </div>

            <div class="m-portlet__body">
                @if (!empty(session('global_message')))
                    <div class="col-md-8">
                        <div class="alert {{session('global_message')['status'] == 200 ? 'alert-info' : 'alert-warning' }} alert-dismissible fade show">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
                            {{session('global_message')['message']}}
                        </div>
                    </div>
                @endif
                @if (count($errors) > 0)
                    <div class="col-md-8">
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <div class="row mb-4">
                    @can('create-transaction-web-ads')
                            <div class="">
                                <a href="{{action('\Modules\Ads\Http\Controllers\TrxAdsWebController@create')}}" class="btn btn-brand m-btn m-btn--custom m-btn--icon m-btn--pill m-btn--air">
                                    <span>
                                        <i class="la la-plus"></i>
                                        <span>Add New Transaction</span>
                                    </span>
                                </a>
                            </div>
                    @endcan
                    @canany(['read-all-transaction-web-ads', 'create-transaction-web-ads', 'approving-payment-web-ads', 'approving-content-web-ads', 'layouting-content-web-ads'])
                        <div class="col mt-2 mt-sm-0 ml-auto text-sm-right">
                            <button class="btn btn-danger" data-toggle="modal" data-target="#myModalprint">Filter Report</button>
                        </div>
                    @endcan
                </div>

                <!--begin: Datatable -->
                <table class="table table-striped responsive data-table-ajax" data-ajax="{{action('\Modules\Ads\Http\Controllers\TrxAdsWebController@serviceMaster')}}" id="data-term" width="100%">
                    <thead>
                        <tr>
                            <th data-priority="1">ID</th>
                            <th>Position</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th data-priority="3">Total</th>
                            <th>Publish Date</th>
                            <th data-priority="5">Author</th>
                            <th data-priority="4">Status</th>
                            <th>Created At</th>
                            <th data-priority="2" class="no-sort">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>

                <div class="modal fade" id="myModalprint" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-md d-flex align-items-center h-100" role="document">
                        <form action="{{action('\Modules\Ads\Http\Controllers\TrxAdsWebController@report')}}" method="get">
                            <div class="modal-content">            
                                <div class="modal-body">
                                        <div class="form-group m-form__group d-md-flex">
                                            <div class="col-md-5 d-md-flex justify-content-end py-3">
                                                <label for="exampleInputEmail1">Date Start :<span class="ml-1 m--font-danger" aria-required="true">*</span></label>
                                            </div>
                                            <div class="col">
                                                <div class="input-group input-append date date-picker" data-date-format="yyyy-mm-dd" comment-data-date-end-date="-1d">
                                                    <span class="input-group-addon add-on"><i class="glyphicon glyphicon-calendar"></i></span>
                                                    <input class="form-control m-input" type="text" name="date_start" value="" required readonly/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group m-form__group d-md-flex">
                                            <div class="col-md-5 d-md-flex justify-content-end py-3">
                                                <label for="exampleInputEmail1">Date End :<span class="ml-1 m--font-danger" aria-required="true">*</span></label>
                                            </div>
                                            <div class="col">
                                                <div class="input-group input-append date date-picker" data-date-format="yyyy-mm-dd" comment-data-date-end-date="-1d">
                                                    <span class="input-group-addon add-on"><i class="glyphicon glyphicon-calendar"></i></span>
                                                    <input class="form-control m-input" type="text" name="date_end" value="" required readonly/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group m-form__group d-md-flex">
                                            <div class="col-md-5 d-md-flex justify-content-end py-3">
                                                <label for="exampleInputEmail1">Agent :<span class="ml-1 m--font-danger" aria-required="true">*</span></label>
                                            </div>
                                            <div class="col">
                                                <select class="form-control" name="agent">
                                                    @canany(['read-all-transaction-web-ads', 'approving-payment-web-ads', 'approving-content-web-ads', 'layouting-content-web-ads'])
                                                        <option value="all">All Agent</option>
                                                        @foreach ($agents_report as $agent)
                                                            <option value="{{encrypt($agent->getKey())}}">{{$agent->name}}</option>
                                                        @endforeach
                                                    @else
                                                        <option value="{{encrypt(Auth::user()->id)}}">{{Auth::user()->name}}</option>
                                                    @endcan
                                                </select>
                                            </div>
                                        </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-accent">Generate</button>
                                </div>
                            </div><!-- /.modal-content -->
                        </form>
                    </div><!-- /.modal-dialog -->
                </div>

                <!--end: Datatable -->
            </div>


        </div>

        <!--end::Portlet-->

    </div>
</div>
{{-- End of Row --}}

@endsection

@section('page_level_js')
    {{Html::script(module_asset_url('core:assets/metronic-v5/global/plugins/bootstrap-tagsinput/bootstrap-tagsinput.min.js'))}}
    {{Html::script(module_asset_url('core:assets/js/autosize.min.js'))}}
    {{Html::script('vendor/laravel-filemanager/js/lfm.js')}}
@endsection