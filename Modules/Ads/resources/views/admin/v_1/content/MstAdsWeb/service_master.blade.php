<div class="col">
    <div class="btn-group">
        <a href="javascript:void(0)" class="btn btn-outline-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Actions
        </a>
        <div class="dropdown-menu dropdown-menu-left">
            @can('update-master-ads', $mst_ad_web)
                <button class="dropdown-item" type="button">
                    <a class="m-link m-link--state m-link--info" href="{{action('\Modules\Ads\Http\Controllers\MstAdsWebController@create').'?code='.encrypt($mst_ad_web->getKey())}}"><i class="fa fa-edit"> Edit</i></a>
                </button>
            @endcan
            @can('delete-master-ads', $mst_ad_web)
                <form action="{{action('\Modules\Ads\Http\Controllers\MstAdsWebController@destroy')}}" method="post" accept-charset="utf-8">
                    {{method_field('DELETE')}}
                    {{csrf_field()}}
                    <input type="hidden" name="{{\Modules\Ads\Entities\MstAdsPosition::getPrimaryKey()}}" value="{{encrypt($mst_ad_web->getKey())}}">
                </form>
                <button class="dropdown-item confirm-delete" type="button"><a class="m-link m-link--state m-link--accent" data-toggle="modal" href="#small"><i class="fa fa-trash"> Delete</i></a></button>
            @endcan
        </div>
    </div>
</div>