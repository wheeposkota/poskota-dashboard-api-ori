@if($locations->count() > 0)
    <select name="mst_city_id[]" class="form-control" required>
        <option disabled selected>-- Please Select Location --</option>
        @foreach ($locations as $location)
            <option value="{{encrypt($location->getKey())}}" {{$location->getKey() == $mst_city_id ? 'selected' : ''}}>{{$location->name}}</option>
        @endforeach
    </select>
@else
    <input type="hidden" name="mst_city_id[]">
@endif