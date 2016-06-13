<?php 
function wpts_pro_edit_custom_box(){
	global $post;
	echo '<input type="hidden" name="enablewpts_noncename" id="enablewpts_noncename" value="' . wp_create_nonce( plugin_basename(__FILE__) ) . '" />'; 	?>
	<?php
			$enablewpts = get_post_meta($post->ID,'enablewpts',true);
			if($enablewpts=="1"){
				$checked = ' checked="checked" ';
			}else{
				$checked = '';
			}
	?>
		<p><input type="checkbox" id="enablewpts" name="enablewpts" value="1" <?php echo $checked;?> />&nbsp;<label for="enablewpts"><strong>Enable WP Post Tabs PRO Feature</strong></label></p>
	<?php
}
function wpts_pro_add_custom_box() {
	global $wpts,$default_tab_settings;

	foreach($default_tab_settings as $key=>$value){
		if(!isset($wpts[$key])) $wpts[$key]='';
	}
	
	if( function_exists( 'add_meta_box' ) ) {
		$post_types=get_post_types();
		if(isset($post_types)){
			foreach($post_types as $post_type) {
				if($post_type=='post' and $wpts['posts']=='0')
					add_meta_box( 'wpts_box1', __( 'Post Tabs' ), 'wpts_pro_edit_custom_box', 'post', 'side','high' );
				elseif ($post_type=='page' and $wpts['pages']=='0')
					add_meta_box( 'wpts_box2', __( 'Page Tabs' ), 'wpts_pro_edit_custom_box', 'page', 'advanced' );
				else 
					if($post_type!='attachment' and $post_type!='revision' and $post_type!='nav_menu_item' and $wpts['posts']=='0')
						add_meta_box( 'wpts_box1', __( 'Post Tabs' ), 'wpts_pro_edit_custom_box', $post_type, 'side','high' );
			}
		}
	}
}
/* Use the admin_menu action to define the custom boxes */
add_action('admin_menu', 'wpts_pro_add_custom_box');

function wpts_pro_savepost(){
	global $post;
	if(isset($post))$post_id = $post->ID;
	else $post_id = '';
	// verify this came from the our screen and with proper authorization,
	  // because save_post can be triggered at other times
	if(isset($_POST['enablewpts_noncename'])){	  
		if ( !wp_verify_nonce( $_POST['enablewpts_noncename'], plugin_basename(__FILE__) )) {
			return $post_id;
		}	
	}
	else{
		return $post_id;		
	}
	  // verify if this is an auto save routine. If it is our form has not been submitted, so we dont want
	  // to do anything
	  if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
		return $post_id;
	  // Check permissions
	  if ( 'page' == $_POST['post_type'] ) {
		if ( !current_user_can( 'edit_page', $post_id ) )
		  return $post_id;
	  } else {
		if ( !current_user_can( 'edit_post', $post_id ) )
		  return $post_id;
	  }
	  // OK, we're authenticated: we need to find and save the data
	$data =  ($_POST['enablewpts'] == "1") ? "1" : "0";
	update_post_meta($post_id, 'enablewpts', $data);
	return $data;
}
add_action('save_post', 'wpts_pro_savepost');

//Code to add settings page link to the main plugins page on admin
function wpts_pro_admin_url( $query = array() ) {
	global $plugin_page;

	if ( ! isset( $query['page'] ) )
		$query['page'] = $plugin_page;

	$path = 'admin.php';

	if ( $query = build_query( $query ) )
		$path .= '?' . $query;

	$url = admin_url( $path );

	return esc_url_raw( $url );
}

add_filter( 'plugin_action_links', 'wpts_pro_plugin_action_links', 10, 2 );

function wpts_pro_plugin_action_links( $links, $file ) {
	if ( $file != WPTSPRO_PLUGIN_BASENAME )
		return $links;

	$url = wpts_pro_admin_url( array( 'page' => 'wpts-pro-settings-page' ) );

	$settings_link = '<a href="' . esc_attr( $url ) . '">'
		. esc_html( __( 'Settings') ) . '</a>';

	array_unshift( $links, $settings_link );

	return $links;
}
?>
