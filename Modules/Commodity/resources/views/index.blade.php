@extends('commodity::layouts.master')

@section('content')
    <h1>Hello World</h1>

    <p>
        This view is loaded from module: {!! config('commodity.name') !!}
    </p>
@stop
