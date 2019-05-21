{{--<!-- Fonts -->--}}
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,300italic">
<link rel="stylesheet" href="/admin/templates/system/fonts/font-awesome/font-awesome.css">
<link rel="stylesheet" href="/admin/templates/system/fonts/web-icons/web-icons.css">

<!-- template specific style overrides -->
@if($template != null || $template != '')
	<link rel="stylesheet" href="/admin/templates/{{$template}}/css/colours.css">
	<link rel="stylesheet" href="/admin/templates/{{$template}}/css/structure.css">
@endif

@stack('header-css')