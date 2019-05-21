
function initMap() {
	// var mapCenter = {lat: 53.426952, lng: -2.528975}; // Airangel Location
	// default location now is 0, 0 if no $lat and $lng is passed to the map blade


	var locationInput   = $("#location");
	var locationError   = $("#no-location");
	var fakeAddress    = $("#fakeAddress");
	var address1Input   = $(".address1");
	var address2Input   = $(".address2");
	var town            = $(".town");
	var postcodeInput   = $(".postcode");
	var countrySelect   = $(".country");


	// Create a map object and specify the DOM element for display.
	var map = new google.maps.Map(document.getElementById('map'), {
		zoom: 18,
		minZoom: 2,
		center: mapCenter,
		mapTypeControl: false,
		navigationControl: false,
		mapTypeId: google.maps.MapTypeId.HYBRID
	});

	// Create a marker and set its position.
	var sitemarker = new google.maps.Marker({
		map: map,
		position: mapCenter,
		icon: '/admin/templates/system/images/marker.png',
		animation: google.maps.Animation.DROP,
		draggable: true

	});
	//setSiteLocation fills the address and location fields
	setLocation();

	// After the marker has been dragged, change the location in the hidden form element and create the address
	google.maps.event.addListener(sitemarker, 'dragend', function () {
		setLocation();
	});

	/**
	 * setSiteLocation fills the address and location fields
	 */
	function setLocation()
	{
		// Rounding latitude and longitude to the nearest 11 dicimal to fit into the database
		var lat = Math.round(sitemarker.position.lat() * 100000000000) / 100000000000;
		var lng = Math.round(sitemarker.position.lng() * 100000000000) / 100000000000;

		var latLong = lat.toString() + ", " + lng.toString();

		// Setting the location hidden input with lat, lng
		locationInput.val(latLong);
		geocodeSitePosition(sitemarker.getPosition());
	}

	// Convert the location into an address
	function geocodeSitePosition(pos) {
		geocoder = new google.maps.Geocoder();
		geocoder.geocode({
				latLng: pos
			},
			function (results, status) {
				if (status == google.maps.GeocoderStatus.OK) {

					fakeAddress.val(results[0].formatted_address);
					var postcode = extractFromAdress(results[0].address_components, "postal_code").long_name;
					var country = extractFromAdress(results[0].address_components, "country").short_name;
					var countryName = extractFromAdress(results[0].address_components, "country").long_name;
					var formattedAddress = results[0].formatted_address;
					//Splitting it with : as the separator
					var addressArray = formattedAddress.split(",");

					if(addressArray[0] !== undefined)
						address1Input.val(addressArray[0].replace(" " + postcode, "").replace(" " + countryName,"").trim());
					else
						address1Input.val('');

					if(addressArray[1] !== undefined)
						address2Input.val(addressArray[1].replace(" " + postcode, "").replace(" " + countryName,"").trim());
					else
						address2Input.val('');

					if(addressArray[2] !== undefined)
						town.val(addressArray[2].replace(" " + postcode, "").replace(" " + countryName,"").trim());
					else
						town.val('');

					postcodeInput.val(postcode);
					countrySelect.val(country).trigger("chosen:updated");

					// Hide the no-location error div
					locationError.hide();
				}
				else {
					// Keep address as is but empty the hidden location input field
					fakeAddress.val('');
					locationInput.val('');
					// Show no location error
					//locationError.show();
				}
			});
	}

	function extractFromAdress(components, type){
		for (var i=0; i<components.length; i++)
			for (var j=0; j<components[i].types.length; j++)
				if (components[i].types[j]==type) return components[i] ;
		return "";
	}

	// Geocode an address
	function codeAddress(address) {

		geocoder.geocode({'address': address}, function (results, status) {
			if (status == google.maps.GeocoderStatus.OK) {
				// Center the map
				map.setCenter(results[0].geometry.location);

				// Mover the pointer
				sitemarker.setPosition(results[0].geometry.location);

				// Add the location to the form
				// Rounding latitude and longitude to the nearest 11 dicimal to fit into the database
				var lat = Math.round(results[0].geometry.location.lat() * 100000000000) / 100000000000;
				var lng = Math.round(results[0].geometry.location.lng() * 100000000000) / 100000000000;

				var latLong = lat.toString() + ", " + lng.toString();

				locationInput.val(latLong);
				// show marker
				//marker.setMap(map);
				locationError.hide();

			} else {
				//alert('We could not find that address, please try again with a closer match');
				//console.log('Geocode was not successful for the following reason: ' + status);
				//fakeAddress.val("");
				locationInput.val("");
				//locationError.show();
				// hide marker
				//marker.setMap(null);
			}
		});
	}

	// On typing of the address field move the pointer to the encoded address location
	// There is a delay of 1 secound to refresh the location after typing
	var timeoutId = 0;

	$('body').on('keyup', '#fakeAddress', function () {
		changeAddress($(this));
	});

	function changeAddress(thisInput)
	{
		clearTimeout(timeoutId);
		timeoutId = setTimeout(function(){
			// Geocode the address to latlong
			//alert($(this).val());
			var address = thisInput.val();
			newLatLong = codeAddress(address);
		}, 1000);
	}
	/**
	 * When changing the hidden location input field the map and the address input field will reflect to the new location lat, lng
	 */
	locationInput.on("change",function(){
		var location = $(this).val().toString();

		var lat = 0;
		var lng = 0;
		if(location !="")
		{// Show the marker and setup the right lat and lng from the hidden location input field
			var latlng = location.split(',');
			lat = parseFloat(latlng[0]);
			lng = parseFloat(latlng[1]);
			sitemarker.setMap(map);
		}
		else
		{// Hide marker and empty the address input field
			address1Input.val("");
			address2Input.val("");
			town.val("");
			postcodeInput.val("");
			countrySelect.val("");
			sitemarker.setMap(null);
		}
		// Setup the map and fill the address input field
		var googleLocation = new google.maps.LatLng(lat, lng);
		map.setCenter(googleLocation);
		sitemarker.setPosition(googleLocation);
		setLocation();

	});

}



