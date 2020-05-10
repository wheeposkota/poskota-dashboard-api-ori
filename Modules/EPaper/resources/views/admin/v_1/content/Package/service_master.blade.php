<div class="col">
    <div class="btn-group">
        <a href="javascript:void(0)" class="btn btn-outline-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Actions
        </a>
        <div class="dropdown-menu dropdown-menu-left">
            @can('update-epaper', $package)
                <button class="dropdown-item" type="button">
                    <a class="m-link m-link--state m-link--info" href="{{route('epaper-package.resource.create').'?code='.encrypt($package->getKey())}}"><i class="fa fa-edit"> Edit</i></a>
                </button>
            @endcan
            @can('delete-epaper', $package)
                <form action="{{route('epaper-package.resource.delete')}}" method="post" accept-charset="utf-8">
                    {{method_field('DELETE')}}
                    {{csrf_field()}}
                    <input type="hidden" name="{{\Modules\EPaper\Entities\EPaper::getPrimaryKey()}}" value="{{encrypt($package->getKey())}}">
                </form>
                <button class="dropdown-item confirm-delete" type="button"><a class="m-link m-link--state m-link--accent" data-toggle="modal" href="#small"><i class="fa fa-trash"> Delete</i></a></button>
            @endcan
        </div>
    </div>
</div>