<!DOCTYPE html>
<html lang="en">
<head>
	<title>{{ trans('admin.help') }}</title>
</head>
<body>
	<div class="container-fluid">
		<div class="row">

			<div class="col-md-12">
				{!! $content or trans('admin.no-help') !!}
			</div>

		</div>
	</div>
</body>
</html>