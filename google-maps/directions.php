<?php
include ('params.php');

echo '
var map_'.$count.', directions_'.$count.';

latlng="'.$latlng.'", compteur=0;
if (GBrowserIsCompatible()) {
	map_'.$count.' = new GMap2(document.getElementById("Googlemap_'.$count.'"));
	map_'.$count.'.clearOverlays();
	map_'.$count.'.enableDoubleClickZoom();
	map_'.$count.'.enableContinuousZoom();
	map_'.$count.'.setMapType('.$setMapType.');
	map_'.$count.'.setUIToDefault();

	directions_'.$count.' = new GDirections(map_'.$count.', document.getElementById("directions_'.$count.'"));
	directions_'.$count.'.clear();
	GEvent.addListener(directions_'.$count.', "error", function() {
		if (directions_'.$count.'.getStatus().code == G_GEO_UNKNOWN_ADDRESS) {

			if(latlng!="" && compteur < 2) {
				var coord=new GLatLng('.$latlng.');
				map_'.$count.'.setCenter(coord, '.$zoom.');
				directions_'.$count.'.load("from: '.$From.' to: '.$To.' @'.$latlng.'");
				compteur+=1;
			}
			else {
				ErrorMsg="'.__('Error: ',googlemaps).'602\n";
				ErrorMsg+="'.__('Info: ',googlemaps).'http://www.google.com/apis/maps/documentation/reference.html#GGeoStatusCode";
				//alert(ErrorMsg);
			}

		}
		else {
			ErrorMsg="'.__('Error: ',googlemaps).'"+directions_'.$count.'.getStatus().code+"\n";
			ErrorMsg+="'.__('On: ',googlemaps).'\nfrom: '.$From.' to: '.$To.'";
			//alert(ErrorMsg);
		}
	});
	GEvent.addListener(directions_'.$count.', "load", function(){
		map_'.$count.'.addOverlay(directions_'.$count.'.getPolyline());
	});
	directions_'.$count.'.load("from: '.$From.' to: '.$To.'");

	PosLib = document.getElementById("Lib_'.$count.'");
	TagSmall=document.createElement("small")
	Lien = document.createElement(\'a\');
	Lien.setAttribute("href","'.$GLink.'");
	Lien.appendChild(document.createTextNode("'.__('Zoom in',googlemaps).'"));
	TagSmall.appendChild(Lien);
	PosLib.appendChild(TagSmall);
}
else {
	document.getElementById("Googlemap_'.$count.'").innerHTML="'.__("Sorry, your browser can't display Google Maps...",googlemaps).'";
}
';
?>