@if(isset($categoryPermissions))
{{--	<h3><span id="permissionName"></span> <small>{{trans('admin.permissions')}}</small></h3>--}}
	<table id="showTable" class="table table-striped table-bordered dataTable no-footer hover">
		<thead>
		<tr>
			<th>
				{{trans('admin.permission')}}
			</th>
			<th>
				{{trans('admin.subpermissions')}}
			</th>
		</tr>
		</thead>
		@foreach($categoryPermissions as $category => $perms)
			<tr>
				<td>
					{{trans('admin.'.$category)}}
				</td>
				<td class="padding-0">
					<table  class="show-details" style="width:100%">
						@foreach($perms as $permission)
							<tr style="width:100%">
								<td>
									{{$permission}}
								</td>
							</tr>
						@endforeach
					</table>
				</td>
			</tr>
		@endforeach
	</table>
@else
	<div class="text-center" id="contents">
		<h2>{{trans('admin.permissions-view-title')}}</h2>
		<p>{{trans('admin.permissions-view-desc')}}</p>
	</div>
@endif
