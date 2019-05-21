{{--
This blade is used all over the system to implement most client side datatable, where you pass the following variables in order for it to work properly
$rows = is a collection of all rows needed to display
$columns = is a an array of all columns needed to display
[$detailsColumns] = if you have detail column that will display several columns
--}}

	{{--Token needed for the ajax actions--}}
	{!! csrf_field() !!}

	<?php
		$permission = str_replace('/','.',$route ?? '');
		$route = $route ?? '';
    ?>

	@stack('before-datatable')

	<table id="{{$tableId ?? ''}}-table" class="table table-striped table-bordered dataTable no-footer hover">
		<thead>
		<tr>
			@foreach($columns as $column => $columnHelper)
				<th>
					{{trans('admin.' . $column)}}
				</th>
			@endforeach

			{{--Show Actions in handled in the Javascript of the datatable call--}}
			<th style="width: 60px;">{{trans('admin.actions')}}</th>

		</tr>
		</thead>
		@foreach($rows as $row)
			<tr>
				@foreach($columns as $column => $columnHelper)
					<td>
						@if(isset($clickableRow) && $clickableRow)
							<a href="/{{$indexRoute or $route}}/{{$row->id  }}">
						@endif

						{{--Checking if the column is in the detailsColumns list or not--}}
						@if(isset($detailsColumns) && in_array($column, array_keys($detailsColumns)))
							{{--Loops in the details column--}}
							@foreach($detailsColumns as $key => $details)
								@if($column == $key)
									{{-- If loop in the list of DB columns that you want to combine in one DT column--}}
									@foreach($details as $detailColumn => $helper)
										@if($row->$detailColumn ?? null != '')
											<div class="detail-column">
												{{--Show the DB column title --}}
												@if(!isset($hideTitleColumns) or (isset($hideTitleColumns) and !in_array($detailColumn, $hideTitleColumns)))
													<span class="detail-column-title">
														<strong>{{trans('admin.' . $detailColumn)}}</strong>:
													</span>
												@endif
												<span class="detail-column-description {{$detailColumn}}">
													{{--Show the DB column data, if no helper show as-is, if there is a helper render the data--}}
													@if($helper == '')
														{{$row->$detailColumn}}
													@else
														@if(isset($hardware))
															{{--if there is a helper render the data, $row->updated is being passed due to hardwareTypeColumn helper needs--}}
															{!! $helper::renderData($row->$detailColumn, $row->updated ?? null) !!}
														@elseif(isset($hhe))
															{!! $helper::renderData($row->$detailColumn, $row->last_seen ?? null) !!}
														@elseif(isset($gateways))
															{!! $helper::renderData($row->$detailColumn, $row->packetloss ?? null) !!}
														@else
															{!! $helper::renderData($row->$detailColumn) !!}
														@endif
													@endif
												</span>
											</div>
										@endif
									@endforeach
								@endif
							@endforeach
						@else
							{{--Else if it is a normal DB column--}}
							@if($columnHelper == '')
								{{--if no helper show as-is--}}
								{{$row->$column}}
							@else
								@if(isset($hardware))
								{{--if there is a helper render the data, $row->updated is being passed due to hardwareTypeColumn helper needs--}}
									{!! $columnHelper::renderData($row->$column, $row->updated ?? null) !!}
								@elseif(isset($hhe))
									{!! $columnHelper::renderData($row->$column, $row->last_seen ?? null) !!}
								@elseif(isset($gateways))
									{!! $columnHelper::renderData($row->$column, $row->packetloss ?? null) !!}
								@else
									{!! $columnHelper::renderData($row->$column) !!}
								@endif
							@endif
						@endif

						@if(isset($clickableRow) && $clickableRow)
							</a>
						@endif

					</td>
				@endforeach

				<td>
					<?php
					if(isset($row->status) && $row->status == 'active')
					{
						$activate 	= trans('admin.de-activate');
						$icon		= 'fa-toggle-on';
						$color		= 'text-success';
					}
					else
					{
						$activate 	= trans('admin.activate');
						$icon		= 'fa-toggle-off';
						$color		= 'text-danger';
					}
					if(empty($customActions)) {
						$customActions = ['activate', 'edit', 'delete'];
					}
					?>

					@if(in_array('activate', $customActions))
						@can('access' , $permission . '.activate')
							<a title="{{$activate}} '{{$row->name ?? $row->mac ?? trans('admin.record')}}'" class="action action_status" href="/{{$route}}/{{$row->id }}" data-status="{{$row->status }}" data-id="{{$row->id }}" data-name="{{$row->name ?? $row->mac ?? trans('admin.record')}}">
								<i class="fa {{$icon}} action {{$color}}"></i>
							</a>
						@endcan
					@endif

					@if(in_array('view', $customActions))
						<a title="{{trans('admin.view')}} '{{$row->name ?? $row->mac ?? trans('admin.record')}}'" class="action action_view" href="/{{$route}}/{{$row->id }}" data-id="{{$row->id }}" data-name="{{$row->name ?? $row->mac ?? trans('admin.record')}}">
							<i class="fa fa-eye action text-info"></i>
						</a>
					@endif

					@if(in_array('edit-guest', $customActions))
						@if (Gate::allows('access' ,'manage.guests.edit' ) )
							<a title="{{trans('admin.view')}} '{{$row->name ?? $row->mac ?? trans('admin.record')}}'" class="action action_edit" href="/manage/guests/{{$row->id }}/edit" data-id="{{$row->id }}" data-name="{{$row->name ?? $row->mac ?? trans('admin.record')}}">
								<i class="fa fa-eye action text-info"></i>
							</a>
						@endif
					@endif

					@if(in_array('sign-out-guest', $customActions))
						@if (Gate::allows('access' ,'online-now.sign-out-guest' ) )
							<a title="{{trans('admin.sign-out')}} '{{$row->name ?? $row->mac ?? trans('admin.record')}}'" class="action action_signout" href="/json/online-now/{{session('admin.site.loggedin')}}/sign-out-guest" data-session="{{$row->session_id }}" data-name="{{$row->name ?? $row->mac ?? trans('admin.record')}}">
								<i class="fa fa-sign-out action text-danger"></i>
							</a>
						@endif
					@endif

					<?php
						// exception for the system roles and permission page, not to show the edit button for default roles
						if(\Request::route()->getName() == 'system.roles-and-permissions.index' and is_null($row->site))
							$showEdit = false;
						else
							$showEdit = true;
					?>

					@if(in_array('edit', $customActions) and $showEdit)
						@if (Gate::allows('access' ,$permission . '.edit' ) or Gate::allows('access' ,$permission . '-edit' ) )
							<a title="{{trans('admin.edit')}} '{{$row->name ?? $row->mac ?? trans('admin.record')}}'" class="action action_edit" href="/{{$route}}/{{$row->id }}/edit">
								<i class="fa fa-pencil action text-info"></i>
							</a>
						@endif
					@endif


					@if(in_array('delete', $customActions))
						@can('access' , $permission . '.delete')
							@if((!isset($hardware) and !isset($hhe)) or (isset($hardware) and $row->updated < (Carbon\Carbon::now()->subHour())) or (isset($hhe) and $row->last_seen < (Carbon\Carbon::now()->subMinutes(15))))
								<a title="{{trans('admin.delete')}} '{{$row->name ?? $row->mac ?? trans('admin.record')}}'" class="action action_delete" href="/{{$route}}/{{$row->id }}" data-id="{{$row->id }}" data-name="{{$row->name ?? $row->mac ?? trans('admin.record')}}">
									<i class="fa fa-trash-o action text-danger"></i>
								</a>
							@endif
						@endcan
					@endif
				</td>
			</tr>
		@endforeach
	</table>


	@stack('after-datatable')
