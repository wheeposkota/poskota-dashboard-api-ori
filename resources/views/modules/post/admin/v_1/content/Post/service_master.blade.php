<div class="col">
    <div class="btn-group">
        <a href="javascript:void(0)" class="btn btn-outline-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Actions
        </a>
        <div class="dropdown-menu dropdown-menu-left">
            <button class="dropdown-item" type="button">
                @if($post->post_status == 'draft' || \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $post->publish_date)->gt(\Carbon\Carbon::now()))
                    <a class="m-link m-link--state m-link--warning" href="{{url(config('app.frontend_url').'/'.$post->link.'?token='.Auth::user()->remember_token)}}" target="_blank"><i class="fa fa-eye"> Preview</i></a>
                @else
                    <a class="m-link m-link--state m-link--warning" href="{{url(config('app.frontend_url').'/'.$post->link)}}" target="_blank"><i class="fa fa-eye"> Preview</i></a>
                @endif
            </button>
            @can('update-post', $post)
                <button class="dropdown-item" type="button">
                    <a class="m-link m-link--state m-link--info" href="{{route('cms.post-data.create').'?code='.encrypt($post->getKey())}}"><i class="fa fa-edit"> Edit</i></a>
                </button>
            @endcan
            @can('delete-post', $post)
                <form action="{{route('cms.post-data.delete')}}" method="post" accept-charset="utf-8">
                    {{method_field('DELETE')}}
                    {{csrf_field()}}
                    <input type="hidden" name="{{\Gdevilbat\SpardaCMS\Modules\Post\Entities\Post::getPrimaryKey()}}" value="{{encrypt($post->getKey())}}">
                </form>
                <button class="dropdown-item confirm-delete" type="button"><a class="m-link m-link--state m-link--accent" data-toggle="modal" href="#small"><i class="fa fa-trash"> Delete</i></a></button>
            @endcan
        </div>
    </div>
</div>