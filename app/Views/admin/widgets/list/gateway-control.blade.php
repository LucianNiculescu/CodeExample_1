<div id="gatewayControlWidget" class="panel-body">
@include('admin.templates.system.loading', ['loadingStyle' => 'position: absolute; top: 90px;'])
	@if( isset($gateways) && !$gateways->isEmpty() )
		<table id="gatewayControlTable" class="table table-striped table-bordered dataTable no-footer hover">
			<thead>
			<tr>
				<th style="width: 20%">
					MAC
				</th>
				<th>
					{{trans('admin.name')}}
				</th>

				{{--Show Actions in handled in the Javascript of the datatable call--}}
				<th  style="width: 20%">{{trans('admin.actions')}}</th>

			</tr>
			</thead>
			@foreach($gateways as $gateway)
				<tr>
					<td>
						{{$gateway->mac}}
					</td>
					<td>
						{{$gateway->name}}
					</td>
					<td>
						<a title="{{trans('admin.connecting-gateway')}}" class="action action_reboot" href="/json/widgets/gateway-control/0/reboot-gateway" data-mac="{{$gateway->mac}}" data-name="{{$gateway->name}}">
							<i class="fa  gateway-reboot"></i>
						</a>
						<a title="{{trans('admin.connecting-gateway')}}" class="action action_aaa" href="/json/widgets/gateway-control/0/aaa-gateway" data-mac="{{$gateway->mac}}" data-name="{{$gateway->name}}">
							<i class="fa gateway-aaa"></i>
						</a>

					</td>
				</tr>
			@endforeach
		</table>
	@else
		@include('admin.widgets.no-gateways')
	@endif
</div>

