<h3 class="margin-0 margin-bottom-10">
	{{trans('admin.email-templates')}}
	<small>
		@include('admin.templates.system.input-fields.help', ['help' => trans('help.brand|fields|email-templates')])
	</small>
</h3>

<table class="table-striped table-bordered table-hover table" id="emailTemplates">
	<thead>
	<tr>
		<th class="" style="width:40%">{{trans('admin.portal')}}</th>
		<th class="" style="width:30%">{{trans('admin.email-template')}}</th>
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

			@foreach(['email_welcome', 'email_receipt'] as $emailTemplate)
				<tr>
					<td>
						{{$portal['name']}}
					</td>
					<td>
						{{trans('admin.'.$emailTemplate)}}
					</td>
					<td>
						{{trans('admin.'.$portalLanguage)}}
					</td>

					<td>
						<?php $found=false; ?>
						@foreach($contents as $content)
							@if($content['portal']['id'] == $key and $content['name'] == $emailTemplate and $portalLanguage == substr($content['language'],0,2))
								<i class="fa fa-check-square-o text-success"  title="{{trans('admin.got-email-template')}}"></i>
								<?php $found=true; ?>
								@break;
							@endif
						@endforeach

						@if(!$found)
							<i class="fa fa-warning text-warning" title="{{trans('admin.no-email-template')}}" ></i>
						@endif

						@can('access', 'manage.brand.emails-edit')
							<a title="{{trans('admin.edit')}}" class="action action_edit" href="/manage/brand/emails/{{$key}}/{{$emailTemplate}}/edit/">
								<i class="fa fa-pencil action text-info"></i>
							</a>
						@else
							<a title="{{trans('admin.edit')}}" class="action action_view" href="/manage/brand/emails/{{$key}}/{{$emailTemplate}}">
								<i class="fa fa-eye action text-info"></i>
							</a>
						@endcan

						@if($found)
							@can('access', 'manage.brand.emails-edit')
								<a title="{{trans('admin.reset')}}" class="action action_reset" href="{{ route('manage.brand.emails.edit') }}" data-id="{{$content['id']}}" data-name="{{trans('admin.'.$emailTemplate)}}" data-portal="{{$portal['name']}}">
									<i class="fa fa-trash-o action text-danger"></i>
								</a>
							@endcan
						@endif
					</td>

				</tr>
			@endforeach
		@endforeach
	</tbody>
</table>
