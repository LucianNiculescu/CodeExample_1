@extends('admin.templates.system.index')

@section('index-contents')
	<div class="row">
		<div class="col-xs-12 col-md-7 margin-bottom-30 ">
			@include('admin.templates.system.client-side-datatable')
		</div>
		<div class="col-xs-12 col-md-5 role-details">
			<div class="contents">
				@include('admin.modules.roles.show')
			</div>
		</div>
	</div>
@endsection

@push('footer-js')
	<script>
		$(document).ready(function() {
			/*Draw the datatable*/
			$('#{{$tableId}}-table').dataTable({
				"order"     : [0, "asc"],
				"aoColumns"	: [
					null,
					null,
					null,
					{"bVisible" : {{$showActions ? "true" : "false"}}, "bSortable" : false }
				]
			});
		});

	</script>
@endpush