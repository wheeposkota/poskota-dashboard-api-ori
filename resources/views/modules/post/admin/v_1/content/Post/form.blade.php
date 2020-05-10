@extends('core::admin.'.$theme_cms->value.'.templates.parent')

@section('page_level_css')
    {{Html::style(module_asset_url('core:assets/metronic-v5/global/plugins/bootstrap-tagsinput/bootstrap-tagsinput.css'))}}
    {{Html::style(module_asset_url('core:assets/metronic-v5/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css'))}}
    {{Html::style(module_asset_url('core:assets/metronic-v5/global/plugins/typeahead/typeaheadjs.css'))}}
@endsection

@section('title_dashboard', 'Post')

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
                    <span class="m-nav__link-text">Post</span>
                </a>
            </li>
        </ul>
@endsection

@section('content')

<div class="row">
    <div class="col-sm-12">

        <form class="m-form m-form--fit m-form--label-align-right" action="{{route('cms.post-data.store', ['callback' => 'saveRelatedPost'])}}" method="post" enctype="multipart/form-data">
            <!--begin::Portlet-->
            <div class="row">
                <div class="col-md-8 pl-0">
                    <div class="col-12">
                        <div class="m-portlet m-portlet--head-lg m-portlet--responsive-mobile" id="main_portlet">
                            <div class="m-portlet__head">
                                <div class="m-portlet__head-wrapper">
                                    <div class="m-portlet__head-caption">
                                        <div class="m-portlet__head-title">
                                            <h3 class="m-portlet__head-text">
                                                Post Form
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
                                <div class="m-portlet__body p-0">
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
                                    <div class="form-group m-form__group d-md-flex px-0">
                                        <div class="col-md-3 d-md-flex justify-content-end py-3">
                                            <label for="exampleInputEmail1">Post Title<span class="ml-1 m--font-danger" aria-required="true">*</span></label>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control m-input slugify" data-target="slug" placeholder="Post Title" name="post[post_title]" value="{{old('post.post_title') ? old('post.post_title') : (!empty($post) ? $post->post_title : '')}}">
                                        </div>
                                    </div>
                                    <div class="form-group m-form__group d-md-flex px-0">
                                        <div class="col-md-3 d-md-flex justify-content-end py-3">
                                            <label for="exampleInputEmail1">Post Slug<span class="ml-1 m--font-danger" aria-required="true">*</span></label>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control m-input" id="slug" placeholder="Post Slug" name="post[post_slug]" value="{{old('post.post_slug') ? old('post.post_slug') : (!empty($post) ? $post->post_slug : '')}}" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group m-form__group d-md-flex px-0 flex-wrap">
                                        <div class="col-md-3 d-md-flex justify-content-end py-3">
                                            <label for="exampleInputEmail1">Category</label>
                                        </div>
                                        <div class="col">
                                            <select class="form-control m-input select2" name="taxonomy[category][]">
                                                @foreach ($categories as $category)
                                                    <option value="{{$category->getKey()}}" {{!empty($post->taxonomies) && in_array($category->getKey(), $post->taxonomies->pluck(\Gdevilbat\SpardaCMS\Modules\Taxonomy\Entities\TermTaxonomy::getPrimaryKey())->toArray()) ? 'selected' : ''}}>{{$category->term->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group m-form__group d-md-flex px-0 flex-wrap">
                                        <div class="col-md-3 d-md-flex justify-content-end py-3">
                                            <label for="exampleInputEmail1">Tag</label>
                                        </div>
                                        <div class="col">
                                            <select class="form-control m-input taginput w-100" name="taxonomy[tag][]" multiple>
                                                @if(old('taxonomy.tag'))
                                                    @foreach(old('taxonomy.tag') as $tag)
                                                        <option value="{{$tag}}">{{$tag}}</option>
                                                    @endforeach
                                                @else
                                                    @foreach ($tags as $tag)
                                                        @if(!empty($post->taxonomies) && in_array($tag->getKey(), $post->taxonomies->pluck(\Gdevilbat\SpardaCMS\Modules\Taxonomy\Entities\TermTaxonomy::getPrimaryKey())->toArray()))
                                                            <option value="{{$tag->term->name}}">{{$tag->term->name}}</option>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group m-form__group d-md-flex px-0 flex-wrap">
                                        <div class="col-md-3 d-md-flex justify-content-end py-3">
                                            <label for="exampleInputEmail1">Related Article</label>
                                        </div>
                                        <div class="col">
                                            <select class="form-control m-input" id="related-post" name="related_post[]" multiple>
                                                @if(!empty($post))
                                                    @foreach ($post->relatedPost as $related_post)
                                                        <option value="{{$related_post->getKey()}}" selected>{{$related_post->post_title}}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group m-form__group d-md-flex px-0">
                                        <label class="col-md-3 control-label">Publish Date<span class="required" aria-required="true">*</span></label>
                                        <div class="col-md-9">
                                            <div class="input-group date">
                                                <input type="text" class="form-control m-input" readonly="" name="post[publish_date]" value="{{old('post.publish_date') ? old('post.publish_date') : (!empty($post) ? $post->publish_date : \Carbon\Carbon::now())}}" placeholder="Select date &amp; time" id="m_datetimepicker_2">
                                                <div class="input-group-append">
                                                    <span class="input-group-text"><i class="la la-calendar-check-o glyphicon-th"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group m-form__group d-md-flex px-0">
                                        <div class="col-12">
                                            <textarea class="form-control m-input texteditor" placeholder="Post Content" name="post[post_content]">{{old('post.post_content') ? old('post.post_content') : (!empty($post) ? $post->post_content : '')}}</textarea>
                                        </div>
                                    </div>
                                    <input type="hidden" name="post[post_excerpt]" value="{{old('post.post_excerpt') ? old('post.post_excerpt') : (!empty($post) ? $post->post_excerpt : '')}}">
                                </div>
                                {{csrf_field()}}
                                @if(isset($_GET['code']))
                                    <input type="hidden" name="{{\Gdevilbat\SpardaCMS\Modules\Post\Entities\Post::getPrimaryKey()}}" value="{{$_GET['code']}}">
                                @endif
                                {{$method}}

                            <!--end::Form-->
                        </div>
                    </div>
                    @can('comment-post')
                        <div class="col-12">
                            <div class="col-12 px-0">
                                <div class="m-portlet m-portlet--tab">
                                    <!--begin::Form-->
                                        <div class="m-portlet__body px-0">
                                            <div class="form-group m-form__group d-md-flex px-0">
                                                <label class="col-md-3 control-label">Additional Comment</label>
                                                <div class="col">
                                                    <textarea class="form-control m-input autosize" placeholder="Add A Comment about This Article Here" name="meta[cms_comment]">{{old('meta.cms_comment') ? old('meta.cms_comment') : (!empty($post) && $post->postMeta->where('meta_key', 'cms_comment')->first() ? $post->postMeta->where('meta_key', 'cms_comment')->first()->meta_value : '')}}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    <!--end::Form-->
                                </div>
                            </div>
                        </div>
                    @endcan
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
                                    @can('publish-post')
                                        <div class="form-group m-form__group d-md-flex px-0 flex-wrap">
                                            <div class="col-7 d-md-flex">
                                                <label for="exampleInputEmail1">Publish Post<span class="ml-1 m--font-danger" aria-required="true">*</span></label>
                                            </div>
                                            <div class="col-5">
                                                <span class="m-switch m-switch--icon m-switch--danger">
                                                    <label>
                                                        <input type="checkbox" {{old('post.post_status') ? 'checked' : ((!empty($post) && $post->post_status == 'publish' ? 'checked' : ''))}} name="post[post_status]">
                                                        <span></span>
                                                    </label>
                                                </span>
                                            </div>
                                        </div>
                                    @endcan
                                    <div class="form-group m-form__group d-md-flex px-0 flex-wrap">
                                        <div class="col-7 d-md-flex">
                                            <label for="exampleInputEmail1">Open Comment<span class="ml-1 m--font-danger" aria-required="true">*</span></label>
                                        </div>
                                        <div class="col-5">
                                            <span class="m-switch m-switch--icon m-switch--danger">
                                                <label>
                                                    <input type="checkbox" {{old('post.comment_status') ? 'checked' : ((!empty($post) && $post->comment_status == 'close' ? '' : 'checked'))}} name="post[comment_status]">
                                                    <span></span>
                                                </label>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-group m-form__group d-md-flex px-0 flex-wrap">
                                        <div class="col-7 d-md-flex">
                                            <label for="exampleInputEmail1">Highlight Post<span class="ml-1 m--font-danger" aria-required="true"></span></label>
                                        </div>
                                        <div class="col-5">
                                            <span class="m-switch m-switch--icon m-switch--danger">
                                                <label>
                                                    <input id="highlight-post" type="checkbox" {{old('post.int_highlight') ? 'checked' : ((!empty($post) && $post->int_highlight == 1 ? 'checked' : ''))}}>
                                                    <input id="highlight-post-value" type="hidden" name="post[int_highlight]" value="{{old('post.int_highlight') ? old('post.int_highlight') : (!empty($post) ? $post->int_highlight : 0)}}">
                                                    <span></span>
                                                </label>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-group m-form__group d-md-flex px-0 flex-wrap">
                                        <div class="col-7 d-md-flex">
                                            <label for="exampleInputEmail1">Popular Post<span class="ml-1 m--font-danger" aria-required="true"></span></label>
                                        </div>
                                        <div class="col-5">
                                            <span class="m-switch m-switch--icon m-switch--danger">
                                                <label>
                                                    <input id="popular-post" type="checkbox" {{old('post.int_popular') ? 'checked' : ((!empty($post) && $post->int_popular == 1 ? 'checked' : ''))}}>
                                                    <input id="popular-post-value" type="hidden" name="post[int_popular]" value="{{old('post.int_popular') ? old('post.int_popular') : (!empty($post) ? $post->int_popular : 0)}}">
                                                    <span></span>
                                                </label>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            <!--end::Form-->
                        </div>
                    </div>
                    <div class="col-12 px-0">
                        <div class="m-portlet m-portlet--tab">
                            <!--begin::Form-->
                                <div class="m-portlet__body px-0">
                                    <div class="form-group m-form__group d-md-flex px-0 flex-wrap">
                                        <div class="col-12 d-md-flex py-3">
                                            <label for="exampleInputEmail1">Feature Image</label>
                                        </div>
                                        <div class="col-12">
                                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                                <div class="fileinput-new thumbnail" style="width: 200px; height: 150px;">
                                                    @if(!empty($post) && !empty($post->postMeta->where('meta_key', 'cover_image')->first()) && $post->postMeta->where('meta_key', 'cover_image')->first()->meta_value['file'] != null)
                                                        <img src="{{generate_storage_url($post->postMeta->where('meta_key', 'cover_image')->first()->meta_value['file'])}}" alt=""> 
                                                    @else
                                                        <img src="http://www.placehold.it/200x150/EFEFEF/AAAAAA&amp;text=no+image" alt=""> 
                                                    @endif
                                                </div>
                                                <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 150px;"> </div>
                                                <div>
                                                    <span class="btn btn-file btn-accent m-btn m-btn--air m-btn--custom">
                                                        <span class="fileinput-new"> Select image </span>
                                                        <span class="fileinput-exists"> Change </span>
                                                        <input type="file" name="meta[cover_image][file]"> </span>
                                                    <a href="javascript:;" class="btn default fileinput-exists" data-dismiss="fileinput"> Remove </a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group m-form__group d-md-flex px-0 flex-wrap">
                                            <div class="col-12 d-md-flex py-3">
                                                <label for="exampleInputEmail1">Caption Image</label>
                                            </div>
                                            <div class="col-12">
                                                <input type="text" class="form-control m-input count-textarea" placeholder="Cover Caption" name="meta[cover_image][caption]" data-target-count-text="#caption-cover" value="{{old('meta.cover_image.caption') ? old('meta.cover_image.caption') : (!empty($post) && $post->postMeta->where('meta_key', 'cover_image')->first() ? $post->postMeta->where('meta_key', 'cover_image')->first()->meta_value['caption'] : '')}}">
                                                <div class="pt-1"><span id="caption-cover"></span> Character</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <!--end::Form-->
                        </div>
                    </div>
                    <div class="col-12 px-0">
                        <div class="m-portlet m-portlet--tab">
                            <!--begin::Form-->
                                <div class="m-portlet__body px-0">
                                    <div class="form-group m-form__group d-md-flex px-0 flex-wrap">
                                        <div class="col-12 d-md-flex py-3">
                                            <label for="exampleInputEmail1">Meta Title</label>
                                        </div>
                                        <div class="col-12">
                                            <input type="text" class="form-control m-input count-textarea" placeholder="Meta Title" name="meta[meta_title]" data-target-count-text="#meta-title" value="{{old('meta.meta_title') ? old('meta.meta_title') : (!empty($post) && $post->postMeta->where('meta_key', 'meta_title')->first() ? $post->postMeta->where('meta_key', 'meta_title')->first()->meta_value : '')}}">
                                            <div class="pt-1"><span id="meta-title"></span> Character</div>
                                        </div>
                                    </div>
                                    <div class="form-group m-form__group d-md-flex px-0 flex-wrap">
                                        <div class="col-12 d-md-flex py-3">
                                            <label for="exampleInputEmail1">Meta Keyword</label>
                                        </div>
                                        <div class="col-12">
                                            <input type="text" class="form-control m-input" placeholder="Meta Keyword" name="meta[meta_keyword]" value="{{old('meta.meta_keyword') ? old('meta.meta_keyword') : (!empty($post) && $post->postMeta->where('meta_key', 'meta_keyword')->first() ? $post->postMeta->where('meta_key', 'meta_keyword')->first()->meta_value : '')}}" data-role="tagsinput">
                                        </div>
                                    </div>
                                    <div class="form-group m-form__group d-md-flex px-0 flex-wrap">
                                        <div class="col-12 d-md-flex py-3">
                                            <label for="exampleInputEmail1">Meta Description</label>
                                        </div>
                                        <div class="col-12">
                                            <textarea class="form-control m-input autosize count-textarea" placeholder="Meta Description" name="meta[meta_description]" data-target-count-text="#meta-description">{{old('meta.meta_description') ? old('meta.meta_description') : (!empty($post) && $post->postMeta->where('meta_key', 'meta_description')->first() ? $post->postMeta->where('meta_key', 'meta_description')->first()->meta_value : '')}}</textarea>
                                            <div class="pt-1"><span id="meta-description"></span> Character</div>
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

@section('page_script_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#m_datetimepicker_2').datetimepicker();

            $("#highlight-post").on('change', function(){
                $("#highlight-post-value").val(this.checked ? 1 : 0);
            });

            $("#popular-post").on('change', function(){
                $("#popular-post-value").val(this.checked ? 1 : 0);
            });

            var tag = new Bloodhound({
              datumTokenizer: Bloodhound.tokenizers.obj.whitespace('name'),
              queryTokenizer: Bloodhound.tokenizers.whitespace,
              prefetch: {
                url: "{{action('\Gdevilbat\SpardaCMS\Modules\Taxonomy\Http\Controllers\TaxonomyController@getSuggestionTag')}}",
                cache: false,
                filter: function(list) {
                  return $.map(list, function(tag) {
                    return { name: tag };
                    });
                }
              }
            });
            tag.initialize();

            $('.taginput').tagsinput({
              typeaheadjs: {
                name: 'tag',
                displayKey: 'name',
                valueKey: 'name',
                source: tag.ttAdapter()
              }
            });

            $('#related-post').select2({
                ajax: {
                    url: '{{route('cms.post-data.suggestion-related')}}',
                    dataType: 'json',
                    data: function (params) {
                      var query = {
                        q: params.term,
                        id: {{!empty($post) ? $post->getKey() : 0}}
                      }

                      // Query parameters will be ?search=[term]&type=public
                      return query;
                    },
                    delay: 1000,
                    processResults: function (data, params) {
                      // parse the results into the format expected by Select2
                      // since we are using custom formatting functions we do not need to
                      // alter the remote JSON data, except to indicate that infinite
                      // scrolling can be used
                      params.page = params.page || 1;

                      return {
                        results: $.map(data, function (item) {
                            return {
                                text: item.post_title,
                                id: item.id_posts
                            }
                        }),
                        pagination: {
                          more: (params.page * 30) < data.total_count
                        }
                      };
                    },
                    // Additional AJAX parameters go here; see the end of this chapter for the full code of this example
                },
                placeholder: 'Search Related Article',
                minimumInputLength: 5,
            });
        });
    </script>
@endsection