@if($taxonomies->count() > 0)
    <select name="ad_taxonomy_id[]" class="form-control" required>
        <option disabled selected>-- Please Select Subcategory --</option>
        @foreach ($taxonomies as $taxonomy)
            <option value="{{encrypt($taxonomy->getKey())}}" {{$taxonomy->getKey() == $ad_taxonomy_id ? 'selected' : ''}}>{{$taxonomy->term->name}}</option>
        @endforeach
    </select>
@else
    <input type="hidden" name="ad_taxonomy_id[]">
@endif