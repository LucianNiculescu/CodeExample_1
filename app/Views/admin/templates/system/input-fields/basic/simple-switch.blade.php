<input type="hidden" name="{{$columnName}}" value="false" />
<input type="checkbox" value="true"
	name="{{$columnName}}"
	class="js-switch-small {{$extraClass or ''}}"
	data-plugin="switchery"
	data-size="small"
	{{$extraProperty or ''}}
	data-switchery="true"
	style="display: none;">