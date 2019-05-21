{{--
Input field used for text, password fields
$columnName is the name and id of the field which would match the DB column name
[$label] is the UI label which should be translated
[$autofocus] is set as 'autofocus' if it is the first form field
[$placeholder]
[$value] if you want to pass a direct value in so it will overwrite any other value from the DB
[$help]
$tabindex
--}}
<div class="{{$customFullSize or 'col-md-12 col-sm-12'}} {{$customDivClass or ''}} clearfix col-inner-border">
	<div class="row">
		<div class="col-md-11 ">
			<h3 class="margin-0 padding-bottom-15">{{$label}}</h3>
		</div>

		<div class="col-md-1 hidden-xs hidden-sm padding-0">
			@if(isset($help))
				@include('admin.templates.system.input-fields.help', ['help' => $help, 'extraHelpClass' => 'margin-top-0'])
			@endif
		</div>

		<div class="col-md-12 ">
			<textarea class="form-control"  {{ $autofocus or  ''}} tabindex="{{$tabindex}}"
				id={{$columnName}} name={{$columnName}}
				rows={{$rows or 3}}
				{{$validation or ''}}
				@if(isset($placeholder)) placeholder={{$placeholder}} @endif
			>@if(!empty(Input::old($columnName))){{Input::old($columnName)}}
				@elseif(isset($value)){{$value}}@elseif(isset($module->$columnName)){{$module->$columnName}}@endif</textarea>
		</div>
	</div>
</div>