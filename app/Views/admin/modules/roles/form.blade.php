@extends('admin.templates.system.form')
@section('form-contents')
	<div class="row">
		<div class="col-xs-12">
			@include('admin.modules.roles.form.tabs')
		</div>
	</div>
@endsection