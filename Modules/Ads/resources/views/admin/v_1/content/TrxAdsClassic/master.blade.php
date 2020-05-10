@extends('core::admin.'.$theme_cms->value.'.templates.parent')

@section('page_level_css')
    {{Html::style(module_asset_url('core:assets/metronic-v5/global/plugins/bootstrap-tagsinput/bootstrap-tagsinput.css'))}}
@endsection

@section('title_dashboard', ' Iklan Baris')

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
                    <span class="m-nav__link-text">Iklan Baris</span>
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
                            Master Data of Iklan Baris
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
                    @can('create-transaction-classic-ads')
                        <div class="ml-md-4">
                            <div class="dropdown">
                                <button class="btn btn-brand dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Tambah Iklan Baris
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 40px, 0px);">
                                    <a class="dropdown-item" href="{{action('\Modules\Ads\Http\Controllers\TrxAdsClassicController@create').'?ads='.encrypt('iklan-umum')}}">Iklan Umum</a>
                                    <a class="dropdown-item" href="{{action('\Modules\Ads\Http\Controllers\TrxAdsClassicController@create').'?ads='.encrypt('iklan-khusus')}}">Iklan Khusus</a>
                                </div>
                            </div>
                        </div>
                    @endcan
                    <div class="ml-2">
                        <button class="btn btn-info dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Aksi Sekaligus
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 40px, 0px);">
                            @can('layouting-content-classic-ads')
                                <a class="dropdown-item" href="javascript:void(0)" data-action="{{action('\Modules\Ads\Http\Controllers\TrxAdsClassicController@bulkApprovingContent')}}" id="bulk-close-session">Tutup Sesi</a>
                            @endcan
                        </div>
                    </div>
                    <div class="ml-1 ml-md-auto mt-2 mt-sm-0 row justify-content-end pr-2">
                        @canany(['read-all-transaction-classic-ads', 'create-transaction-classic-ads', 'approving-payment-classic-ads', 'approving-content-classic-ads', 'layouting-content-classic-ads'])
                            <div class="mr-2">
                                <button class="btn btn-danger" data-toggle="modal" data-target="#myModalprint">Saring Laporan</button>
                            </div>
                        @endcan
                        @canany(['layouting-content-classic-ads'])
                            <div class="mr-2">
                                <a href="{{action('\Modules\Ads\Http\Controllers\TrxAdsClassicController@layotingPrint')}}" class="btn btn-warning" target="_blank">Layout Print</a>
                            </div>
                        @endcan
                    </div>
                </div>

                <!--begin: Datatable -->
                <table class="table table-striped responsive" data-ajax="{{action('\Modules\Ads\Http\Controllers\TrxAdsClassicController@serviceMaster')}}" id="data-ads" width="100%">
                    <thead>
                        <tr>
                            <th data-priority="1"><input style="width: 20px" type="checkbox" id="checkall"></th>
                            <th data-priority="1">ID</th>
                            <th data-priority="3" class="no-sort">Tipe</th>
                            <th data-priority="4" class="no-sort">Kategori</th>
                            <th>Konten</th>
                            <th data-priority="9">Kuantitas</th>
                            <th data-priority="10">Harga</th>
                            <th data-priority="7" class="no-sort">Tanggal Terbit</th>
                            <th data-priority="11">Diskon</th>
                            <th data-priority="5">Total</th>
                            <th data-priority="8" class="no-sort">Pembuat</th>
                            <th data-priority="6">Status</th>
                            <th>Tanggal Dibuat</th>
                            <th data-priority="2" class="no-sort">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>

                <!--end: Datatable -->
            </div>


        </div>

        <div class="modal fade" id="myModalprint" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-md d-flex align-items-center h-100" role="document">
                <form action="{{action('\Modules\Ads\Http\Controllers\TrxAdsClassicController@report')}}" method="get">
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
                                            @canany(['read-all-transaction-classic-ads', 'approving-payment-classic-ads', 'approving-content-classic-ads', 'layouting-content-classic-ads'])
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
                                <div class="form-group m-form__group d-md-flex">
                                    <div class="col-md-5 d-md-flex justify-content-end py-3">
                                        <label for="exampleInputEmail1">Status :<span class="ml-1 m--font-danger" aria-required="true">*</span></label>
                                    </div>
                                    <div class="col">
                                        <select class="form-control" name="status">
                                            <option value="all">All (Total Pembayaran)</option>
                                            <option value="{{\App\Models\TrxOrder::TRX_PENDING}}">{{\App\Models\TrxOrder::TRX_PENDING}}</option>
                                            <option value="{{\App\Models\TrxOrder::TRX_CONFIRM}}">{{\App\Models\TrxOrder::TRX_CONFIRM}}</option>
                                            <option value="{{\App\Models\TrxOrder::TRX_DONE}}">{{\App\Models\TrxOrder::TRX_DONE}}</option>
                                            <option value="{{\App\Models\TrxOrder::TRX_APPROVED}}">{{\App\Models\TrxOrder::TRX_APPROVED}}</option>
                                            <option value="{{\App\Models\TrxOrder::TRX_EXPIRE}}">{{\App\Models\TrxOrder::TRX_EXPIRE}}</option>
                                            <option value="{{\App\Models\TrxOrder::TRX_CANCEL}}">{{\App\Models\TrxOrder::TRX_CANCEL}}</option>
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

@section('page_script_js')
    <script type="text/javascript">
        $("#data-ads").DataTable( {
            "pagingType": "full_numbers",
            "processing": true,
            "serverSide": true,
            "order": [
                @if(\Auth::user()->role->first()->slug == 'layout-iklan')
                    [ 11, "asc" ]
                @endif
            ],
            "ajax": $.fn.dataTable.pipeline( {
                url: $(this).attr('data-ajax'),
                pages: 5 // number of pages to cache
            }),
             "columnDefs": [
                {
                    "targets": [ 0 ],
                    "orderable": false
                },
                @if(\Auth::user()->role->first()->slug == 'layout-iklan')
                    {
                        "targets": [ 2 ],
                        "visible": false
                    },
                    {
                        "targets": [ 5 ],
                        "visible": false
                    },
                    {
                        "targets": [ 6 ],
                        "visible": false
                    },
                    {
                        "targets": [ 8 ],
                        "visible": false
                    },
                    {
                        "targets": [ 9 ],
                        "visible": false
                    }
                @endif
            ],
            "drawCallback": function( settings ) {
                deleteData();
                $("#bulk-close-session").click(function(event) {
                    var values = $("input[name='id[]']:checked").map(function(){return $(this).val();}).get();
                    window.location.href = $(this).attr('data-action')+'?code='+values;
                });
                $("#checkall").change(function(event) {
                    $('.checkitem').prop("checked", $(this).prop("checked"));
                });
            },
            "initComplete": function(settings, json) {
                var $searchBox = $("div.dataTables_filter input");
                $searchBox.unbind();
                var searchDebouncedFn = debounce(function() {
                    var api = new $.fn.dataTable.Api( settings );
                    api.search( this.value ).draw();
                }, 1000);
                $searchBox.on("keyup", searchDebouncedFn);
            }
        } );
    </script>
@endsection