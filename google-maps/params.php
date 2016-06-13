<?php
require_once('../../../wp-config.php');
$googlemaps_more=get_option('googlemaps_more');

$latlng=(isset($_GET['ll'])) ? $_GET['ll'] : '';
$count=(isset($_GET['count'])) ? $_GET['count'] : 0;
$zoom=(isset($_GET['z']) && $_GET['z']!="") ? $_GET['z'] : 10;
$sll=(isset($_GET['sll'])) ? $_GET['sll'] : '';
$From=isset($_GET['saddr']) ? $_GET['saddr'] : '';
$To=isset($_GET['daddr']) ? $_GET['daddr'] : '';
$GLink=(isset($_GET['Glink'])) ? $_GET['Glink'] : '';
$kml=(isset($_GET['ml'])) ? $_GET['kml'] : '' ;
$maptype=(isset($_GET['t'])) ? $_GET['t'] : '';

switch ($maptype) {
	case "h":
		$setMapType="G_HYBRID_MAP";
		break;
	case "k":
		$setMapType="G_SATELLITE_MAP";
		break;
	case "p":
		$setMapType="G_PHYSICAL_MAP";
		break;
	default:
		$setMapType="G_NORMAL_MAP";
}

if($latlng=="") $latlng=$sll;
$URL="";

foreach ($_GET as $key => $var) {
	if($key!="Glink" && $key!="count") $URL.="&".$key."=".$var;
}

$GLink.="?".$URL;
$GLink=str_replace("?&","?",$GLink);

if($kml!="") {
	if(strpos($kml,"http://maps.google.com/maps?q=")!==false) {
		$posdeb=strpos($kml,"q=")+2;
		$newURL=substr($kml,$posdeb);
		$posfin=substr($kml,$posdeb);
	}
	$GLink="http://maps.google.com/maps?q=".str_replace(array("&kml=","&"),array("","%26"),$URL);
	$kml=str_replace("&kml=","",$URL);
}

$URL="http://www.google.com/maps/ms?output=nl".str_replace("&kml=","",$URL);

/*
$offset = 60*60*24*30;
$ExpStr = gmdate("D, d M Y H:i:s",time() + $offset)." GMT";
$last_modified_time = gmdate("D, d M Y H:i:s",filemtime($_SERVER['SCRIPT_FILENAME']))." GMT";

header("Last-Modified: ".$last_modified_time);
header("Cache-Control: max-age=".$offset.", must-revalidate");
header("Pragma: private");
header("Expires: ".$ExpStr);
*/
header('Content-Type: application/x-javascript; charset='.get_option('blog_charset'));
?>