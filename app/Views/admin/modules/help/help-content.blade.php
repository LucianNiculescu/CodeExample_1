<div class="col-xs-8 padding-0">
	<div class="tab-content">

		@if(is_array($helpArray))
			{{-- Loop through the array and create the main tabs --}}
			@foreach( $helpArray as $index => $content )

				<div class="tab-pane scrollable" id="{{ $index }}Main" role="tabpanel">

					<div class="col-lg-12">
						@if(!empty($content['index']))
							<p style="margin-top:20px">{!! $content['index'] !!}</p>
						@else
							<h2>{{ trans('admin.'.$index) }}</h2>
						@endif

						<div class="panel-group @if(count($content) < 2) no-border @endif" id="{{ $index }}Accordion" aria-multiselectable="true" role="tablist">

							@if(is_array($content))
								{{-- Create the acordian for this content --}}
								@foreach( $content as $key => $value )
									@if($key !== 'index')
										<div class="panel">
											<div class="panel-heading" id="{{ $index }}{{$key}}Heading" role="tab">
												<a class="panel-title collapsed" data-toggle="collapse" href="#{{ $index }}{{$key}}Collapse"
												   data-parent="#{{ $index }}Accordion" aria-expanded="false"
												   aria-controls="{{ $index }}{{$key}}Collapse">
													{{ trans( 'admin.' .$key .'') }}
												</a>
											</div>
											<div class="panel-collapse collapse" id="{{ $index }}{{$key}}Collapse" aria-labelledby="{{ $index }}{{$key}}Heading"
												 role="tabpanel">
												<div class="panel-body">
													@if(!is_array( $value ))
														{!! $value !!}
													@else
														{{-- Loop through each field --}}
														@foreach($value as $name => $text )
															{{--<li><strong>{{ trans('admin.' . $name) }}</strong>: {{ $text }}</li>--}}
															<div>
																<strong>{{ trans('admin.' . $name) }}</strong>
																<p>{{ $text }}</p>
															</div>
														@endforeach

													@endif
												</div>
											</div>
										</div>
									@endif

								@endforeach
							@endif

						</div>
					</div>
				</div>

			@endforeach
		@endif

	</div>
</div>

