{{--dropdown menu to be shown Gateways--}}
<div class="@if(isset($widget)) smaller-screens-only @endif widget-submenu-container" style=" {{$customStyle or ''}}">
	<div class="gateway-menu">
		<div class="btn-group">
			<a class="btn dropdown-toggle dropdown-display widget-dropdown-display" data-toggle="dropdown" href="#" style="color:#333;">
				@foreach($gateways as $index => $gateway)
					@if($index == 0)
						<i class="gateway-icon fa fa-wifi"></i>
						<span class="gateway-name">{{$gateway->name}}</span>
					@endif
				@endforeach
				<span class="caret select-gateway"></span>
			</a>
			<ul class="dropdown-menu widget-sub-menu">
				@foreach($gateways as $index => $gateway)
					<li class="widget-sub-item @if($index == 0)active @endif">
						<a href="" data-toggle="tab" class="mac" data-item-mac="{{$gateway->mac}}">
							{{$gateway->name}}
						</a>
					</li>
				@endforeach
			</ul>
		</div>

	</div>
</div>