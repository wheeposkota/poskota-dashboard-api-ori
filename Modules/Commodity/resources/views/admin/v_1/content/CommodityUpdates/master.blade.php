@extends('core::admin.'.$theme_cms->value.'.templates.parent')

@section('title_dashboard', ' Commody Update')

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
                    <span class="m-nav__link-text">Commody Update</span>
                </a>
            </li>
        </ul>
@endsection

@section('content')

<div class="row">
    <div class="col-md-5">
        <div class="m-portlet m-portlet--tab m-portlet--skin-dark m-portlet--bordered-semi m--bg-brand">
            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <div class="m-portlet__head-title">
                        <h3 class="m-portlet__head-text">
                            Detail by Click ID Master
                        </h3>
                    </div>
                </div>
            </div>
            <div class="m-portlet__body" id="commodity-update" data-member="{{action('\Modules\Commodity\Http\Controllers\CommodityUpdateController@getData')}}" v-cloak>
                <div class="m-widget13">
                    <div class="m-widget13__item">
                        <span class="m-widget13__desc m--align-right text-white">
                            ID :
                        </span>
                        <span class="m-widget13__text m-widget13__text-bolder text-white">
                            @{{ data.id != undefined ? data.id : '-' }}
                        </span>
                    </div>
                    <div class="m-widget13__item">
                        <span class="m-widget13__desc m--align-right text-white">
                            Name :
                        </span>
                        <span class="m-widget13__text m-widget13__text-bolder text-white">
                            @{{ data.type != undefined ? data.type : '-' }}
                        </span>
                    </div>
                    <div class="m-widget13__item">
                        <span class="m-widget13__desc m--align-right text-white">
                            Content :
                        </span>
                        <span class="m-widget13__text m-widget13__text-bolder text-white">
                            @{{ data.content != undefined ? data.content : '-' }}
                        </span>
                    </div>
                    <div class="m-widget13__item">
                        <span class="m-widget13__desc m--align-right text-white">
                            Price :
                        </span>
                        <span class="m-widget13__text m-widget13__text-bolder text-white">
                            @{{ data.price != undefined ? data.price : '-' }}
                        </span>
                    </div>
                    <div class="m-widget13__item">
                        <span class="m-widget13__desc m--align-right text-white">
                            Author :
                        </span>
                        <span class="m-widget13__text m-widget13__text-bolder text-white">
                            @{{ data.author != undefined ? data.author : '-' }}
                        </span>
                    </div>
                    <div class="m-widget13__item">
                        <span class="m-widget13__desc m--align-right text-white">
                            Created_at :
                        </span>
                        <span class="m-widget13__text m-widget13__text-bolder text-white">
                            @{{ data.created_at != undefined ? data.created_at : '-' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-7">

        <!--begin::Portlet-->
        <div class="m-portlet m-portlet--tab">
            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <div class="m-portlet__head-title">
                        <span class="m-portlet__head-icon m--hide">
                            <i class="fa fa-gear"></i>
                        </span>
                        <h3 class="m-portlet__head-text">
                            Master Data of Commodity Update
                        </h3>
                    </div>
                </div>
            </div>

            <div class="m-portlet__body">
                <div class="col-md-8">
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

                <!--begin: Datatable -->
                <table class="table table-striped responsive data-table-ajax" id="data-Member" data-ajax="{{action('\Modules\Commodity\Http\Controllers\CommodityUpdateController@serviceMaster')}}" width="100%">
                    <thead>
                        <tr>
                            <th data-priority="1">ID</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Author</th>
                            <th>Created At</th>
                            <th data-priority="2" class="no-sort">Action</th>
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
{{-- End of Row --}}

@endsection

@section('page_level_js')
    {{Html::script(module_asset_url('Commodity:resources/views/admin/'.$theme_cms->value.'/js/commodity_update.js').'?id='.filemtime(module_asset_path('Commodity:resources/views/admin/'.$theme_cms->value.'/js/commodity_update.js')))}}
@endsection