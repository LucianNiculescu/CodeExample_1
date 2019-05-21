@extends('admin.templates.system.master')

@section('content')

	<div class="translations">
		<div >
			<h2 class="title">
				{{$title}}<small>{{$description}}</small>
			</h2>

			<div class="form_actions">
				@include('admin.help-pages.button')
			</div>

		</div>

	@if(\Gate::allows('access', 'translations.upload') or \Gate::allows('access', 'translations.download') )
		<form autocomplete="off" class="form-horizontal" method="post" action="/translations" enctype="multipart/form-data">
			<div class="container contents_frame">

				{!! csrf_field() !!}
				<div class="container translations">
					<div class="row">
						<div class="pull-left padding-left-0 col-lg-6 col-md-6 col-sm-12 col-xs-12">
							@can('access', 'translations.upload')
								<div class="input-group">
									<label class="input-group-btn">
										<span class="btn btn-info" title="{{trans('admin.browse')}}">
											<i class="fa fa-folder-open-o padding-right-5"></i> {{trans('admin.browse')}}...
											<input type="file" name='translation-csv'  style="display: none;" multiple="">
										</span>
									</label>
									<input type="text" class="form-control" name='translation-csv' value="{{old('translation-csv')}}"  readonly="" style="width:300px">
								</div>

								@if ($errors->has('translation-csv'))
									<span class="help-block">
										<strong>{{ $errors->first('translation-csv') }}</strong>
									</span>
								@endif
							@endcan
						</div>
						<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pull-right padding-right-0">
							<a href="/" type="button" class="btn btn-default pull-right margin-left-5" title="Cancel">
								<i class="fa fa-ban padding-right-5"></i>
								{{trans('admin.cancel')}}
							</a>
							@can('access', 'translations.download')
								<a href="/translations/download" type="button" class="btn btn-info pull-right margin-left-5" title="Download">
									<i class="fa fa-download padding-right-5"></i>
									{{trans('admin.download')}}
								</a>
							@endcan

							@can('access', 'translations.upload')
								<button type="submit" class="btn save_all btn-info pull-right margin-left-5" title="Upload">
									<i class="fa fa-upload padding-right-5"></i>
									{{trans('admin.upload')}}
								</button>
							@endcan
						</div>
					</div>
				</div>
			</div>
		</form>
	@endif

		{!! $translationsDatatable->render() !!}
</div>

@include('admin.templates.system.loading-datatable')
@endsection

@push('footer-js')
{!! $translationsDatatable->script() !!}
@endpush
