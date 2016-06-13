<?php
include ('params.php');

echo '
var map_'.$count.';

latlng="'.$latlng.'";
if (GBrowserIsCompatible()) {
	map_'.$count.' = new GMap2(document.getElementById("Googlemap_'.$count.'"));
	map_'.$count.'.clearOverlays();
	map_'.$count.'.enableDoubleClickZoom();
	map_'.$count.'.enableContinuousZoom();
	map_'.$count.'.setMapType('.$setMapType.');

	geoXml = new GGeoXml("'.$kml.'");

	if(latlng!="") {
		var coord=new GLatLng('.$latlng.');
		map_'.$count.'.setCenter(coord, '.$zoom.');
	}
	else
		map_'.$count.'.setCenter(new GLatLng(0,0), 1);

	map_'.$count.'.addOverlay(geoXml);
	map_'.$count.'.setUIToDefault();
	geoXml.gotoDefaultViewport(map_'.$count.');

	PosLib = document.getElementById("Lib_'.$count.'");
	TagSmall=document.createElement("small")
	Lien = document.createElement(\'a\');
	Lien.setAttribute(\'href\',"'.$GLink.'");
	Lien.appendChild(document.createTextNode("'.__('Zoom in',googlemaps).'"));
	TagSmall.appendChild(Lien);
	PosLib.appendChild(TagSmall);
}
else {
	document.getElementById("Googlemap_'.$count.'").innerHTML="'.__("Sorry, your browser can't display Google Maps...",googlemaps).'";
}
';
?>