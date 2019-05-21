<div class="{{$customFullSize or 'col-md-12 col-sm-12'}} {{$customDivClass or ''}} input-container input-container-input">
    <div class="row">

		@include('admin.templates.system.input-fields.basic.label')

        <div class="{{$customInputSize or 'col-md-8'}} col-sm-12 col-xs-12">
			<p class="margin-right-10 margin-top-10 {{$customReadonlyClass or 'capitalize-text'}}">
				@if(isset($value))
					{!! $value !!}
				@elseif(isset($module->$columnName))
					@if($columnName == 'created' or $columnName == 'updated' )
						{{\App\Helpers\DateTime::medium($module->$columnName, true)}}
					@else
						{{$module->$columnName}}
					@endif
				@endif
			</p>
			@if(isset($hiddenValue))
				<input  type="hidden" id={{$columnName}} name={{$columnName}}
				@if(!empty(Input::old($columnName)))
					value="{{Input::old($columnName)}}"
				@elseif(isset($value))
					value="{{$value}}"
				@elseif(isset($module->$columnName))
					value="{{$module->$columnName}}"
				@endif
				>
			@endif
        </div>

        <div class="col-md-1 hidden-xs hidden-sm padding-0">
            @if(isset($help))
				@include('admin.templates.system.input-fields.help', ['help' => $help, 'extraHelpClass' => 'margin-top-15'])
            @endif
        </div>
    </div>
</div>