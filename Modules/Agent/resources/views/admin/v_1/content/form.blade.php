@extends('core::admin.'.$theme_cms->value.'.templates.parent')

@section('title_dashboard', 'Agent')

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
                    <span class="m-nav__link-text">Agent</span>
                </a>
            </li>
        </ul>
@endsection

@section('content')

<div class="row">
    <div class="col-sm-12">

        <!--begin::Portlet-->
        <div class="m-portlet m-portlet--tab">
            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <div class="m-portlet__head-title">
                        <span class="m-portlet__head-icon m--hide">
                            <i class="fa fa-gear"></i>
                        </span>
                        <h3 class="m-portlet__head-text">
                            Agent Form
                        </h3>
                    </div>
                </div>
            </div>

            <!--begin::Form-->
            <form class="m-form m-form--fit m-form--label-align-right" action="{{action('\Modules\Agent\Http\Controllers\AgentController@store')}}" method="post">
                <div class="m-portlet__body">
                    <div class="col-md-5 offset-md-4">
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
                    <div class="form-group m-form__group d-md-flex">
                        <div class="col-md-4 d-md-flex justify-content-end py-3">
                            <label for="exampleInputEmail1">Agent Name<span class="ml-1 m--font-danger" aria-required="true">*</span></label>
                        </div>
                        <div class="col-md-8">
                            <input type="text" class="form-control m-input" placeholder="Agent Name" name="mst_ptlagen_name" value="{{old('mst_ptlagen_name') ? old('mst_ptlagen_name') : (!empty($agent) ? $agent->mst_ptlagen_name : '')}}">
                        </div>
                    </div>
                    <div class="form-group m-form__group d-md-flex">
                        <div class="col-md-4 d-md-flex justify-content-end py-3">
                            <label for="exampleInputEmail1">Agent Address<span class="ml-1 m--font-danger" aria-required="true">*</span></label>
                        </div>
                        <div class="col-md-8">
                            <textarea type="text" class="form-control m-input" placeholder="Agent Address" name="mst_ptlagen_address">{{old('mst_ptlagen_address') ? old('mst_ptlagen_address') : (!empty($agent) ? $agent->mst_ptlagen_address : '')}}</textarea>
                        </div>
                    </div>
                    <div class="form-group m-form__group d-md-flex">
                        <div class="col-md-4 d-md-flex justify-content-end py-3">
                            <label for="exampleInputEmail1">Agent Regional<span class="ml-1 m--font-danger" aria-required="true">*</span></label>
                        </div>
                        <div class="col-md-8">
                            <input type="text" class="form-control m-input" placeholder="Agent Regional" name="mst_ptlagen_region" value="{{old('mst_ptlagen_region') ? old('mst_ptlagen_region') : (!empty($agent) ? $agent->mst_ptlagen_region : '')}}">
                        </div>
                    </div>
                    <div class="form-group m-form__group d-md-flex">
                        <div class="col-md-4 d-md-flex justify-content-end py-3">
                            <label for="exampleInputEmail1">Member/Staff</label>
                        </div>
                        <div class="col-md-8">
                            <select class="form-control select2" name="user_id[]" multiple placeholder="Select Member Of This Agent">
                                @foreach ($users as $user)
                                    @if(!empty($agent))
                                        <option value="{{encrypt($user->getKey())}}" {{!empty($agent) && $agent->users->where('id', $user->getKey())->count() > 0 ? 'selected' : ''}} {{$user->agent->whereNotIn(\Modules\Agent\Entities\MstAgens::getPrimaryKey(), [$agent->getKey()])->count() > 0 ? 'disabled' : ''}}>{{$user->name}}{{$user->agent->whereNotIn(\Modules\Agent\Entities\MstAgens::getPrimaryKey(), [$agent->getKey()])->count() > 0 ? ' -- Terdaftar di '.$user->agent->first()->mst_ptlagen_name : ''}}</option>
                                    @else
                                        <option value="{{encrypt($user->getKey())}}" {{$user->agent->count() > 0 ? 'disabled' : ''}}>{{$user->name}}{{$user->agent->count() > 0 ? ' -- Terdafatar di '.$user->agent->first()->mst_ptlagen_name : ''}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                {{csrf_field()}}
                @if(isset($_GET['code']))
                    <input type="hidden" name="id" value="{{$_GET['code']}}">
                @endif
                {{$method}}
                <div class="m-portlet__foot m-portlet__foot--fit">
                    <div class="m-form__actions">
                        <div class="offset-md-4">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </div>
            </form>

            <!--end::Form-->
        </div>

        <!--end::Portlet-->

    </div>
</div>
{{-- End of Row --}}

@endsection