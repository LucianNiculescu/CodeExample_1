
	{!! csrf_field() !!}
	{!! $packagesDatatable->render() !!}

	@include('admin.templates.system.loading-datatable')



@push('footer-js')
{!! $packagesDatatable->script() !!}
@endpush