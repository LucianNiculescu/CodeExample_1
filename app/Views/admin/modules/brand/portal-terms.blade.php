
<h5 class="margin-bottom-10">
	{{trans('admin.portal-terms-and-conditions')}}
</h5>
<table class="table-striped table-bordered table-hover table" id="terms">
	<thead>
	<tr>
		<th class="" style="width:70%">{{trans('admin.portal')}}</th>
		<th class="" style="width:10%">{{trans('admin.language')}}</th>
		<th class="" style="width:20%">{{trans('admin.actions')}}</th>
	</tr>
	</thead>
	<tbody>
		@foreach($portals as $key=>$portal)
			<?php
				$portalLanguage = (isset($portal['attributes'][0]))? $portal['attributes'][0]['value'] : 'en';
				if(!in_array($portalLanguage, \App\Helpers\Language::getLanguages()))
					$portalLanguage = 'en';
			?>
			<tr>
				<td>
					{{$portal['name']}}
				</td>
				<td>
					{{trans('admin.'.$portalLanguage)}}
				</td>
				<td>
					<?php $found=false; ?>
					@foreach($terms as $term)
						@if($term['portal']['id'] == $key and $portalLanguage == substr($term['language'],0,2))
							<i class="fa fa-check-square-o text-success"  title="{{trans('admin.got-terms')}}"></i>
							<?php $found=true; ?>
							@break
						@endif
					@endforeach

					@if(!$found)
						<i class="fa fa-warning text-warning" title="{{trans('admin.no-terms')}}" ></i>
					@endif

					@can('access', 'manage.brand.terms-edit')
						<a title="{{trans('admin.edit')}}" class="action action_edit" href="/manage/brand/terms/{{$key}}/edit/">
							<i class="fa fa-pencil action text-info"></i>
						</a>
					@else
						<a title="{{trans('admin.edit')}}" class="action action_view" href="/manage/brand/terms/{{$key}}">
							<i class="fa fa-eye action text-info"></i>
						</a>
					@endcan

					@if($found)
						@can('access', 'manage.brand.terms-edit')
							<a title="{{trans('admin.reset')}}" class="action action_reset" href="/manage/brand/terms/edit" data-id="{{$term['id']}}" data-name="{{trans('admin.terms-and-conditions')}}" data-portal="{{$portal['name']}}">
								<i class="fa fa-trash-o action text-danger"></i>
							</a>
						@endcan
					@endif
				</td>

			</tr>
		@endforeach
	</tbody>
</table>

