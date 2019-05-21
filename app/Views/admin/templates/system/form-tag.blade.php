@yield('before-form-content')

<form autocomplete="off" class="form-horizontal form_blade validate-me" method="post" action="{{ $actionUrl }}" id="form" @if(isset($uploadFiles)) enctype="multipart/form-data" @endif>
	{!! csrf_field() !!}
	<input name="_method" type="hidden" value="{{$hiddenMethod}}">

	@yield('form-tag-content')

</form>

@yield('after-form-content')