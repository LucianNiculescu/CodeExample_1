{{--
This blade is used all over the system to implement most client side datatable, where you pass the following variables in order for it to work properly
$rows = is an array of all rows needed to display
$columns = is a an array of all columns needed to display
[$detailsColumns] = if you have detail column that will display several columns
--}}


{{--Token needed for the ajax actions--}}
	{!! csrf_field() !!}

	<table id="{{$tableId ?? ''}}-table" class="table table-striped table-bordered dataTable no-footer hover">
		<thead>
		<tr>
			@foreach($columns as $column => $columnHelper)
				<th>
					{{trans('admin.' . $column)}}
				</th>
			@endforeach

			<th style="width: 60px;">{{trans('admin.actions')}}</th>

		</tr>
		</thead>
		@if(!empty($rows) && (is_array($rows) || (is_object($rows) && $rows->count() > 0)))
			@foreach($rows as $row)
				<tr>
					@foreach($columns as $column => $columnHelper)
						<td>
							@if(isset($customColumns) && in_array($column, array_keys($customColumns)))
								@foreach($customColumns as $customColumn => $helperAndParameter)
									{!! $helperAndParameter[0]::renderData($row, $helperAndParameter[1]) !!}
								@endforeach
							{{--Checking if the column is in the detailsColumns list or not--}}
							@elseif(isset($detailsColumns) && in_array($column, array_keys($detailsColumns)))
								{{--Loops in the details column--}}
								@foreach($detailsColumns as $key => $details)
									@if($column == $key)
										<?php $dataFound = false;?>
										{{-- If loop in the list of DB columns that you want to combine in one DT column--}}
										@foreach($details as $detailColumn => $helper)
											@if($row[$detailColumn] ?? null != '')
												<?php $dataFound = true;?>
												<div class="detail-column">
													{{--Show the DB column title --}}
													@if(!isset($hideTitleColumns) or (isset($hideTitleColumns) and !in_array($detailColumn, $hideTitleColumns)))
														<span class="detail-column-title">
															<strong>{{trans('admin.' . $detailColumn)}}</strong>:
														</span>
													@endif
													<span class="detail-column-description">
														{{--Show the DB column data, if no helper show as-is, if there is a helper render the data--}}
														@if($helper == '')
															{{$row[$detailColumn] ?? ''}}
														@else
															{!! $helper::renderData($row[$detailColumn] ?? '') !!}
														@endif
													</span>
												</div>
											@endif
										@endforeach

										@if(!$dataFound)
											{{trans('admin.n-a')}}
										@endif
									@endif
								@endforeach
							@else
								{{--Else if it is a normal DB column--}}
								@if($columnHelper == '')
									{{--if no helper show as-is--}}
									{{$row[$column] ?? ''}}
								@else
									{!! $columnHelper::renderData($row[$column] ?? '') !!}
								@endif
							@endif

						</td>
					@endforeach

					<td>
						@if(isset($actionsArray))
							@foreach($actionsArray as $actionName=>$action)
								{!! \App\Admin\Helpers\Datatables\ActionsColumn::renderData($actionName, $action, $row, $mac) !!}
							@endforeach
						@endif
					</td>
				</tr>
			@endforeach
		@endif
	</table>
