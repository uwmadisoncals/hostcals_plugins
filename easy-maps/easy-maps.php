<?php


// Include simplehtmldom
if( ! class_exists( 'simple_html_dom_node' ) ) {
	include_once( 'simple_html_dom.php' );
}

/**
 * Plugin Name: Easy Maps for Wordpress
 * Description: Enhance your posts with Google Maps.  Use the Trippy Easy Maps plugin to easily create more dynamic content.
 * Plugin URI:
 * Version:     1.1.6
 * Author:      Team Trippy
 * Author URI:  http://www.trippy.com
 * License:     GPLv2
 * License URI: ./assets/license.txt
 * Text Domain:
 * Domain Path: /languages
 * Network:     false
 */


ob_start();

add_filter( 'mce_css', 'trippy_add_custom_admin_css' );

function trippy_add_custom_admin_css( $mce_css ) {
	
	// Add Admin CSS to Admin Page
	wp_enqueue_style( 'admin_css', plugins_url('css/admin-style.css',  __FILE__));
	
	// Add Admin CSS to TinyMCE Editor
	if ( !empty( $mce_css ) )
		$mce_css .= ',';
	$mce_css .= plugins_url( 'css/admin-style.css', __FILE__ );
	return $mce_css;
}

add_action ( 'admin_head', 'trippy_add_toolbar_button' );
function trippy_add_toolbar_button() {
	global $typenow;
	
	// only on Post Type: post and page
	if (! in_array ( $typenow, array (
			'post',
			'page' 
	) ))
		return;

	wp_enqueue_style( 'font-awesome', '//netdna.bootstrapcdn.com/font-awesome/4.0.1/css/font-awesome.css', null, '4.0.1' );
	
	add_filter ( 'mce_external_plugins', 'trippy_add_tinymce_plugin' );
	// Add to line 1 form WP TinyMCE
	add_filter ( 'mce_buttons', 'trippy_add_tinymce_button' );
}

// inlcude the js for tinymce
function trippy_add_tinymce_plugin($plugin_array) {
	wp_enqueue_script ( 'jquery' );
	wp_enqueue_script('jquery-ui-autocomplete', '', array('jquery-ui-widget', 'jquery-ui-position'), '1.8.6');
	
	
	
	$plugin_array ['trippy_autocomplete'] = plugins_url ( '/plugin.js', __FILE__ );
	// Print all plugin js path
	// var_dump( $plugin_array );
	return $plugin_array;
}

// Add the button key for address via JS
function trippy_add_tinymce_button($buttons) {
	array_push ( $buttons, 'trippy_autocomplete_button_key' );
	// Print all buttons
	// var_dump( $buttons );
	
	return $buttons;
}

add_action( 'add_meta_boxes', 'trippy_map_panel_add' );
function trippy_map_panel_add()
{
    add_meta_box( 'trippy-map-preview-container', 'Easy Maps Map Preview', 'trippy_render_map_panel', 'post', 'normal', 'high' );
}

function trippy_render_map_panel()
{
	global $post;
	
	$contentBody = $post->post_content;
	
	$currentLayoutMode = getTrippyMapLayoutOption();
	
	$selectedLeft = "";
	$selectedRight = "";
	$selectedBottom = "";
	if ($currentLayoutMode == "left") {
		$selectedLeft = " checked=YES ";
	}
	else if ($currentLayoutMode == "bottom") {
		$selectedBottom = " checked=YES ";
	} else  {
		$selectedRight = " checked=YES ";
	}
	
	
	echo "<div class=\"trippy-admin-panel-map-container\">";
	echo trippy_render_map($contentBody, true);
	echo "</div>";

	$hasPlaces = trippy_content_has_places($contentBody);
	if (!$hasPlaces) {
		echo "<div class=\"trippy-map-panel-preview-instructions\">Type \"@place-name\" in your post to map a town, attraction, hotel, or restaurant. (example @Eiffel Tower)</div>";
	}
	
	echo "<div class=\"trippy-admin-panel-position-control\"><p><strong>Display Map on published post:</strong></p>";
	echo "<input type=\"radio\" name=\"trippy-map-position-radio\" value=\"left\" ". $selectedLeft . "><img width=\"25\" alt=\"float left\" src=\"" . plugins_url('img/float-left-icon.gif',  __FILE__) . "\">left ";
	echo "<input type=\"radio\" name=\"trippy-map-position-radio\" value=\"right\" ". $selectedRight . "><img width=\"25\" alt=\"float right\"src=\"" . plugins_url('img/float-right-icon.gif',  __FILE__) . "\">right ";
	echo "<input type=\"radio\" name=\"trippy-map-position-radio\" value=\"bottom\" ". $selectedBottom . "><img width=\"25\" alt=\"bottom\"src=\"" . plugins_url('img/bottom-icon.gif',  __FILE__) . "\">bottom ";
	echo "</div>";
	
}

