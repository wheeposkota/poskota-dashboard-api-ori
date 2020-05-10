@extends('core::admin.'.$theme_cms->value.'.templates.parent')

@section('page_level_css')
    {{Html::style(module_asset_url('core:assets/metronic-v5/global/plugins/bootstrap-tagsinput/bootstrap-tagsinput.css'))}}
    {{Html::style(module_asset_url('core:assets/metronic-v5/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css'))}}
    {{Html::style(module_asset_url('core:assets/metronic-v5/global/plugins/typeahead/typeaheadjs.css'))}}
@endsection

@section('title_dashboard', 'Video')

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
                    <span class="m-nav__link-text">Video</span>
                </a>
            </li>
        </ul>
@endsection

@section('content')

<div class="row">
    <div class="col-sm-12">

        <form class="m-form m-form--fit m-form--label-align-right" action="{{action('\Modules\Gallery\Http\Controllers\VideoController@store')}}" method="post" enctype="multipart/form-data">
            <!--begin::Portlet-->
            <div class="row">
                <div class="col-md-8">
                    <div class="m-portlet m-portlet--last m-portlet--head-lg m-portlet--responsive-mobile" id="main_portlet">
                        <div class="m-portlet__head">
                            <div class="m-portlet__head-wrapper">
                                <div class="m-portlet__head-caption">
                                    <div class="m-portlet__head-title">
                                        <h3 class="m-portlet__head-text">
                                            Video Form
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
                                <ul class="nav nav-tabs" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-toggle="tab" href="#" data-target="#content">Content</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#" data-target="#video">Video</a>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane active" id="content" role="tabpanel">
                                        <div class="form-group m-form__group d-flex px-0">
                                            <div class="col-3 d-flex justify-content-end py-3">
                                                <label for="exampleInputEmail1">Gallery Title :<span class="ml-1 m--font-danger" aria-required="true">*</span></label>
                                            </div>
                                            <div class="col-9">
                                                <input type="text" class="form-control m-input slugify" data-target="slug" placeholder="Gallery Title" name="gallery[gallery_title]" value="{{old('gallery.gallery_title') ? old('gallery.gallery_title') : (!empty($gallery) ? $gallery->gallery_title : '')}}">
                                            </div>
                                        </div>
                                        <div class="form-group m-form__group d-flex px-0">
                                            <div class="col-3 d-flex justify-content-end py-3">
                                                <label for="exampleInputEmail1">Gallery Slug :<span class="ml-1 m--font-danger" aria-required="true">*</span></label>
                                            </div>
                                            <div class="col-9">
                                                <input type="text" class="form-control m-input" id="slug" placeholder="gallery Slug" name="gallery[gallery_slug]" value="{{old('gallery.gallery_slug') ? old('gallery.gallery_slug') : (!empty($gallery) ? $gallery->gallery_slug : '')}}">
                                            </div>
                                        </div>
                                        <div class="form-group m-form__group d-flex px-0">
                                            <div class="col-12">
                                                <textarea class="form-control m-input texteditor" placeholder="gallery Content" name="gallery[gallery_content]">{{old('gallery.gallery_content') ? old('gallery.gallery_content') : (!empty($gallery) ? $gallery->gallery_content : '')}}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="video" role="tabpanel">
                                        <div class="tab-pane" id="gallery" role="tabpanel">
                                            <div class="form-group m-form__group d-flex">
                                                <div class="col">
                                                    <div v-for="(item, index) in (components)">
                                                        <div class="d-flex my-1">
                                                            <div class="col-md-10">
                                                                <div class="input-group">
                                                                    <div class="input-group-prepend">
                                                                        <span class="input-group-text" id="basic-addon1">
                                                                            https://www.youtube.com/watch?v=
                                                                        </span>
                                                                    </div>
                                                                   <input v-bind:id="'input-video-'+(index)" placeholder="Youtube ID" class="form-control file-inpu" v-bind:data-index="index" v-bind:data-name="'video'" type="text" v-bind:name="'gallery[gallery_source]['+(index)+'][video]'" v-model="components[index]['video']">
                                                                </div>
                                                                <a  v-bind:href="components[index]['video'] != null ? 'https://youtube.com/watch?v='+components[index]['video'] : 'javascript:void(0)'" target="_blank">
                                                                   <img v-bind:id="'image-video-'+(index)" style="margin-top:15px;max-height:100px;" v-bind:src="components[index]['video'] != null ? 'https://img.youtube.com/vi/'+components[index]['video']+'/default.jpg' : ''">
                                                                </a>
                                                            </div>
                                                            <div class="col">
                                                                <button type="button" class="btn m-btn--pill btn-metal" v-on:click="removeComponent(index)"><span><i class="fa fa-minus"></i></span></button>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex my-1">
                                                            <div class="col-md-10">
                                                                <textarea v-bind:name="'gallery[gallery_source]['+(index)+'][caption]'" v-model="components[index]['caption']" class="form-control" placeholder="Add A Caption For Video"></textarea>
                                                            </div>
                                                        </div>
                                                        <hr>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group m-form__group d-flex">
                                                <div class="col-md-6 offset-md-4">
                                                    <button type="button" class="btn btn-success" v-on:click="addComponent">Add Video</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{csrf_field()}}
                            @if(isset($_GET['code']))
                                <input type="hidden" name="{{\Modules\Gallery\Entities\Gallery::getPrimaryKey()}}" value="{{$_GET['code']}}">
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
                                            <label for="exampleInputEmail1">Publish Photo<span class="ml-1 m--font-danger" aria-required="true">*</span></label>
                                        </div>
                                        <div class="col-5">
                                            <span class="m-switch m-switch--icon m-switch--danger">
                                                <label>
                                                    <input type="checkbox" {{old('gallery.gallery_status') ? 'checked' : ((!empty($gallery) && $gallery->gallery_status == 'publish' ? 'checked' : ''))}} name="gallery[gallery_status]">
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
    {{Html::script('vendor/laravel-filemanager/js/lfm.js')}}
@endsection

@section('page_script_js')
    <script type="text/javascript">
        var Gallery = new Vue({
            mixins: [componentMixin],
            el: "#gallery",
            data: {
                components: {!! old('gallery.gallery_source') ? json_encode(old('gallery.gallery_source')) : (!empty($gallery) ? json_encode($gallery->gallery_source) : json_encode(array())) !!},
            },
        });
    </script>
@endsection