<h5 class="margin-bottom-10">
	{{trans('admin.site-terms-and-conditions')}}
</h5>

<table class="table-striped table-bordered table-hover table" id="terms">
	<thead>
	<tr>
		<th class="" style="width:80%">{{trans('admin.language')}}</th>
		<th class="" style="width:20%">{{trans('admin.actions')}}</th>
	</tr>
	</thead>
	<tbody>
		@foreach( $siteLanguageTerms as $language => $terms )
			<tr>
				<td>{{ trans('admin.'.$language) }}</td>
				<td>
					{{-- If we have no terms then warn it needs an override --}}
					@if( is_null($terms) )
						<i class="fa fa-warning text-warning" title="{{trans('admin.no-terms')}}" ></i>
					@else
						<i class="fa fa-check-square-o text-success"  title="{{trans('admin.got-terms')}}"></i>
					@endif

					{{-- Allow edit if we can, view if we can't edit --}}
					@can('access', 'manage.brand.site-terms-edit')
						<a title="{{trans('admin.edit')}}" class="action action_edit" href="/manage/brand/site-terms/{{ Session::get('admin.site.loggedin') ?? 0 }}/{{ $language }}/edit/">
							<i class="fa fa-pencil action text-info"></i>
						</a>
					@else
						<a title="{{trans('admin.edit')}}" class="action action_view" href="/manage/brand/site-terms/{{ Session::get('admin.site.loggedin') ?? 0 }}/{{ $language }}">
							<i class="fa fa-eye action text-info"></i>
						</a>
					@endcan

					@if(!is_null($terms))
						@can('access', 'manage.brand.site-terms-edit')
							<a title="{{trans('admin.reset')}}" class="action action_reset" href="{{ route('manage.brand.site.terms.edit') }}" data-id="{{$terms['id']}}" data-name="{{trans('admin.terms-and-conditions')}}" data-portal="{{ trans('admin.'.$language) }}">
								<i class="fa fa-trash-o action text-danger"></i>
							</a>
						@endcan
					@endif
				</td>
			</tr>
		@endforeach
	</tbody>
</table>

