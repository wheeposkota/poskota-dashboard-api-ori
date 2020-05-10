<div class="col">
    @if($member->type == 'verified')
        <div class="btn-group">
            <a href="javascript:void(0)" class="btn btn-outline-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Actions
            </a>
            <div class="dropdown-menu dropdown-menu-left">
                @can('update-member', $member)
                    <button class="dropdown-item" type="button">
                        <a class="m-link m-link--state m-link--info" href="{{action('\Modules\Member\Http\Controllers\MemberController@create').'?code='.encrypt($member->id)}}"><i class="fa fa-edit"> Edit</i></a>
                    </button>
                @endcan
                @can('delete-member', $member)
                    <form action="{{action('\Modules\Member\Http\Controllers\MemberController@destroy')}}" method="post" accept-charset="utf-8">
                        {{method_field('DELETE')}}
                        {{csrf_field()}}
                        <input type="hidden" name="id" value="{{encrypt($member->id)}}">
                    </form>
                    <button class="dropdown-item confirm-delete" type="button"><a class="m-link m-link--state m-link--accent" data-toggle="modal" href="#small"><i class="fa fa-trash"> Delete</i></a></button>
                @endcan
            </div>
        </div>
    @else
        -
    @endif
</div>