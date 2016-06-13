<?php
include ('params.php');
$LibAdresse="";

if(isset($_GET['q'])) {
	$q=$_GET['q'];
	$pos = strpos($q, ',');
	if($pos!==false) {
		$Ville="<div style='text-transform:capitalize;'><b>".substr($q, 0, $pos)."</b></div>";
		$Lieu="<p style='text-transform:capitalize;'>".substr($q, $pos+1)."<br/><small>".__('Approximate placement',googlemaps)."</small></p>";
		$LibAdresse=$Ville.$Lieu;
	}
	else {
		$pos = strpos($q, ' ');
		if($pos!==false) {
			$LibAdresse="<p style='text-transform:capitalize;'><b>".$q."</b><br/><small>".__('Approximate placement',googlemaps)."</small></p>";
		}
		else {
		//Est-il nécéssaire d'afficher uniquement une info (le nom de la ville)
		//$LibAdresse="<p><b>".$q."</b></p>";
		$q="";
		}
	}
}
else $q="";

if($googlemaps_more==1 || $googlemaps_more=="") include ('more.php');

echo '
var map_'.$count.', geocoder=null, geoXml=null, latlng;

latlng="'.$latlng.'", q="'.$q.'";
if (GBrowserIsCompatible()) {
	map_'.$count.' = new GMap2(document.getElementById("Googlemap_'.$count.'"));
	map_'.$count.'.clearOverlays();
	map_'.$count.'.enableDoubleClickZoom();
	map_'.$count.'.enableContinuousZoom();
	map_'.$count.'.setMapType('.$setMapType.');
	map_'.$count.'.setUIToDefault();
	';
	if($googlemaps_more==1 || $googlemaps_more=="") echo 'map_'.$count.'.addControl(new MoreControl_'.$count.'());';
	echo '

	if(q!="") {
		adresse="'.$q.'";
		geocoder = new GClientGeocoder();
		geocoder.getLocations(adresse, 
		function(reponse) {
			if (!reponse || reponse.Status.code != 200) {
				ErrorMsg= "'.__('Impossible To GeoCode:',googlemaps).'\n";
				ErrorMsg+="'.__('Error Code: ',googlemaps).'"+reponse.Status.code+"\n";
				ErrorMsg+="'.__('Info: ',googlemaps).'http://www.google.com/apis/maps/documentation/reference.html#GGeoStatusCode";
				//alert(ErrorMsg);

				if(latlng!="") {
					var coord=new GLatLng('.$latlng.');
					map_'.$count.'.setCenter(coord, '.$zoom.');

					var marker = new GMarker(coord);
					GEvent.addListener(marker, "click",
					function() {
						marker.openInfoWindowHtml("'.$LibAdresse.'");
					});
					map_'.$count.'.addOverlay(marker);
					marker.openInfoWindowHtml("'.$LibAdresse.'");
				}

			}
			else {
				place = reponse.Placemark[0];
				var Adresse = place.address;
				var Glatitude = place.Point.coordinates[1];
				var Glongitude = place.Point.coordinates[0];

				InfoArray=Adresse.split(", ");
				Adresse="<div style=\'text-transform:capitalize;\'><b>"+InfoArray[0]+"</b></div>";
				for(var i=1; i<InfoArray.length; i++) {
					Adresse+="<div style=\'text-transform:capitalize;\'>"+InfoArray[i]+"</div>";
				}

				var coord=new GLatLng(Glatitude,Glongitude);
				map_'.$count.'.setCenter(coord, '.$zoom.');
				var marker = new GMarker(coord);
				GEvent.addListener(marker, "click",
				function() {
					marker.openInfoWindowHtml(Adresse);
				});
				map_'.$count.'.addOverlay(marker);
				marker.openInfoWindowHtml(Adresse);
			}
		});
	}
	else {
		var coord=new GLatLng('.$latlng.');
		map_'.$count.'.setCenter(coord, '.$zoom.');

		geoXml = new GGeoXml("'.$URL.'");
		map_'.$count.'.addOverlay(geoXml);
		//geoXml.gotoDefaultViewport(map_'.$count.');
	}

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