@push('footer-js')
<script>
	$( document ).ready(function() {
		@foreach($notifications as $notification)
			notification("{{ strtolower($notification->type) }}", "{!! trim($notification->message) !!}");
		@endforeach
	});
</script>
@endpush