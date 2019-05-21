
<script>
    /**
     * initMap setups the google map , it is used at the moment for gateways
     * $lat and $lng are passed using php to initiate the map
     * Could be used in later modules, take care of the lat and lng
     * also the input fields location is hidden and the address is the real address, no location is the error message div under the address input
     */
	{{--var mapCenter = {lat: {{isset($lat)? $lat : 51.501364}}, lng: {{isset($lng)? $lng : -0.14189}} };--}}
	var mapCenter = {lat: {{isset($lat)? $lat : 0}}, lng: {{isset($lng)? $lng : 0}} };
	// The rest of the code is in js/map.js
	initMap();
</script>
