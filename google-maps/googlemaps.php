<?php
/*
Plugin Name: Google Maps
Plugin URI: http://wordpress.org/extend/plugins/google-maps/
Description: Create XHTML code to embed Google maps in your articles.
Author: Pierre Sudarovich
Author URI: http://www.monblogamoua.fr/
Version: 1.81
*/

define('googlemaps', 'google-maps/lang/googlemaps');

$googlemaps_version = "1.8";
$googlemaps_userlevel = 8;
$count=0;
$defaultWidth="100%";
$defaultHeight="520px";

if ( !function_exists('current_user_can') ) :
	function current_user_can() { return 0; }
endif;

if(function_exists('load_plugin_textdomain')) load_plugin_textdomain(googlemaps);

function googlemaps_embed($matches) {
global $count, $defaultWidth, $defaultHeight, $width, $height;

	$PathToPlugin = get_bloginfo('wpurl') . "/wp-content/plugins/google-maps";
	$googlemaps_key=get_option('googlemaps_key');
	$googlemaps_more=get_option('googlemaps_more');

	$param=trim($matches[3]);

	$params = explode(" ", $param);
	$arg = count($params);

	if($arg>1) $param =substr($param, 0, strpos($param, ' '));

	$input = explode("&", $param);

	$URL=$input[0];
	$pos = strpos($URL, '?');
	$thevalue=substr($URL, $pos);
	$URL=substr($param, $pos);
	$URL=str_replace("?&amp;","?",$URL);

	if ($arg == 3) list($url, $width, $height) = $params;
	else if ($arg==2) list($url, $width) = $params;
	else if ($arg == 1) list($url) = $params;
	else return "";

	$posLink = strpos($url, '?');
	$Linkvalue=substr($url, 0, $pos);

	if($width=="") {
		$width=get_option('googlemaps_width');
		if($width=="")
			$width=$defaultWidth;
	}
	if($height=="") {
		$height=get_option('googlemaps_height');
		if($height=="")
			$height=$defaultHeight;
	}

	$pattern="#(?:(px|%))#";
	if (!preg_match_all($pattern, $width, $params)) $width.="px";
	if (!preg_match_all($pattern, $height, $params)) $height.="px";

	if (strpos($URL, 'kml')!==false) {
		$JSFile="kml";
		$TheLink=$Linkvalue;
		$Linkvalue="";
		$URL="?kml=".$TheLink.$URL;
	}
	elseif(strpos($URL, 'q=')!==false) $JSFile="markers";
	elseif (strpos($URL, 'saddr=')!==false && strpos($URL, 'daddr=')!==false) $JSFile="directions";
	else $JSFile="maps";

	$input="";
	if($count==0) $input='<script src="http://maps.google.com/maps/?file=api&amp;v=2&amp;key='.$googlemaps_key.'" type="text/javascript"></script>';

	$input.='<div class="GoogleMap" id="Googlemap_'.$count.'" style="margin:auto;width:'.$width.';height:'.$height.';">';
	$input.=__('Loading...',googlemaps).'<br/><noscript class="infoG">';
	$input.=__('Be careful to see the map you have to activate the Javascript!',googlemaps).'</noscript></div>';
	$input.='<div id="Lib_'.$count.'" style="margin:auto;width:'.$width.';">&nbsp;</div><div id="directions_'.$count.'"></div>';
	if($googlemaps_more==1 || $googlemaps_more=="") {
		$input.='<div id="outer_more_'.$count.'" class="outer_more"><form action=""><div id="box_'.$count.'" class="box_more" style="display:none;">
		<input name="mark_'.$count.'" type="checkbox" onclick="switchLayer(this.checked, layers[0].obj, '.$count.')"/> '.__('Photos',googlemaps).' <br/>
		<input name="mark_'.$count.'" type="checkbox" onclick="switchLayer(this.checked, layers[1].obj, '.$count.')"/> '.__('Videos',googlemaps) .' <br/>
		<input name="mark_'.$count.'" type="checkbox" onclick="switchLayer(this.checked, layers[2].obj, '.$count.')"/> '.__('Wikipedia',googlemaps) .' <br/>
		<input name="mark_'.$count.'" type="checkbox" onclick="switchLayer(this.checked, layers[3].obj, '.$count.')"/> '.__('Webcams',googlemaps). '
		<hr class="more_sep"/>
		<div class="boxlink"><a id="boxlink_'.$count.'" href="javascript:void(0)" onclick="hideAll('.$count.')">'.__('Hide all',googlemaps).'</a></div></div></form></div>';
	}
	$input.='<script type="text/javascript" src="'.$PathToPlugin.'/'.$JSFile.'.php'.$URL.'&amp;Glink='.$Linkvalue.'&amp;count='.$count.'"></script>';

$count+=1;
return $input;
}

