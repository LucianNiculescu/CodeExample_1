{{--Getting jsMessage variable from the MessagesComposer--}}

@push('footer-js')
	@if( !empty ($jsMessage) )
		<script>
			$( document ).ready(function() {
				swal({
					title: "{{ucfirst($jsMessage[0])}}!",
					text: "{{$jsMessage[1]}}",
					type: "{{$jsMessage[0]}}",
					timer: 10000});
			});
		</script>
	@endif
@endpush


<?php Session::forget('messages');?>