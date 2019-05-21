{{--
Exact Copy of input.blade but with extra input and extra error
$columnName is the name and id of the field which would match the DB column name
[$label] is the UI label which should be translated
[$autofocus] is set as 'autofocus' if it is the first form field
[$type] default is text
$fakename is the name and id of the input field, it is fake because it doesn't relate to the DB
[$placeholder]
[$value] if you want to pass a direct value in so it will overwrite any other value from the DB
[$extraError] if you need extra error, make sure you have the exact id translated in the DB
[$help]
$tabindex
--}}

<div class="{{$customFullSize or 'col-md-12 col-sm-12'}} {{$customDivClass or ''}} input-container input-container-advanced-input">
    <div class="row">
        @include('admin.templates.system.input-fields.basic.label')

        <div class="col-md-8 col-sm-12 col-xs-12">
            <div class="form-group {{ $errors->has($columnName) ? ' has-error' : '' }}">

                <input class="form-control" {{ $autofocus or  ''}} tabindex="{{$tabindex}}"
                    type="{{$type or 'text'}}"
                    id={{$fakeName}} name={{$fakeName}}
                    @if(isset($placeholder)) placeholder={{$placeholder}} @endif>

                {{--Showing old value when validation failed or value for the edit mode--}}
                <input type="hidden" id={{$columnName}} name={{$columnName}}
                    @if(!empty(Input::old($columnName)))
                        value="{{Input::old($columnName)}}"
                    @elseif(isset($value))
                       value="{{$value}}"
                    @elseif(isset($module->$columnName))
                       value="{{$module->$columnName}}"
                    @endif

                   {{--Adding client side validation rules--}}
                   {{$validation or ''}}
                >
                {{--Showing Validation Error--}}
                @if ($errors->has($columnName))
                    <span class="help-block">
                        <strong>{{ $errors->first($columnName) }}</strong>
                    </span>
                @endif
                @if(isset($extraError))
                    <span class="help-block" id="{{$extraError}}" style="color:red; display:none">
                        <strong>{{ trans('admin.'.$extraError) }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="col-md-1 hidden-xs hidden-sm padding-0">
            @if(isset($help))
                @include('admin.templates.system.input-fields.help', ['help' => $help, 'extraHelpClass' => 'margin-top-10'])
            @endif
        </div>
    </div>
</div>