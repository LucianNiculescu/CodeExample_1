{{--
input-group-with-select.blade is used for dropdownlists with a text input
$selectColumnName is the name and id of the field of the select which would match the DB column name
$textColumnName is the name and id of the field of the text input which would match the DB column name
[$label] is the UI label which should be translated
[$autofocus] is set as 'autofocus' if it is the first form field
[$placeholder]
options is a yield because there is logic
$list is the array that will be looped in to set the value as the Key
$default is the default option
[$extra] is used to pass something extra like data-extra="{{$extra}}"
[$keyKey] is when the item is an array and the item has a key for the option value
[$itemKey] is when an item is an array and the item has a key for the option display
[$noKey] is when a list has no specific key so $key = $item
[$help]
$tabindex
--}}
<div class="{{$customFullSize or 'col-md-12 col-sm-12'}} {{$customDivClass or ''}}  input-container input-container-select">
    <div class="row">

        @include('admin.templates.system.input-fields.basic.label')

        <?php // newColumnName is used for columnNames with '[' or ']' to remove them and use it in the validation code instead of the columnName
            $newColumnName = str_replace('[', '', str_replace(']', '', $selectColumnName));
        ?>

		<div class="{{$customInputSize or 'col-md-8'}} col-sm-12 col-xs-12">
			<div class="form-group {{ $errors->has($newColumnName) ? ' has-error' : '' }}">
				<div class="input-group">
					<div class="input-group-btn">
						<select class="form-control {{$selectClass or 'chosen-select'}}" tabindex="{{$tabindex}}"
								id="{{$selectColumnName}}" name="{{$selectColumnName}}"

								{{--Adding client side validation rules--}}
								{{$selectValidation or ''}}

								@if(isset($selectPlaceholder)) data-placeholder="{{$selectPlaceholder}}" @endif
						>
							{{--<option value=""></option>--}}

							@if(isset($list))
								{{--Fill in  the foreachloop in the options sent in the list--}}
								@foreach($list as $key => $item)

									<?php
									if(isset($keyKey))
									{
										$key  = $item[$keyKey];
										$item = $item[$itemKey];
									}
									if(isset($noKey))
										$key = $item;
									?>

									<option value="{{$key}}" {{$extra or ''}}
									@if(isset($location)) data-location="{{$item[1]}}" @endif
											@if(isset($reference)) data-reference="{{$item[2]}}" @endif

											{{-- Dissabled --}}
											@if( isset($disabledArray) && in_array( $key, $disabledArray ) ) disabled="disabled" @endif

											{{--Showing old value when validation failed or value for the edit mode--}}
											@if( !empty(Input::old($selectColumnName)))
											@if($key == Input::old($selectColumnName))
											selected
											@endif
											@elseif(isset($selectValue))
											@if($key == $selectValue)
											selected
											@elseif(is_array($selectValue))
											@if(in_array($key,array_keys($selectValue)))
											selected
											@endif
											@endif
											@elseif(isset($module->$selectColumnName))
											@if($key == $module->$selectColumnName)
											selected
											@endif
											@else
											@if(isset($default) and $key == $default)
											selected
											@endif
											@endif>

										@if(is_array($item) and !empty($item))
											{{$item[0]}}
										@else
											{{$item}}
										@endif
									</option>
								@endforeach
							@endif
						</select>
						{{--Showing Validation Error--}}
						@if ($errors->has($newColumnName))

							<span class="help-block">
                                <strong>{{ $errors->first($newColumnName) }}</strong>
                            </span>
						@endif
					</div>
					<input class="@if(!isset($disabledColor))form-control @endif {{ $extraClass or  ''}}" {{ $autofocus or  ''}} tabindex="{{$tabindex}}"
						   type="{{$type or 'text'}}" id={{$textColumnName}} name={{$textColumnName}}

					@if(isset($disabledColor)) DISABLED style="width:30px;height:30px;padding:0;border:none" @endif
						   {{--Showing old value when validation failed or value for the edit mode--}}

						   @if(!empty(Input::old($textColumnName)))
						   value="{{Input::old($textColumnName)}}"
						   @elseif(isset($textValue))
						   value="{{$textValue}}"
						   @elseif(isset($module->$textColumnName))
						   value="{{$module->$textColumnName}}"
						   @endif

						   {{--Adding client side validation rules--}}
						   {!! $validation or ''  !!}

						   @if(isset($textPlaceholder)) placeholder="{{$textPlaceholder}}" @endif
					>
					@if(isset($disabledColor)) <span style="position: absolute;top: 5px;left: 35px;">{{$value}}</span> @endif



					@if(isset($hasButton))
						<span class="input-group-btn">
							<a href="{{$buttonHref or '#'}}" class="btn btn-default pull-right {{$customButtonClass or ''}}" role="button" target="{{$buttonTarget or '_blank'}}">{{$buttonLabel or trans('admin.preview')}}</a>
						</span>
					@endif
				</div>
			</div>

		</div>

		<div class="col-md-1 hidden-xs hidden-sm padding-0">
			@if(isset($help))
				@include('admin.templates.system.input-fields.help', ['help' => $help, 'extraHelpClass' => 'margin-top-10'])
			@endif
		</div>
	</div>
	<div class="row">
        <div class="col-md-12">
			{{--Showing Validation Error--}}
			@if ($errors->has($textColumnName))
				<span class="help-block">
							<strong>{{ $errors->first($textColumnName) }}</strong>
						</span>
			@endif
		</div>
    </div>

</div>
