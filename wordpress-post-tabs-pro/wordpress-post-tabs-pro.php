<?php
/*
Plugin Name: WordPress Post Tabs PRO
Plugin URI: http://tabbervilla.com/wordpress-post-tabs-pro/
Description: Add fully customizable beautiful tabs to your WordPress Pages/Posts/Sidebar Widgets/Template files using Shortcode/Widget/Template tags. Lots of amazing features! Watch Live Demo at <a href="http://tabbervilla.com/wordpress-post-tabs-pro/">Plugin Page</a>.
Version: 2.0
Author: TabberVilla
Author URI: http://tabbervilla.com/
WordPress version supported: 3.5 and above
*/

/*  Copyright 2012-2014  TabberVilla  (email : tedeshpa@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
define( 'WPTS_PRO_ACTIVE', '1' );
if ( ! defined( 'WPTSPRO_PLUGIN_BASENAME' ) )
	define( 'WPTSPRO_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define('WPTSPRO_VER',"2.0",false);
define('WPTSPRO_URLPATH', trailingslashit( WP_PLUGIN_URL . '/' . plugin_basename( dirname(__FILE__) ) ) );
include_once (dirname (__FILE__) . '/core/tinymce/tinymce.php');
global $wpts,$default_tab_settings;
$wpts = get_option('wpts_options');
$default_tab_settings=array('speed' => '1',
	                   'transition' => '',
			   'pages' => '1',
			   'posts' => '1',
			   'stylesheet' => 'default',
			   'reload' => '0',
			   'tab_code' => 'wptab',
			   'tab_end_code' => 'end_wptabset',
			   'support' => '1', 
			   'fade' => '0', 
			   'jquerynoload' => '0',
			   'showtitle' =>'0',
			   'linktarget' =>'0',
			   'nav'=>'0',
			   'next_text'=>'Next &#187;',
			   'prev_text'=>'&#171; Prev',
			   'enable_everywhere'=>'0',
			   'disable_fouc'=>'0',
			   'css'=>'',
			   'onhover'=>'0',
			   'tabtop'=>'0',
			   'taburl'=>'0',
			   'location'=>'top',
			   'support'=>'1',
			   'nested'=>'0',
			   'ext_link_icon' => 0,
			   'showurl'=>'0'
			   );
function wpts_pro_plugin_url( $path = '' ) {
	return plugins_url( $path, __FILE__ );
}

//on activation, your WordPress Post Tabs options will be populated. Here a single option is used which is actually an array of multiple options
function activate_wpts_pro() {
	global $wpts,$default_tab_settings;
	foreach($default_tab_settings as $key=>$value) {
	  if(!isset($wpts[$key])) {
		 $wpts[$key] = $value;
	  }
	}	  
	update_option('wpts_options',$wpts);

	global $wpts_style,$default_tab_styles;
	$wpts_style_curr=$wpts_style;
	foreach($default_tab_styles as $key=>$value) {
		if(!isset($wpts_style_curr[$key])) {
			$wpts_style_curr[$key] = $value;
		}
		else{
			if(is_array($wpts_style_curr[$key])){
				foreach($wpts_style_curr[$key] as $key1=>$value1){
					if(!isset($wpts_style_curr[$key][$key1])) {
						$wpts_style_curr[$key][$key1]=$value1;
					}
				}
			}
		}
	}
	delete_option('wpts_style_options');	  
	update_option('wpts_style_options',$wpts_style_curr);
}
register_activation_hook( __FILE__, 'activate_wpts_pro' );

function wpts_pro_custom_css() {
	global $wpts;
	$css=$wpts['css'];
	$line_breaks = array("\r\n", "\n", "\r");
	$css = str_replace($line_breaks, "", $css);
	if($css and !empty($css)){
		if( ( is_admin() and isset($_GET['page']) and 'wpts-pro-settings-page' == $_GET['page']) or !is_admin() ){	?>
			<script type="text/javascript">jQuery(document).ready(function() { jQuery("head").append("<style type=\"text/css\"><?php echo $css;?></style>"); }) </script>
<?php 	}
	}
}
add_action('wp_footer', 'wpts_pro_custom_css');
add_action('admin_footer', 'wpts_pro_custom_css');

function wpts_pro_wp_init() {
	global $post, $wpts_count,$wpts_tab_count,$wpts_content,$wpts_prev_post,$wpts;
	$wpts_count=0;
	$wpts_tab_count=array();
	$wpts_prev_post='';
	$wpts_content=array();
	$wpts_skin=array(); 
	if(!isset($wpts['jquerynoload']) or $wpts['jquerynoload']!='1') {
		wp_enqueue_script('jquery');
	}
	
}
add_action( 'wp', 'wpts_pro_wp_init' );
//New Custom Post Type
if( !post_type_exists('wpts') ){
	add_action( 'init', 'wpts_pro_post_type', 11 );
	function wpts_pro_post_type() {
			$labels = array(
			'name' => _x('Custom Tabs', 'post type general name'),
			'singular_name' => _x('Custom Tab', 'post type singular name'),
			'add_new' => _x('Add New', 'wpts'),
			'add_new_item' => __('Add New Custom Tab'),
			'edit_item' => __('Edit Custom Tab'),
			'new_item' => __('New Custom Tab'),
			'all_items' => __('All Custom Tabs'),
			'view_item' => __('View Custom Tab'),
			'search_items' => __('Search Custom Tabs'),
			'not_found' =>  __('No Custom tabs found'),
			'not_found_in_trash' => __('No Custom tabs found in Trash'), 
			'parent_item_colon' => '',
			'menu_name' => 'Custom Tabs'
			);
			$args = array(
			'labels' => $labels,
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true, 
			'show_in_menu' => true, 
			'query_var' => true,
			'rewrite' => true,
			'capability_type' => 'page',
			'has_archive' => true, 
			'hierarchical' => true,
			'menu_position' => null,
			'supports' => array('title','editor','thumbnail','custom-fields','page-attributes')
			); 
			register_post_type('custom_tab',$args);
	}

	//add filter to ensure the text SliderVilla, or slidervilla, is displayed when user updates a slidervilla 
	add_filter('post_updated_messages', 'wpts_updated_messages');
	function wpts_updated_messages( $messages ) {
	  global $post, $post_ID;

	  $messages['wpts'] = array(
		0 => '', // Unused. Messages start at index 1.
		1 => sprintf( __('Custom Tab updated. <a href="%s">View Custom tab</a>'), esc_url( get_permalink($post_ID) ) ),
		2 => __('Custom field updated.'),
		3 => __('Custom field deleted.'),
		4 => __('Custom Tab updated.'),
		/* translators: %s: date and time of the revision */
		5 => isset($_GET['revision']) ? sprintf( __('Custom Tab restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		6 => sprintf( __('Custom Tab published. <a href="%s">View Custom tab</a>'), esc_url( get_permalink($post_ID) ) ),
		7 => __('Custom Tab saved.'),
		8 => sprintf( __('Custom Tab submitted. <a target="_blank" href="%s">Preview Custom tab</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
		9 => sprintf( __('Custom Tabs scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Custom tab</a>'),
		  // translators: Publish box date format, see http://php.net/date
		  date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
		10 => sprintf( __('Custom Tab draft updated. <a target="_blank" href="%s">Preview Custom tab</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
	  );

	  return $messages;
	}
} 

//if custom_post is true
// Load the update-notification class
add_action('init', 'wptspro_update_notification');
function wptspro_update_notification()
{
    require_once (dirname (__FILE__) . '/core/upgrade.php');
    $wptspro_upgrade_remote_path = 'http://tabbervilla.com/notifications/wptspro.php';
    new wptspro_update_class ( WPTSPRO_VER, $wptspro_upgrade_remote_path, WPTSPRO_PLUGIN_BASENAME );
}
require_once (dirname (__FILE__) . '/core/admin.php');
require_once (dirname (__FILE__) . '/core/settings.php');
require_once (dirname (__FILE__) . '/core/shortcodes.php');
require_once (dirname (__FILE__) . '/core/widgets.php');
require_once (dirname (__FILE__) . '/core/functions.php');
wpts_include_from_folder( dirname (__FILE__) . '/addons/', $ext='php');
?>
