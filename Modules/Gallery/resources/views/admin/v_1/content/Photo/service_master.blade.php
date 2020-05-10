<div class="col">
    <div class="btn-group">
        <a href="javascript:void(0)" class="btn btn-outline-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Actions
        </a>
        <div class="dropdown-menu dropdown-menu-left">
            <button class="dropdown-item" type="button">
                <a class="m-link m-link--state m-link--warning" href="{{url(config('app.frontend_url').'/foto/'.$gallery->gallery_slug)}}" target="_blank"><i class="fa fa-eye"> Preview</i></a>
            </button>
            @can('update-gallery', $gallery)
                <button class="dropdown-item" type="button">
                    <a class="m-link m-link--state m-link--info" href="{{action('\Modules\Gallery\Http\Controllers\PhotoController@create').'?code='.encrypt($gallery->getKey())}}"><i class="fa fa-edit"> Edit</i></a>
                </button>
            @endcan
            @can('delete-gallery', $gallery)
                <form action="{{action('\Modules\Gallery\Http\Controllers\PhotoController@destroy')}}" method="post" accept-charset="utf-8">
                    {{method_field('DELETE')}}
                    {{csrf_field()}}
                    <input type="hidden" name="{{\Modules\Gallery\Entities\Gallery::getPrimaryKey()}}" value="{{encrypt($gallery->getKey())}}">
                </form>
                <button class="dropdown-item confirm-delete" type="button"><a class="m-link m-link--state m-link--accent" data-toggle="modal" href="#small"><i class="fa fa-trash"> Delete</i></a></button>
            @endcan
        </div>
    </div>
</div>