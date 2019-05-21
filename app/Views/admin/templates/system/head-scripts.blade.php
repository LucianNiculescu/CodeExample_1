
{{--Sweet alert had a conflict with jquery and the solution was to put it before jquery--}}
{{--<script src="/admin/templates/system/vendor/bootstrap-sweetalert/sweet-alert.min.js"></script>--}}
{{--<script src="/admin/templates/system/vendor/jquery/jquery.js"></script>--}}
{{--<script src="http://cdn.jsdelivr.net/jquery.validation/1.15.0/jquery.validate.min.js"></script>--}}
{{--<script src="http://cdn.jsdelivr.net/jquery.validation/1.15.0/additional-methods.min.js"></script>--}}
<script>
	// values are coming from the GlobalInfoComposer to be used in the draggable script
			@foreach( $jsConstants as $key => $value )
	var {{ strtoupper ($key) }} = '{{$jsConstants[$key] or 'null' }}';
	@endforeach
</script>
<!--[if lt IE 9]>
<script src="/admin/templates/system/vendor/html5shiv/html5shiv.min.js"></script>
<![endif]-->
<!--[if lt IE 10]>
<script src="/admin/templates/system/vendor/media-match/media.match.min.js"></script>
<script src="/admin/templates/system/vendor/respond/respond.min.js"></script>
<![endif]-->
<!-- Scripts -->
{{--<script src="/admin/templates/system/vendor/modernizr/modernizr.js"></script>--}}
{{--<script src="/admin/templates/system/js/search/modernizr.custom.js"></script>--}}
{{--<script src="/admin/templates/system/vendor/breakpoints/breakpoints.js"></script>--}}
{{--blade specific scripts will be pushed here--}}
@stack('header-js')
