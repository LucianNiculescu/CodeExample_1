{{--needs testing--}}
{{--
Switch for boolean values
$columnName is the name and id of the field which would match the DB column name
[$label] is the UI label which should be translated
[$trueValue] default is 'true'
[$falseValue] default is 'false'
[$labelFirst] default is null
$tabindex
--}}

<?php
    if(!isset($trueValue))
        $trueValue = 'true';
    if(!isset($falseValue))
        $falseValue = 'false';
?>

    <div class="{{$customFullSize or 'col-md-12 col-sm-12'}} {{$customDivClass or ''}}">
        <div class="row">
            <div class="col-md-11 col-sm-11 col-xs-11">
                @if(isset($label) && isset($labelFirst))
                    <label class="padding-top-3 padding-right-10 {{$customLabelClass or ''}}" for={{$columnName}} id={{$labelId or ''}}>{{$label}}</label>
                @endif
                <input type="hidden" name={{$columnName}} value={{$falseValue}}>
                <input type="checkbox" value={{$trueValue}} id={{$columnName}} name={{$columnName}} class="{{$class or ''}}" data-plugin="switchery"
                    @if(!empty(Input::old($columnName)))
                        @if(Input::old($columnName) == $trueValue)
                            checked
                        @endif
                    @elseif(isset($value))
                        @if($value == $trueValue)
                            checked
                        @endif
                    @elseif(isset($module->$columnName))
                        @if($module->$columnName == $trueValue)
                            checked
                        @endif
                    @endif
                >
                {{--Label is filled with a JS function--}}
                @if(isset($label) && !isset($labelFirst))
                    <label class="padding-top-3 {{$customLabelClass or ''}}" for={{$columnName}} id={{$labelId or ''}}>{!! '&nbsp;'.$label !!}</label>
                @endif
            </div>
            <div class="col-md-1 hidden-xs hidden-sm padding-0">
                @if(isset($help))
					@include('admin.templates.system.input-fields.help', ['help' => $help, 'extraHelpClass' => 'margin-top-5'])
                @endif
            </div>
        </div>
    </div>

