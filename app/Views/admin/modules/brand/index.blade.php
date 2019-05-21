@extends('admin.templates.system.index')

@section('index-contents')

@if(Gate::denies('access' ,'manage.brand.look-and-feel' ) and
	Gate::denies('access' ,'manage.brand.emails' ) and
	Gate::denies('access' ,'manage.brand.terms' ) and
	Gate::denies('access' ,'manage.brand.site-terms' ) and
	Gate::denies('access' ,'manage.brand.vouchers' ) )

	{!! trans('error.no-brand-permissions-found') !!}
@else
	<div class="row">

		@can('access', 'manage.brand.look-and-feel')
			<div class="col-xs-12 col-sm-12 col-md-6 brand-div">
				<div class="clearfix col-inner-border" >
					@include('admin.modules.brand.look-and-feel')
				</div>
			</div>
		@endcan

		@can('access', 'manage.brand.emails')
			<div class="col-xs-12 col-sm-12 col-md-6 brand-div">
				<div class="clearfix col-inner-border">
					@include('admin.modules.brand.emails')
				</div>
			</div>
		@endcan


		{{--Checking if there is terms or site-terms permission then it will show the header of the section--}}
		@if(\Gate::allows('access', 'manage.brand.terms') or \Gate::allows('access', 'manage.brand.site-terms') )
			<div class="col-xs-12 col-sm-12 col-md-6 brand-div">
				<div class="clearfix col-inner-border">
					<h3 class="margin-0">
						{{trans('admin.terms-and-conditions')}}
						<small>
							@include('admin.templates.system.input-fields.help', ['help' => trans('help.brand|fields|terms')])
						</small>
					</h3>
		@endif

		@can('access', 'manage.brand.terms')
			@include('admin.modules.brand.portal-terms')
		@endcan

		@can('access', 'manage.brand.site-terms')
			@include('admin.modules.brand.site-terms')
		@endcan

	{{--Checking if there is terms or site-terms permission then it will close the ts&cs section--}}
		@if(\Gate::allows('access', 'manage.brand.terms') or \Gate::allows('access', 'manage.brand.site-terms') )
				</div>
			</div>
		@endif

		@can('access', 'manage.brand.vouchers')
			<div class="col-xs-12 col-sm-12 col-md-6 brand-div">
				<div class="clearfix col-inner-border">
					@include('admin.modules.brand.vouchers')
				</div>
			</div>
		@endcan

	</div>

@endif

@endsection
