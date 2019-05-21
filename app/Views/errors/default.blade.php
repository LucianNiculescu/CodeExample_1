@extends('admin.templates.system.index')

@section('index-contents')
    <div>
        {!! $error_message !!}
        <br>
        @if(!empty($prev))
            {!! trans('admin.errors|last-page', ['url' => $prev, 'home'=> url('estate')]) !!}
        @else
            {!! trans('admin.errors|home-page', ['home'=> url('estate')]) !!}
        @endif
    </div>
@endsection
