{{--
	To Use
	------
	Include the accordion like so:
		@include('admin.templates.system.input-fields.accordion', [
			'id' 			=> 'MyAccordion',
			'panels' 		=> [
				[
					'title' => 'Panel 1 Title',
					'body' 	=> 'This is a Panel body and the first of its kind
				],
				[
					'title' => 'Panel 2 Title',
					'body' 	=> 'This is a Panel body and the second of its kind
				],
			]
		]

	We can also include "'customClass' => 'cust-class'" after the ID to add 1 or more (space seperated)
	If we want to have the accordian 'fill' a 'row' add the class 'full-width',
	If we want to have the accordian bordered add the class 'bordered'
--}}

<style>
	/* Accordion overrides */
	.accordion.panel-group.bordered .panel{border:1px solid #ccc; border-radius:0}
	.accordion.panel-group.bordered .panel + .panel{margin-top:-1px;}
	.accordion.panel-group.full-width{margin:0 -21px -21px -21px;}
</style>

<div class="accordion panel-group {{ $customClass or '' }}" id="{{ $id or 'Accordion' }}" aria-multiselectable="true" role="tablist">

	@foreach( $panels as $key => $value )
		<div class="panel">
			<div class="panel-heading" id="Heading{{ $key }}{{ $id or 'Accordion' }}" role="tab">
				<a class="panel-title collapsed" data-toggle="collapse" href="#Collapse{{ $key }}{{ $id or 'Accordion' }}" data-parent="#{{ $id or 'Accordion' }}" aria-expanded="false" aria-controls="Collapse{{ $key }}{{ $id or 'Accordion' }}">
					{!! $value['title'] !!}
				</a>
			</div>
			<div class="panel-collapse collapse" id="Collapse{{ $key }}{{ $id or 'Accordion' }}" aria-labelledby="Heading{{ $key }}{{ $id or 'Accordion' }}" role="tabpanel">
				<div class="panel-body">
					{!! $value['body'] !!}
				</div>
			</div>
		</div>

	@endforeach

</div>