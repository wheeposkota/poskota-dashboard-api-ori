@if(isset($taxonomies))
	@if($taxonomies->count() > 0)
	    <select name="ad_taxonomy_id[]" class="form-control" required>
            <option disabled selected>-- Please Select Subcategory --</option>
	        @foreach ($taxonomies as $taxonomy)
	            <option value="{{encrypt($taxonomy->getKey())}}">{{$taxonomy->term->name}}</option>
	        @endforeach
	    </select>
	@else
	    <input type="hidden" name="ad_taxonomy_id[]">
	@endif
@else
    <input type="hidden" name="ad_taxonomy_id[]">
@endif

@if(isset($locations))
	@if($locations->count() > 0)
	    <select name="mst_city_id[]" class="form-control" required>
            <option disabled selected>-- Please Select Location --</option>
	        @foreach ($locations as $location)
	            <option value="{{encrypt($location->getKey())}}">{{$location->name}}</option>
	        @endforeach
	    </select>
	@else
	    <input type="hidden" name="mst_city_id[]">
	@endif
@else
    <input type="hidden" name="mst_city_id[]">
@endif