function trippy_content_has_places($contentBody) {
	$html = str_get_html($contentBody);
	
	if ($html) {
	foreach($html->find('.trippy-place-element') as $element) {
		return true;
		}
	}
	return false;	
}


function trippy_render_map($contentBody, $isAdminMode) {
 	global $post;
	
// 	$postID = $post->ID;
	
	
 	$currentLayoutMode = getTrippyMapLayoutOption() ? getTrippyMapLayoutOption() : "right" ; 
 	
	
	wp_enqueue_style( 'admin_css', plugins_url('css/trippy-map-style.css',  __FILE__));
	
	
	wp_register_style( 'custom-style', plugins_url( '/css/trippy-map-style.css', __FILE__ ), array(), '20140619', 'all' );
	
	// For either a plugin or a theme, you can then enqueue the style:
	wp_enqueue_style( 'custom-style' );
	
	global $content_width;
	
	$easymaps_content_width = $content_width;
	
	$html = str_get_html($contentBody);
	
	if ( ! isset( $easymaps_content_width ) || $easymaps_content_width == 0) {
		$easymaps_content_width = 640;
	}
	
	if ($easymaps_content_width > 640) {
		$easymaps_content_width = 640;
	}
	
	
	if ($currentLayoutMode != "bottom") {
		$easymaps_content_width = round($easymaps_content_width/2);
	}

	if ($easymaps_content_width < 250) {
		$easymaps_content_width = 250;
	}
	
	$mapWidth = $isAdminMode ? 250 : round($easymaps_content_width);
	
	if (!$mapWidth || $mapWidth == 0) {
		$mapWidth = 640;
	}
	
	if ($mapWidth < 250) {
		$mapWidth = 250;
	}
	
	$mapWidthMinusBorders = $mapWidth - 10;
	
	$trippyStaticMapUrl = "http://maps.googleapis.com/maps/api/staticmap?key=AIzaSyBzXWNRqto9a4mUXUtCdwt3KLXNsSE9l_0&sensor=false&size=". $mapWidthMinusBorders . "x250";
	$label = 0;
	
	$trippy_base_url = "http://www.trippy.com";
	
	$trippyMapDetailUrl = $trippy_base_url . "/places/map?utm_campaign=EASY_MAPS&utm_source=" . $_SERVER['SERVER_NAME']. "&utm_medium=map&placeIds=";
	
	
	$trippyTrackingUrl = $trippy_base_url . "/trwpdot.gif?perm=" . urlencode(get_permalink()) . "&ts=" . get_the_time('U') . "&title=" . urlencode(get_the_title()) ."&placeIds=";
	
	
	$trippyPlacesList = "<div class=\"trippy-map-list-container trippy-map-list-container-".$currentLayoutMode."\"><ol class=\"trippy-map-list-ol ". ($isAdminMode ?  "trippy-admin-display-none " : "")  . "\">";
	

	if ($html) {
	foreach($html->find('.trippy-place-element') as $element) {
		$trippyStaticMapUrl .= "&markers=color:red%7Clabel:" . strtoupper(chr(($label % 26) + 97))  . "%7C" . $element->{'data-coords'};

		$trippyMapDetailUrl .= $element->{'data-trippy-place-id'} . ",";
		$trippyTrackingUrl .= $element->{'data-trippy-place-id'} . ",";
		
		$label++;
	
		$trippyPlacesList .= "<li><div class=\"trippy-list-item-div\"><a href=\"" . $trippy_base_url . "/place/" .$element->{'data-trippy-place-id'} ."?utm_campaign=EASY_MAPS&utm_source=" . $_SERVER['SERVER_NAME']. "&utm_medium=list\" target=\"_blank\">" . $element->{'data-trippy-name'} . "</a>";
		
		if ($element->{'data-trippy-description'} && $element->{'data-trippy-description'} != "null") {
			$trippyPlacesList .= "<br/><span class=\"trippy-list-item-description\">" . $element->{'data-trippy-description'}. "</span>" ;
		}
		
		$trippyPlacesList .= "<div class=\"trippy-list-item-more-info\">";
		
		
		if ($element->{'data-trippy-address'} && $element->{'data-trippy-address'} != "null") {
			$trippyPlacesList .= "<br/>" . $element->{'data-trippy-address'} ;
		}	
		if ($element->{'data-trippy-city-country'} && $element->{'data-trippy-city-country'} != "null") {
			$trippyPlacesList .= "<br/>" . $element->{'data-trippy-city-country'};
		}
		if ($element->{'data-trippy-phone'} && $element->{'data-trippy-phone'} != "null") {
			$trippyPlacesList .= "<br/>" . $element->{'data-trippy-phone'} ;
		}	
		if ($element->{'data-trippy-website'} && $element->{'data-trippy-website'} != "null") {
			$trippyPlacesList .= "<br/><a href=\"" . $element->{'data-trippy-website'} . "\" target=\"_blank\">" . $element->{'data-trippy-website'} ."</a>";
		}	
		
		
				
		$trippyPlacesList .= "</div></div></li>"; 

	}

	
	
	
	$trippyPlacesList .= "</ol></div>";

	if ($isAdminMode) {
		$trippyPlacesList .= "<div>" . $label . " mapped places | <span id=\"trippy-admin-show-map-details\">details</span></div>";
	}
	
	
	
	
	if ($label > 0) {
		$pluginUrl = $trippy_base_url . "/tools/wordpress-easy-maps-plugin";
		if ($currentLayoutMode != "bottom") {
		  $inline_style = "width:".$easymaps_content_width."px";
		} else {
			$inline_style = "";
		}
		
		return "<div style=\"".$inline_style."\" class=\"trippy-map-panel-preview-container trippy-map-panel-preview-container-" . $currentLayoutMode . "\"><div class=\"trippy-map-panel-title\">Mentioned in this post</div><a href=\"". $trippyMapDetailUrl . "\" target=\"_blank\"><img src=\"" . $trippyStaticMapUrl . "\" class=\"trippy-map-panel-preview-map-img\"><!--</a>-->". $trippyPlacesList. " <div class=\"trippy-map-panel-preview-container-header\">" . ($isAdminMode || is_preview() ? "" : "<img src=\"" . $trippyTrackingUrl.  "\" width=\"1\" height=\"1\" style=\"float:left;\">"). "<span class=\"trippy-map-panel-preview-container-header-contents\"><a href=\"http://www.trippy.com\" target=\"_blank\">powered by <strong>trippy</strong></a> &nbsp;|&nbsp; <a href=\"". $pluginUrl . "\" target=\"_blank\">get the wordpress map plugin</a></span></div></div>";	
	} else {
		return "";
	}
	} else {
		return "";
	}
}


