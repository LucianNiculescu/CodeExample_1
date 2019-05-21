{{--
Select.blade is used for dropdownlists
$columnName is the name and id of the field which would match the DB column name
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

<div class="{{$customFullSize or 'col-md-12 col-sm-12'}} {{$customDivClass or ''}} input-container input-container-select">
    <div class="row">

        @include('admin.templates.system.input-fields.basic.label')

        <?php // newColumnName is used for columnNames with '[' or ']' to remove them and use it in the validation code instead of the columnName
            $newColumnName = str_replace('[', '', str_replace(']', '', $columnName));
        ?>

        <div class="{{$customInputSize or 'col-md-8'}} col-sm-12 col-xs-12">
            <div class="form-group {{ $errors->has($newColumnName) ? ' has-error' : '' }}">

                <select class="form-control chosen-select {{$class or ''}}"  {{ $autofocus or  ''}} tabindex="{{$tabindex}}"
                    id={{$columnName}} name={{$columnName}}

                    {{--Adding client side validation rules--}}
                    {{$validation or ''}}

                    @if(isset($placeholder)) data-placeholder={{$placeholder}} @endif>
                    @if(isset($list))
                        {{--Fill in  the foreachloop in the options sent in the list--}}
                        @foreach($list as $group => $items)
                            <optgroup label="{{strtoupper($group)}}">
                                @foreach($items as $key => $item)
                                    <option value="{{$key}}"
                                        {{--Showing old value when validation failed or value for the edit mode--}}
                                        @if( !empty(Input::old($columnName)))
                                            @if($key == Input::old($columnName))
                                                selected
                                            @endif
                                        @elseif(isset($value))
											@if($key == $value)
                                                selected
                                            @elseif(is_array($value))
                                                @if(in_array($key,array_keys($value)))
                                                    selected
                                                @endif
                                            @endif
                                        @elseif(isset($module->$columnName))
                                            @if($key == $module->$columnName)
                                                selected
                                            @endif
                                        @else
                                            @if(isset($default) and $key == $default)
                                                selected
                                            @endif
                                        @endif>{{$item}}
                                    </option>
                                @endforeach
                            </optgroup>
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
        </div>

        <div class="col-md-1 hidden-xs hidden-sm padding-0">
            @if(isset($help))
                @include('admin.templates.system.input-fields.help', ['help' => $help, 'extraHelpClass' => 'margin-top-10'])
            @endif
        </div>
    </div>

</div>
