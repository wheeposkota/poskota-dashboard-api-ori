@foreach ($categories as $category)
    <option value={{$category->getPrimaryKey()}}>{{$category->mst_ad_cat_name}}</option>
    @if(!empty($category->allChildrens))
        @include('ads::admin.'.$theme_cms->value.'.partials.ad_categories', ['categories' => $category->allChildrens])
    @endif
@endforeach