@if(isset($showDetails))
	<table id="showTable" class="table table-striped table-bordered dataTable no-footer hover">
		<thead>
		<tr>
			<th>
				{{trans('admin.key')}}
			</th>
			<th>
				{{trans('admin.value')}}
			</th>
		</tr>
		</thead>
		@foreach($showDetails as $key => $values)
			<tr>
				<td>
					{{trans('admin.'.$key)}}
				</td>
				<td class="padding-0">
					<table class="show-details" style="width:100%">
						@if(!is_array($values))
							<tr style="width:100%">
								<td>
									{!! $values !!}
								</td>
							</tr>
						@else
							@foreach($values as $value)
								<tr style="width:100%">
									<td>
										{!! $value !!}
									</td>
								</tr>
							@endforeach
						@endif
					</table>
				</td>
			</tr>
		@endforeach
	</table>
@else
	<div class="text-center" id="contents">
		<h2>{{trans('admin.view-details-title')}}</h2>
		<p>{{trans('admin.view-details-desc')}}</p>
	</div>
@endif
