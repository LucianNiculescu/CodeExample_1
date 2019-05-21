{{--
Input field used for text, password fields
$columnName is the name and id of the field which would match the DB column name
[$label] is the UI label which should be translated
[$autofocus] is set as 'autofocus' if it is the first form field
[$type] default is text
[$placeholder]
[$value] if you want to pass a direct value in so it will overwrite any other value from the DB
[$help]
[$addonBefore] Item to add in before the input
[$addonAfter] Item to add in after the input
$tabindex
--}}
@if (!isset($sliderText) || ( !$sliderText ))
<div class="{{$customFullSize or 'col-md-12 col-sm-12'}} {{$customDivClass or ''}} input-container input-container-input">
    <div class="row">

        @include('admin.templates.system.input-fields.basic.label')

        <div class="{{$customInputSize or 'col-md-8'}} col-sm-12 col-xs-12">
            <div class="form-group {{ $errors->has($columnName) ? ' has-error' : '' }}">
@endif
                @if(isset($addonBefore) or isset($addonAfter))
                <div class="input-group">
                @endif
                @if(isset($addonBefore))
                    <span class="input-group-addon">{{ $addonBefore }}</span>
                @endif
                <input class="@if(!isset($disabledColor))form-control @endif {{ $extraClass or  ''}} " {{ $autofocus or  ''}} tabindex="{{$tabindex}}"
                    type="{{$type or 'text'}}" id="{{$columnName}}" name="{{$columnName}}" autocomplete="off"

                    @if(isset($disabledColor)) DISABLED style="width:30px;height:30px;padding:0;border:none" @endif
                    {{--Showing old value when validation failed or value for the edit mode--}}

                    @if(!empty(Input::old($columnName)))
                        value="{{Input::old($columnName)}}"
                    @elseif(isset($value))
                       value="{{$value}}"
                    @elseif(isset($module->$columnName))
                       value="{{$module->$columnName}}"
                    @endif

                    {{--Adding client side validation rules--}}
                    {!! $validation or ''  !!}

                    @if(isset($placeholder)) placeholder="{{$placeholder}}" @endif
                >
                @if(isset($addonAfter))
                    <span class="input-group-addon">{{ $addonAfter }}</span>
                @endif
                @if(isset($addonBefore) or isset($addonAfter))
                </div>
                @endif
                @if(isset($disabledColor)) <span style="position: absolute;top: 5px;left: 35px;">{{$value}}</span> @endif
                {{--Showing Validation Error--}}
                @if ($errors->has($columnName))
                    <span class="help-block">
                        <strong>{{ $errors->first($columnName) }}</strong>
                    </span>
                @endif
@if (!isset($sliderText) || ( !$sliderText ))
            </div>
        </div>

        <div class="col-md-1 hidden-xs hidden-sm padding-0">
            @if(isset($help))
                @include('admin.templates.system.input-fields.help', ['help' => $help, 'extraHelpClass' => 'margin-top-10'])
            @endif
        </div>
    </div>
</div>
@endif
