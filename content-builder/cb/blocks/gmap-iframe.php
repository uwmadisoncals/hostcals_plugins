<?php

	$data = array();
	$data['zoom'] = 1;
	$data['mapTypeId'] = 'ROADMAP';
	$data['location'] = '0,0';

	if( isset($this->data['zoom']) ) {
		if(intval($this->data['zoom'])>0) {
			$data['zoom'] = $this->data['zoom'];
		}
	}

	$types = array('HYBRID','ROADMAP','SATELLITE','TERRAIN');
	if(isset($this->data['mapTypeId'])) {
		$this->data['mapTypeId'] = strtoupper($this->data['mapTypeId']);
		if( in_array($this->data['mapTypeId'],$types)) {
			$data['mapTypeId'] = $this->data['mapTypeId'];
		}
	}
	if(isset($this->data['location'])) {
 		$location = explode(',',$this->data['location']);
		if(count($location)==2) {
			$data['location'] = $this->data['location'];
		}
	}

	$location = explode(',',$data['location']);
	$oLatLng = 'new google.maps.LatLng('.$location[0].','.$location[1].')';
?>
<!DOCTYPE html>
<html>
<head>
	<title>Google Map Widget at (contentbuilder.net)</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
	<meta charset="utf-8">
	<style>html, body, #map_canvas {margin: 0;padding: 0;height: 100%;}</style>
	<script src="https://maps.googleapis.com/maps/api/js?sensor=false"></script>
	<script>
		var map, marker, geocoder, editor;
		function initialize() {
			geocoder = new google.maps.Geocoder();
			var mapOptions = {
				zoom: <?php echo $data['zoom']; ?>,
				center: <?php echo $oLatLng; ?>,
				disableDefaultUI:true,
				mapTypeId: google.maps.MapTypeId.<?php echo $data['mapTypeId']; ?>,
				mapTypeControl: true,
				mapTypeControlOptions: {
					style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR
				},
				zoomControl: true
			};
			map = new google.maps.Map(document.getElementById('map_canvas'),mapOptions);
			var markerLocation = <?php echo $oLatLng; ?>;
			marker = new google.maps.Marker({
				draggable: false,
				position: markerLocation,
				title: ''
			});
			marker.setMap(map);
			map.setCenter( marker.getPosition() );
		}
<?php
	if( $action == 'iframe-edit' ) {
?>
		function resize() {
			google.maps.event.trigger( map, 'resize');
			map.setCenter( marker.getPosition() );
		}

		function locateAddress( address ) {
			geocoder.geocode( { 'address': address}, function(results, status) {
				//console.log('status:', results );
				if (status == google.maps.GeocoderStatus.OK) {
					map.setCenter(results[0].geometry.location);
					marker.setPosition( results[0].geometry.location );
					editor.updateLocation( results[0].geometry.location.toUrlValue() );
				} else {
					alert('Address not found!');
				}
			});
		}
<?php
	}
?>
		google.maps.event.addDomListener(window, 'load', initialize);
	</script>
</head>
<body><div id="map_canvas"></div></body>
</html>