{{--
Hidden used
$columnName is the name and id of the field which would match the DB column name
[$value]
--}}
<input type="hidden" name={{$columnName}} id={{$columnName}} class={{$columnName}}
    @if(isset($value))
        value="{{$value}}"
    @elseif(isset($module->$columnName))
        value="{{$module->$columnName}}"
    @endif
/>
