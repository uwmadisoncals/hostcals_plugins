<?php
include ('params.php');

if($googlemaps_more==1 || $googlemaps_more=="") include ('more.php');

echo '
var map_'.$count.', geoXml=null;

latlng="'.$latlng.'";
if (GBrowserIsCompatible()) {
	map_'.$count.' = new GMap2(document.getElementById("Googlemap_'.$count.'"));
	map_'.$count.'.clearOverlays();
	map_'.$count.'.enableDoubleClickZoom();
	map_'.$count.'.enableContinuousZoom();
	map_'.$count.'.setMapType('.$setMapType.');

	if(latlng!="") {
		var coord=new GLatLng('.$latlng.');
		map_'.$count.'.setCenter(coord, '.$zoom.');
	}

	geoXml = new GGeoXml("'.$URL.'");
	map_'.$count.'.addOverlay(geoXml);
	map_'.$count.'.setUIToDefault();
	';

	if($googlemaps_more==1 || $googlemaps_more=="")
		echo 'map_'.$count.'.addControl(new MoreControl_'.$count.'());
	';

	echo '
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