function googlemaps_parse($content) {
	$allblocks = '(?:address|blockquote|code|div|h[1-6]|p|pre)';
	$content = preg_replace_callback( "/(<$allblocks(.*?)>)?\[map:([^]]+)](<\/$allblocks>)?/i", "googlemaps_embed", $content );
	return $content;
}

function googlemaps_admin_page () { 
global $user_level, $googlemaps_userlevel, $googlemaps_version;

    get_currentuserinfo();

	$current=current_user_can('level_'.$googlemaps_userlevel);

	if ($user_level < $googlemaps_userlevel &&  $current!=1) {
		echo '<div class="wrap"><h2>' . __("No Access for you!",wordspew) .'</h2></div>';
	}
	else { ?>
	<div class="wrap">
		<h2><?php _e('Google Maps Embedded',googlemaps);?> v. <?php echo $googlemaps_version; ?></h2>

		<form name="googlemaps_options" action="edit.php?page=googlemaps" method="post" id="googlemaps_options">
		<input type="hidden" name="page" value="googlemaps" />
		
		<fieldset>
			<legend><b><?php _e('GoogleMaps Parameters',googlemaps);?></b></legend>
			<div class="UserOption">
				<?php _e('Key',googlemaps);?>: 
				<input type="text" name="googlemaps_key" value="<?php echo get_option('googlemaps_key'); ?>" 
				size="100" /> 
				<div class="SousRub"><?php _e('Type here the API key furnish by Google. If you haven\'t yet get one, <a href="http://code.google.com/intl/fr/apis/maps/signup.html" target="_blank">click here</a> for sign up, then copy-paste your given key in the field above.',googlemaps);?></div><br />

				<?php _e('Width',googlemaps);?>: 
				<input type="text" name="googlemaps_width" value="<?php echo get_option('googlemaps_width'); ?>" size="6" /> <?php _e('in pixel or percentage (example <b>250px</b> or <b>95%</b>)',googlemaps);?>
				<div class="SousRub"><?php _e('Width by default of your Google map',googlemaps);?></div><br />
				
				<?php _e('Height',googlemaps);?>: 
				<input type="text" name="googlemaps_height" value="<?php echo get_option('googlemaps_height'); ?>" size="6" /> <?php _e('in pixel or percentage (example <b>250px</b> or <b>95%</b>)',googlemaps);?>
				<div class="SousRub"><?php _e('Height by default of your Google map',googlemaps);?></div><br />
				
				<input type="checkbox" name="googlemaps_more" value="1"<?php if(get_option('googlemaps_more')=='1' || get_option('googlemaps_more')=='') { echo ' checked="checked" '; } ?>/>
				<?php _e('Use the button <i>More</i>',googlemaps);?>
				<div class="SousRub"><?php _e('Check this if you want to add the possibility to show more layers on maps.',googlemaps);?></div>

			</div>
		</fieldset>
		<br />

		<input type="submit" name="googlemaps_options" value="<?php _e('Save',googlemaps);?>" class="button" style="font-size: 140%"  />
		</form>
	</div>
<?php }
}

function add_Googlemaps_link($links, $file) {
    if (strstr($file, 'google-maps/googlemaps.php')) {
        $settings_link = '<a href="tools.php?page=googlemaps.php">'. __('Settings').'</a>';
		array_unshift( $links, $settings_link );
    }
    return $links;
}

function googlemaps_options() {
global $user_level, $googlemaps_userlevel;

    get_currentuserinfo();
	$current=current_user_can('level_'.$googlemaps_userlevel);

    if ($user_level <  $googlemaps_userlevel && $current!=1) die(__("Cheatin' uh ?"));

	update_option('googlemaps_key', $_POST['googlemaps_key']);
	update_option('googlemaps_width', $_POST['googlemaps_width']);
	update_option('googlemaps_height', $_POST['googlemaps_height']);
	$button_more = (isset($_POST['googlemaps_more'])) ? 1 : 0;
	update_option('googlemaps_more', $button_more);	
}

function googlemaps_add_admin_page() {
global $googlemaps_userlevel;
add_management_page('GoogleMaps Parameters', __('Google Maps',googlemaps), $googlemaps_userlevel, 'googlemaps', 'googlemaps_admin_page');
}


if (function_exists('add_action')) {
	add_action('admin_menu', 'googlemaps_add_admin_page');
}

if (isset($_POST['googlemaps_options']))
    add_action('init', 'googlemaps_options');

add_filter('the_content', 'googlemaps_parse');
add_filter('plugin_action_links', 'add_Googlemaps_link', 10, 2);
?>