
<div class="widget-chart-container bg-technology-reports">
	<div class="tab-content">
		<div id="" class="widget-chart padding-20">
			@if( isset($gateways) && !$gateways->isEmpty() )
				@if(count($gateways) > 1)

					{{--dropdown menu to be shown on smaller screens--}}
					@include('admin.widgets.gateways-dropdown')

					{{--normal chart in tabbed content panels for larger screens--}}
					<div class="nav-tabs-vertical latency-tabbed-charts">

						<ul class="larger-screens-only nav nav-tabs padding-0 widget-tab-menu" data-plugin="nav-tabs">
							@foreach($gateways as $index => $gateway)
								<li class="wanThroughputChart{{$index}} @if($index == 0) active @endif" >
									<a class="gateway-link" data-toggle="tab" href="#wanThroughputChart{{$index}}" data-item-index="wanThroughputChart{{$index}}" data-item-mac="{{$gateway->mac}}" >
										<p class="tabbed-info-heading gateway-name">{{$gateway->name}}</p>
										<p class="tabbed-info-small mac">{{trans('admin.mac')}}: {{$gateway->mac}}</p>
										<p class="tabbed-info-small">{{trans('admin.ip')}}: {{$gateway->ip}}</p>
									</a>
								</li>
							@endforeach
						</ul>
						<div class="tab-content">
							@include('admin.templates.system.loading', [
								'loadingStyle' => 'height:100%; width:100%',
								'spinnerStyle' => 'position: absolute; top: 200px; color: rgba(255,255,255, 0.75)',
								'loadingClass' => 'bg-technology-reports'
								])
							{{--loops through gateways and creates the chart containers--}}
							@foreach($gateways as $index => $gateway)
								<div id="wanThroughputChart{{$index}}" class="tab-pane chart-container @if($index == 0) active @endif"></div>
							@endforeach

						</div>
					</div>
				@else
					{{--single gateway content--}}
					<div class="tab-content">
						<div id="wanThroughputChart0" class="active chart-container"></div>
					</div>
				@endif
			@else
				@include('admin.widgets.no-gateways')
			@endif
		</div>
	</div>
</div>



