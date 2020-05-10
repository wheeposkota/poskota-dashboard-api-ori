@extends('core::admin.'.$theme_cms->value.'.templates.parent')

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
                            Layouting Print
                        </h3>
                    </div>
                </div>
            </div>

            <div class="m-portlet__body">
                <!--begin: Datatable -->
                <div class="row mb-5">
                    <div class="col-md-8">
                        <div class="alert alert-warning" style="color: #a00000">
                            Iklan Semua Kategori Tanggal yang Sudah Tutup Sesi <br>
                            Terbit {{\Carbon\Carbon::now()->addDay()->format('d-m-Y')}} untuk Semua Sesi
                        </div>
                    </div>
                </div>

                <div>
                    <h4><span style="color: red;">Keterangan:</span> Iklan berwarna <span style="color: blue;">Biru</span> = Iklan Khusus</h4>
                    <em>Total : </em> {{$ad_categories->sum(function($product){return count($product->trxAdClassics);})}} Iklan
                </div>

                <br>

                <div class="table-responsive">
                    <table class="table" width="100%">
                        <tbody>
                            <tr>
                                <td style="color: white;background-color: #7A8b9b;"><center>Isi Iklan</center></td>
                            </tr>
                            <tr>
                                <td style="background-color: black;font-weight: bold;"></td>
                            </tr>
                            @foreach ($ad_categories as $ad_category)
                                <tr>
                                    <td style="background-color: #b7b7b7;font-weight: bold;"><center>{{$ad_category->mst_ad_cat_name}}</center></td>
                                </tr>
                                @foreach ($ad_category->trxAdClassics->groupBy(['city.name', 'rltTaxonomyAds.adTaxonomy.term.name']) as $keyCity => $cityGroup)
                                    @if(!empty($keyCity))
                                        <tr>
                                            <td style="background-color: #e4be05;"><center>{{$keyCity}}</center></td>
                                        </tr>
                                    @endif
                                    @foreach ($cityGroup as $keySub => $subCategory)
                                        @if(!empty($keySub))
                                            <tr>
                                                <td style="background-color: #e4be05;"><center>{{$keySub}}</center></td>
                                            </tr>
                                        @endif
                                        @php
                                            $sortedSubCategory = $subCategory->sortBy(function($product, $key){
                                                return preg_replace('/\s+/', ' ', strtolower(utf8_decode(html_entity_decode($product->trx_ad_content))));
                                            });
                                        @endphp
                                        @foreach($sortedSubCategory as $trxAdClassic)
                                            <tr>
                                                <td style="border-bottom: 1px solid gray;{{$trxAdClassic->rltAdCategory->mstAd->mst_ad_slug == 'iklan-khusus' ? 'color: blue' : ''}}">{!!preg_replace('/^[^\s]+\s+[^\s]+|^[^\s]+/', '<span style="text-transform: uppercase;"><b>$0</b></span>', str_replace('&nbsp;', " " , $trxAdClassic->trx_ad_content))!!}</td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                @endforeach
                                <tr>
                                    <td style="background-color: black;font-weight: bold;"></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!--end: Datatable -->
            </div>


        </div>

        <!--end::Portlet-->

    </div>
</div>
{{-- End of Row --}}

@endsection