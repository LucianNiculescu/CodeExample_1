{{--
Input field used for text, password fields
$columnName is the name and id of the field which would match the DB column name
[$label] is the UI label which should be translated
[$autofocus] is set as 'autofocus' if it is the first form field
[$type] default is text
[$step] default is 0.5
[$min]  default is 0
[$max]  default is 100
$value is a yield because there is logic
[$help]
$tabindex
--}}
<div class="{{$customFullSize or 'col-md-12 col-sm-12'}} {{$customDivClass or ''}} input-container input-container-slider">
	<div class="row">
		@php
		if (isset($sliderText) && $sliderText) {
			$sliderWidth = "col-md-5 col-sm-12 col-xs-12";
		} else {
			$sliderWidth = "col-md-8 col-sm-12 col-xs-12";
		}
		@endphp

		@include('admin.templates.system.input-fields.basic.label')

		<div class="{{ $sliderWidth }}  margin-top-15">
			<div class="form-group {{ $errors->has($columnName) ? ' has-error' : '' }}">
				<input
					class="asRange"
					data-plugin="asRange"
					data-namespace="rangeUi"
					tabindex="{{$tabindex}}"
					id="{{$columnName}}"
					name="{{$columnName}}"
					data-step="{{$step or 0.5}}"
					data-min="{{$min or 0}}"
					data-max="{{$max or 100}}"
					data-tip="true"
					data-pre = "{{$preLabel or ''}}"
					data-post = "{{$postLabel or ''}}"

					@php
					//It's not ideal to have code in a view but we need to calculate a value and use it in multiple places
					if(!empty(Input::old($columnName))) {
						$useValue= Input::old($columnName);
					} elseif(isset($value)) {
						$useValue = $value;
					} elseif(isset($module->$columnName)) {
						$useValue = $module->$columnName;
					}
					@endphp

					@if( isset($useValue) )
						data-value="{{$useValue}}"
						value="{{$useValue}}"
					@endif
				>

				{{--Showing Validation Error--}}
				@if ($errors->has($columnName))
					<span class="help-block">
						<strong>{{ $errors->first($columnName) }}</strong>
					</span>
				@endif
			</div>
		</div>

		@if (isset($sliderText) && $sliderText)
			{{--Download text input--}}
			@php
				// It's not ideal to have code in a view but we need to generate
				// the parameters for the text input from the parameters for this include
				$textParams =
					[
						'columnName'    => $columnName . "-text",
						'type'			=> 'number',
						'tabindex'		=> $tabindex+1,
					];
				if( isset($min) ) {
					$validation = 'min:' . $min;
				} else {
					$validation = 'min:0';
				}
				if( isset($max) ) {
					$validation .= 'max:' . $max;
				} else {
					$validation .= 'max:100';
				}
				$textParams['validation'] = $validation;

				if( isset($useValue) ) {
					$textParams['value'] = $useValue;
				}
			@endphp

			<div class="col-md-3 slider-text">
				@include('admin.templates.system.input-fields.basic.input', $textParams)
			</div>

		@endif
		<div class="col-md-1 hidden-xs hidden-sm padding-0" style="margin-top:-5px">
			@if(isset($help))
				@include('admin.templates.system.input-fields.help', ['help' => $help, 'extraHelpClass' => 'margin-top-15'])
			@endif
		</div>
	</div>
</div>