add_action( 'save_post', 'trippy_map_layout_option_save' );
function trippy_map_layout_option_save( $post_id )
{
	 
	// if our current user can't edit this post, bail
	if( !current_user_can( 'edit_post' ) ) return;
	 
	 
	if( isset( $_POST['trippy-map-position-radio'] ) )
		update_post_meta( $post_id, 'trippy-map-layout-option', esc_attr( $_POST['trippy-map-position-radio'] ) );
	 
}




function my_plugin_function_callback() {

	$contentBody = stripcslashes($_POST['contentBody']);
	
	echo trippy_render_map($contentBody, true);
	
	die();
	
}

add_action('wp_ajax_my_plugin_function', 'my_plugin_function_callback');

//Add Map to Live Post
add_filter( "the_content", "trippy_add_map_content_after_post" );

function getTrippyMapLayoutOption() {
	global $post;
	$currentLayoutMode = get_post_meta( $post->ID, 'trippy-map-layout-option', true);
	
	if (!$currentLayoutMode) {
		$currentLayoutMode = "right";
	}
	return $currentLayoutMode;
}

function trippy_add_map_content_after_post($content){
	
	wp_enqueue_style( 'admin_css', plugins_url('css/trippy-map-style.css',  __FILE__));
	
	
	wp_register_style( 'custom-style', plugins_url( '/css/trippy-map-style.css', __FILE__ ), array(), '20140619', 'all' );
	
	$currentLayoutMode = getTrippyMapLayoutOption();
	
	$processContent = $content;
	if (is_single()) {
		$mapDiv = trippy_render_map($processContent, false);

		if ($currentLayoutMode == "bottom") {
			$content = $content .  $mapDiv;
		} else {
			$content = $mapDiv. $content;
		}
	}
	return $content;
}

