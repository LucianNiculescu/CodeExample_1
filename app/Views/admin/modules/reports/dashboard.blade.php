@extends('admin.templates.system.master')

@section('content')
	@include('admin.widgets.index', ['widgets'=>$widgets, 'widgetSettings' => 'bar'])
@endsection


