{{--Table of permission and on/off--}}
<div class="permissions-table @if($category == 'widgets') widgets @endif" >

	<div class="row permissions-header">
		<div class="col @if($category == 'widgets') col-xs-3 @else col-xs-6 @endif">
			@if($category == 'role-manage')
				{{trans('admin.role-to-manage')}}
			@elseif($category == 'widgets')
				{{trans('admin.widget')}}
			@else
				{{trans('admin.permission')}}
			@endif
		</div>
		@if($category == 'widgets')
			<div class="col col-xs-2">
				{{trans('admin.type')}}
			</div>
			<div class="col col-xs-2">
				{{trans('admin.routes')}}
			</div>
			<div class="col col-xs-5">
				{{trans('admin.description')}}
			</div>
		@else
			<div class="col col-xs-6">
				{{trans('admin.allowed-disallowed')}}
			</div>
		@endif
	</div>

	<div class="@if($category == 'widgets') sortable @endif">

			{{--Category switch--}}
		@if(in_array($category,$userPermissions))
			<div class="row">
				<div class="col col-xs-6 permission_name ">
					<h5 for="permission[{{$currentRole}}][{{ $category }}]">
						{{trans('admin.view')}}
					</h5>
				</div>
				<div class="col col-xs-6 ">
					{{--Category is a permission so Checking if the category is on or off--}}
					{{--Permission is on--}}
					<input type="hidden" name="permission[{{$currentRole}}][{{$category}}]" value="0">
					<input type="checkbox" value="1"
						   name="permission[{{$currentRole}}][{{$category}}]"
						   data-category="{{$category}}"
						   class="js-switch-small category"
						   data-plugin="switchery"
						   data-size="small"
						   @if(in_array($category, $currentRolePermissions) and !is_null($currentRole))
								checked
						   @endif
						   data-switchery="true"
						   style="display: none;">
				</div>
			</div>
		@endif

		@foreach($categoryWithPermissions as $userCat => $userPerms)
			{{--$userPermissions is an array of permissions--}}
			@foreach($userPerms as $userPerm)
				{{-- Don't Show role-manage self--}}
				@if($userPerm != $currentRole)

					{{-- To show the permissions under this category--}}
					@if($userCat == $category and $userPerm != '')
						<?php
							// temp variable to use when putting a checkbox or not
							$permissionFoundAndIsOn = false;
							// Calculating the actual permission as in the DB
							$permission = $userCat . '.' . $userPerm
						?>

						<div class="row @if($category == 'role-manage') role-manage searchable @elseif($category == 'widgets') widgets searchable sortable-tr @endif">
							@foreach(explode('.',$userPerm ) as $subPermission)
								<div class="col @if($category == 'widgets') col-xs-3 @else col-xs-6 @endif permission_name permission_{{$currentRole}}_{{$userPerm}}">
									<div class="widget-permission">
										<h5 for="permission[{{$currentRole}}][{{ $permission }}]" title="{{$subPermission}}">
											{{--Special condition to show role managed title without translation--}}
											@if(is_numeric($subPermission))
												{{$roles[$subPermission]['role']}}  @if(isset($roles[$subPermission]['site']['name'])) <small>{{$roles[$subPermission]['site']['name']}}</small> @endif
												{{--Will show the role to manage if not found then will show the role number, which means an issue with the $roles array--}}
											@else
												@if($category == 'widgets')
													<i class="fa fa-sort margin-right-10" style="cursor:move;"></i>
												@endif
												{{trans('admin.'.$subPermission)}} {{--Will show the translation of the permission--}}
											@endif
										</h5>
									</div>
								</div>
							@endforeach

							@if($category == 'widgets')
									<?php
									if(in_array(substr($permission,8), $activeWidgets))
										$widgetPermission = 2;
									elseif(in_array(substr($permission,8), $inactiveWidgets))
										$widgetPermission = 1;
									else
										$widgetPermission = 0;
									?>
									<input type="hidden" name="permission[{{$currentRole}}][{{$permission}}]" value="{{$widgetPermission}}"/>

								<div class="col col-xs-2 ">
									{!! \App\Admin\Helpers\Datatables\ExplodeToLabelsColumn::renderData($allWidgets[$subPermission]['type']) !!}
								</div>
								<div class="col col-xs-2 ">
									{!! \App\Admin\Helpers\Datatables\ExplodeToLabelsColumn::renderData($allWidgets[$subPermission]['routes']) !!}
								</div>
								<div class="col col-xs-5 ">
									<p>{{trans('admin.'.$allWidgets[$subPermission]['description']) }}</p>
								</div>
							@else
								{{--Checking current rolePermissions to see if it is checked or not. Showing only the right permission--}}
								@if (in_array($permission, $currentRolePermissions) and !is_null($currentRole))
									<div class="col col-xs-6 ">
										<input type="hidden" name="permission[{{$currentRole}}][{{$permission}}]" value="0">
										<input type="checkbox" value="1"
											data-permission="{{$permission}}"
											name="permission[{{$currentRole}}][{{$permission}}]"
											id="permission[{{$currentRole}}][{{$permission}}]"
											class="js-switch-small permission"
											data-plugin="switchery"
											data-size="small"
											checked data-switchery="true"
											style="display: none;">
									</div>
									{{--Permission is found and checked--}}
									<?php $permissionFoundAndIsOn = true; ?>
								@endif

								{{--for this specific permission if it is not checked then an un-checked checkbox will be drawn--}}
								@if(!$permissionFoundAndIsOn)
									<div class="col col-xs-6 ">
										<input type="hidden" name="permission[{{$currentRole}}][{{$permission}}]" value="0">
										<input type="checkbox" value="1"
											data-permission="{{$permission}}"
											name="permission[{{$currentRole}}][{{$permission}}]"
											id="permission[{{$currentRole}}][{{$permission}}]"
											class="js-switch-small permission"
											data-plugin="switchery"
											data-size="small"
											data-switchery="true"
											style="display: none;">
									</div>
								@endif
							@endif
						</div>
					@endif
				@endif
			@endforeach
		@endforeach
	</div>
</div>