<h3 class="margin-0 margin-bottom-10">
	{{trans('admin.vouchers')}}
	<small>
		@include('admin.templates.system.input-fields.help', ['help' => trans('help.brand|fields|vouchers')])
	</small>
</h3>

<table class="table-striped table-bordered table-hover table" id="emailTemplates">
	<thead>
	<tr>
		<th class="" style="width:70%">{{trans('admin.voucher-type')}}</th>
		<th class="" style="width:10%">{{trans('admin.language')}}</th>
		<th class="" style="width:20%">{{trans('admin.actions')}}</th>
	</tr>
	</thead>
	<tbody>
		<?php $languages = \App\Models\AirConnect\Translation::getLanguages(); ?>

		@foreach($languages as $langKey=>$language)
			@foreach(['single_voucher', 'multiple_voucher'] as $voucherType)
				<tr>
					<td>
						{{trans('admin.'.$voucherType)}}
					</td>
					<td>
						{{trans('admin.'.$langKey)}}
					</td>

					<td>
						<?php $found=false; ?>
						@foreach($vouchers as $voucher)
							@if($langKey == $voucher['language'] and $voucher['name'] == $voucherType)
								<i class="fa fa-check-square-o text-success"  title="{{trans('admin.got-voucher')}}"></i>
								<?php $found=true; ?>
								@break;
							@endif
						@endforeach


							@if(!$found)
								<i class="fa fa-warning text-warning" title="{{trans('admin.no-voucher')}}" ></i>
							@endif

							@can('access', 'manage.brand.vouchers-edit')
								<a title="{{trans('admin.edit')}}" class="action action_edit" href="/manage/brand/vouchers/{{ Session::get('admin.site.loggedin') ?? 0 }}/{{$langKey}}/{{$voucherType}}/edit/">
									<i class="fa fa-pencil action text-info"></i>
								</a>
							@else
								<a title="{{trans('admin.edit')}}" class="action action_view" href="/manage/brand/vouchers/{{ Session::get('admin.site.loggedin') ?? 0 }}/{{$langKey}}/{{$voucherType}}">
									<i class="fa fa-eye action text-info"></i>
								</a>
							@endcan
						@if($found)
							@can('access', 'manage.brand.vouchers-edit')
								<a title="{{trans('admin.reset')}}" class="action action_reset" href="{{ route('manage.brand.vouchers.edit') }}" data-id="{{$voucher['id']}}" data-name="{{trans('admin.'.$langKey)}}" data-portal="{{trans('admin.voucher')}}">
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
