<div class="search-results">

	@forelse ($result as $item)
		<?php
		$id 	= $item['_source']['id'] ?? '' ;
		$mac 	= $item['_source']['mac'] ?? '' ;
		$ip 	= $item['_source']['ip'] ?? '' ;
		$user 	= $item['_source']['user'] ?? '' ;
		$name 	= $item['_source']['name'] ?? '' ;
		$type	= $item['_source']['type'] ?? '' ;
		$created= $item['_source']['created'] ?? '' ;
		$updated= $item['_source']['updated'] ?? '' ;
		$status = $item['_source']['status'] ?? '' ;

		$name 	= ($name == '')? trans('admin.no-name') : $name;
		$status = trans('admin.'.$status);
		$created = \App\Admin\Helpers\Datatables\DateColumn::renderData($created);
		$updated = \App\Admin\Helpers\Datatables\DateColumn::renderData($updated);
		?>

		@if($item['_index'] == 'user')
			<h3>
				{!! \App\Admin\Helpers\Datatables\IconColumn::renderData($type) !!} @if($editGuests)<a href="/manage/guests/{{$id}}/edit" class="highlightable">{{$user}}</a>@else<span class="highlightable">{{$user}}</span>@endif
			</h3>
			@if($editGuests)<div class="search-result-url">{{url('/')}}/manage/guests/{{$id}}/edit</div>@endif
			<p class="highlightable">
				{!! trans('admin.search-results-user', [
				'name'		=> $name,
				'mac'		=> $mac,
				'created'	=> $created,
				'updated'	=> $updated
				]) !!}
			</p>
		@elseif($item['_index'] == 'gateway')
			<h3>
				<i title="{{trans('admin.gateway')}}" class="fa fa-wifi"></i> @if($editGateways)<a href="/networking/gateways/{{$id}}/edit"  class="highlightable">{{$name}}</a>@else<span class="highlightable">{{$name}}</span>@endif
			</h3>
			@if($editGateways)<div class="search-result-url">{{url('/')}}/networking/gateways/{{$id}}/edit</div>@endif
			<p class="highlightable">
				{!! trans('admin.search-results-gateway', [
					'mac'		=> $mac,
					'ip'		=> $ip,
					'status'	=> $status,
					'created'	=> $created,
					'updated'	=> $updated
				]) !!}
			</p>
		@elseif($item['_index'] == 'hardware')
			<h3>
				<i title="{{trans('admin.hardware')}}" class="fa fa-wifi"></i> @if($editHardware)<a href="/networking/hardware/{{$id}}/edit" class="highlightable">{{$name}}</a>@else<span class="highlightable">{{$name}}</span>@endif
			</h3>
			@if($editHardware)<div class="search-result-url">{{url('/')}}/networking/hardware/{{$id}}/edit</div>@endif
			<p class="highlightable">
				{!! trans('admin.search-results-hardware', [
					'mac'		=> $mac,
					'ip'		=> $ip,
					'status'	=> $status,
					'created'	=> $created,
					'updated'	=> $updated
				]) !!}
			</p>
		@endif
	@empty
		<p>{!! trans('admin.no-search-results') !!}</p>
	@endforelse

	@if($total > 10)
		<div class="search-buttons">
			<a href="/search" data-type="{{$searchType ?? 'all'}}" data-from="{{$from - 10}}" class="elastic-search previous @if($from == 0) disabled @endif">&lt; {{trans('admin.previous')}}</a>
			<span class="search-results-details">{{trans('admin.results-from-to', ['from' => $from + 1, 'to' => $from+10 > $total ? $total : $from+10])}}</span>
			<a href="/search" data-type="{{$searchType ?? 'all'}}" data-from="{{$from + 10}}" class="elastic-search next @if($from + 10 > $total) disabled @endif">{{trans('admin.next')}} &gt;</a>
		</div>
	@endif
</div>

@push('footer-js')
	<script>
		// Calling the highlight search function that will make the result found in bold
		$(function() {highlightSearch("{{$search}}");});
	</script>
@endpush

