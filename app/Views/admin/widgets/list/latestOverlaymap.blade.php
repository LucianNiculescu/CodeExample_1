{{--{{dd($gatewaysHardware)}}--}}
@push('footer-js')

<script src="http://maps.google.com/maps/api/js?sensor=false" type="text/javascript"></script>

<script type="text/javascript">

	var locations = [
		['Gateway Name',  51.4941554, -0.1415062, 1, 'Latency: 4ms', 'Online Guests: 605', 'CPU: 8%', 'AAA Status: '],
		['Gateway Name', 51.538386, -0.043612, 2, 'Latency: 4ms', 'Online Guests: 605', 'CPU: 8%', 'AAA Status: '],
	];

	var map = new google.maps.Map(document.getElementById('map'), {

		center: new google.maps.LatLng(51.4941554, -0.1415062),
		mapTypeId: google.maps.MapTypeId.HYBRID,
		zoom: 18,
		minZoom: 2,
		mapTypeControl: false,
		navigationControl: false

	});



	var infowindow = new google.maps.InfoWindow();

	overlay = new CustomMarker(
			myLatlng,
			map,
			{
				marker_id: '123'
			}
	);


	function CustomMarker(latlng, map, args) {
		this.latlng = latlng;
		this.args = args;
		this.setMap(map);
	}

	CustomMarker.prototype = new google.maps.OverlayView();

	CustomMarker.prototype.draw = function() {

		var self = this;

		var div = this.div;

		if (!div) {

			div = this.div = document.createElement('div');

			div.className = 'marker';

			div.style.position = 'absolute';
			div.style.cursor = 'pointer';
			div.style.width = '20px';
			div.style.height = '20px';
			div.style.background = 'blue';

			if (typeof(self.args.marker_id) !== 'undefined') {
				div.dataset.marker_id = self.args.marker_id;
			}

			// TODO: add the data into this when clicked -  this should create
			var infowindow = new google.maps.InfoWindow();
			google.maps.event.addDomListener(div, "click", function(event) {
				return function() {
					infowindow.setContent(locations[i][0] + '<br>' +locations[i][4] + '<br>' +locations[i][5] + '<br>' +locations[i][6] + '<br>' +locations[i][7]);
					infowindow.open(map, div);
				}
			});

			var panes = this.getPanes();
			panes.overlayImage.appendChild(div);
		}

		var point = this.getProjection().fromLatLngToDivPixel(this.latlng);

		if (point) {
			div.style.left = (point.x - 10) + 'px';
			div.style.top = (point.y - 20) + 'px';
		}
	};

	CustomMarker.prototype.remove = function() {
		if (this.div) {
			this.div.parentNode.removeChild(this.div);
			this.div = null;
		}
	};

	CustomMarker.prototype.getPosition = function() {
		return this.latlng;
	};
</script>

	{{--<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBYMcPER1JU-qZKjPvbuak5KndhMo7jARA&callback=initMap"></script>--}}
@endpush
	<div id="map" class="col-md-12 padding-0" style="height: 490px;"></div>

