<?php
// function for adding settings page to wp-admin
function wpts_pro_settings() {
    // Add a new submenu under Options:
    add_menu_page( 'WP Post Tabs PRO', 'Tabs PRO', 'manage_options','wpts-pro-settings-page', 'wpts_pro_settings_function', wpts_pro_plugin_url( 'core/images/tab.png' ) );
    add_submenu_page('wpts-pro-settings-page', 'Tabs PRO', 'Tabs PRO', 'manage_options', 'wpts-pro-settings-page', 'wpts_pro_settings_function');
    add_submenu_page('wpts-pro-settings-page', 'Skins Styler', 'Skins Styler', 'manage_options', 'post-tabs-pro-skins-styler', 'wpts_pro_skins_styler_page');
	do_action('wptspro_settings_menu','wpts-pro-settings-page');	
}

//Skin Styler settings 
require_once (dirname (__FILE__) . '/skins_styler.php');

function wpts_pro_admin_head() {
}
add_action('admin_head', 'wpts_pro_admin_head');

function wpts_pro_admin_scripts() {
  if ( is_admin() ){ // admin actions
  // Settings page only
	if ( isset($_GET['page']) && 'wpts-pro-settings-page' == $_GET['page'] ) {
	global $wpts;
	wp_enqueue_script('jquery', false, false, false, false);
	wp_enqueue_script( 'jquery-form' );
	wp_enqueue_script( 'wpts_admin_js', wpts_pro_plugin_url( 'core/js/admin.js' ),	array('jquery'), WPTSPRO_VER, false);
	wp_enqueue_style( 'wpts_admin_css', wpts_pro_plugin_url( 'core/css/admin.css' ),
		false, WPTSPRO_VER, 'all');
	}
  }
}

add_action( 'admin_init', 'wpts_pro_admin_scripts' );
//add_action('admin_init','download_csv');
function download_csv()
{ 
	//Export Settings
	if(isset($_POST['export'])){ 
		if ($_POST['export']=='Export') { 
			@ob_end_clean();			
			// required for IE, otherwise Content-Disposition may be ignored
			if(ini_get('zlib.output_compression'))
			ini_set('zlib.output_compression', 'Off');
			
			header('Content-Type: ' . "text/x-csv");
			header('Content-Disposition: attachment; filename="wordpress-post-tabs-pro-settings-set.csv"');
			header("Content-Transfer-Encoding: binary");
			header('Accept-Ranges: bytes');

			/* The three lines below basically make the
			download non-cacheable */
			header("Cache-control: private");
			header('Pragma: private');
			header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

			$exportTXT='';$i=0;
			$tab_curr= get_option('wpts_options');
			foreach($tab_curr as $key=>$value){
				if($i>0) $exportTXT.="\n";
				if(!is_array($value)){
					$exportTXT.=$key.",".$value;
				}
				else {
					$exportTXT.=$key.',';
					$j=0;
					if($value) {
						foreach($value as $v){
							if($j>0) $exportTXT.="|";
							$exportTXT.=$v;
							$j++;
						}
					}
				}
				$i++;
			}
			$exportTXT.="\n";
			$exportTXT.="Tab_name,wordpress-post-tabs-pro";
			print($exportTXT); 
			exit();
		}
	}	
}
add_action('load-toplevel_page_wpts-pro-settings-page','download_csv');
// This function displays the page content for the Iframe Embed For YouTube Options submenu
function wpts_pro_settings_function() {
	
	//Import Settings
	if (isset ($_POST['import'])) { 
		if ($_POST['import']=='Import') {
			global $wpdb;
			$imported_settings_message='';
			$csv_mimetypes = array('text/csv','text/x-csv','text/plain','application/csv','text/comma-separated-values','application/excel','application/vnd.ms-excel','application/vnd.msexcel','text/anytext','application/octet-stream','application/txt');
			if ($_FILES['settings_file']['error'] == UPLOAD_ERR_OK && is_uploaded_file($_FILES['settings_file']['tmp_name']) && in_array($_FILES['settings_file']['type'], $csv_mimetypes) ) { 
				$imported_settings=file_get_contents($_FILES['settings_file']['tmp_name']); 
				$settings_arr=explode("\n",$imported_settings);
				$tab_settings=array();
				foreach($settings_arr as $settings_field){
					$s=explode(',',$settings_field);
					$inner=explode('|',$s[1]);
					if(count($inner)>1)	$tab_settings[$s[0]]=$inner;
					else $tab_settings[$s[0]]=$s[1];
				}
				$options='wpts_options';
			
				if( $tab_settings['Tab_name'] == 'wordpress-post-tabs-pro' )	{
					update_option($options,$tab_settings);
					$new_settings_msg='<div id="message" class="updated fade" style="clear:left;"><h3>'.__('Settings imported successfully ','wordpress-post-tabs-pro').'</h3></div>';
					$imported_settings_message='<div style="clear:left;color:#006E2E;"><h3>'.__('Settings imported successfully ','wordpress-post-tabs-pro').'</h3></div>';
				}
				else {
					$new_settings_msg=$imported_settings_message='<div id="message" class="error fade" style="clear:left;"><h3>'.__('Settings imported do not match to Wordpress Post Tabs Pro Settings. Please check the file.','wordpress-post-tabs-pro').'</h3></div>';
					$imported_settings_message='<div style="clear:left;color:#ff0000;"><h3>'.__('Settings imported do not match to Wordpress Post Tabs Pro Settings. Please check the file.','wordpress-post-tabs-pro').'</h3></div>';
				}
			}
			else{
				$new_settings_msg=$imported_settings_message='<div id="message" class="error fade" style="clear:left;"><h3>'.__('Error in File, Settings not imported. Please check the file being imported. ','wordpress-post-tabs-pro').'</h3></div>';
				$imported_settings_message='<div style="clear:left;color:#ff0000;"><h3>'.__('Error in File, Settings not imported. Please check the file being imported. ','wordpress-post-tabs-pro').'</h3></div>';
			}
		}
	}
?>
<div class="wrap">

<div style="width:65%;margin-top: 15px;">
	<div style="float:right;"><strong style="color:#ccc;font-size:9px;">powered by</strong> <a style="margin-left:5px;" href="http://tabbervilla.com/" target="_blank" rel="nofollow"><img src="<?php echo wpts_pro_plugin_url('core/images/tabbervilla.png');?>" width="120"/></a> </div>
	<h2 style="font-size:26px;">WordPress Post Tabs PRO</h2>
</div>

<form  method="post" action="options.php" id="wpts_form">
<?php
settings_fields('wpts-group');
$wpts = get_option('wpts_options');
?>

<div id="poststuff" class="metabox-holder has-right-sidebar"> 

<?php //if($wpts['support'] == "1") 
		echo '<div  class="left_panel" id="wpts_form">';  
 //else echo '<div id="wpts_form">';  
?>

<div class="postbox">
<h3 class="hndle"><?php _e('Basic Settings','wptspro'); ?></h2>

<div style="">
<table class="form-table">

<tr valign="top" class="row">
<th scope="row"><label for="wpts_options[stylesheet]"><?php _e('Skin','wptspro'); ?></label></th> 
<td><select name="wpts_options[stylesheet]" id="wpts_stylesheet" >

<?php 
$directory = WP_PLUGIN_DIR.'/'.basename(WPTSPRO_PLUGIN_BASENAME,'.php').'/skins';
if ($handle = opendir($directory)) {
    while (false !== ($file = readdir($handle))) { 
     if($file != '.' and $file != '..') { ?>
      <option value="<?php echo $file;?>" <?php if ($wpts['stylesheet'] == $file){ echo "selected";}?> ><?php echo str_replace("_"," ",$file);?></option>
 <?php  } }
    closedir($handle);
}
?>
</select>
</td>
</tr>

<tr valign="top" class="row even"> 
<th scope="row"><label for="wpts_options[location]"><?php _e('Tabs Location','wptspro'); ?></label></th> 
<td>
<select name="wpts_options[location]" >
<option value="top" <?php if ($wpts['location'] == "top"){ echo "selected";}?> ><?php _e('Top','wptspro'); ?></option>
<option value="bottom" <?php if ($wpts['location'] == "bottom"){ echo "selected";}?> ><?php _e('Bottom','wptspro'); ?></option>
<option value="left" <?php if ($wpts['location'] == "left"){ echo "selected";}?> ><?php _e('Left','wptspro'); ?></option>
<option value="right" <?php if ($wpts['location'] == "right"){ echo "selected";}?> ><?php _e('Right','wptspro'); ?></option>
</select>
</td> 
</tr> 

<tr valign="top" class="row">  
<th scope="row"><label for="wpts_options[nested]"><?php _e('Nest Levels','wptspro'); ?></label></th> 
<td>
<select name="wpts_options[nested]" >
<option value="0" <?php if ($wpts['nested'] == "0"){ echo "selected";}?> ><?php _e('0','wptspro'); ?></option>
<option value="1" <?php if ($wpts['nested'] == "1"){ echo "selected";}?> ><?php _e('1','wptspro'); ?></option>
<option value="2" <?php if ($wpts['nested'] == "2"){ echo "selected";}?> ><?php _e('2','wptspro'); ?></option>
<option value="3" <?php if ($wpts['nested'] == "3"){ echo "selected";}?> ><?php _e('3','wptspro'); ?></option>
<option value="4" <?php if ($wpts['nested'] == "4"){ echo "selected";}?> ><?php _e('4','wptspro'); ?></option>
<option value="5" <?php if ($wpts['nested'] == "5"){ echo "selected";}?> ><?php _e('5','wptspro'); ?></option>
</select>
</td> 
</tr> 

<tr valign="top" class="row even"> 
<th scope="row"><label for="wpts_options[onhover]"><?php _e('Transition on Hover','wptspro'); ?></label></th> 
<td><input name="wpts_options[onhover]" type="checkbox" id="wpts_options_onhover" value="1"  <?php checked("1", $wpts['onhover']); ?> />
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('By default, tab transition is On Click.','wptspro'); ?>
	</div>
</span>
</td> 
</tr> 

<tr valign="top" class="row"> 
<th scope="row"><label for="wpts_options[fade]"><?php _e('Effect','wptspro'); ?></label></th> 
<td>
<select name="wpts_options[fade]" >
<option value="0" <?php if ($wpts['fade'] == "0"){ echo "selected";}?> ><?php _e('No Effect','wptspro'); ?></option>
<option value="1" <?php if ($wpts['fade'] == "1"){ echo "selected";}?> ><?php _e('Fade','wptspro'); ?></option>
<option value="2" <?php if ($wpts['fade'] == "2"){ echo "selected";}?> ><?php _e('Slide Down','wptspro'); ?></option>
<option value="3" <?php if ($wpts['fade'] == "3"){ echo "selected";}?> ><?php _e('Specify Effect thru Shortcode','wptspro'); ?></option>
</select>
</td> 
</tr> 

<tr valign="top" class="row even"> 
<th scope="row"><label for="wpts_options[enable_everywhere]"><?php _e('Enable tabs Sitewide','wptspro'); ?></label></th> 
<td><input name="wpts_options[enable_everywhere]" type="checkbox" id="wpts_options_enable_everywhere" value="1" <?php checked("1", $wpts['enable_everywhere']); ?> />
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('Enable tabs on archives, index and all other templates','wptspro'); ?>
	</div>
</span>
</td> 
</tr> 

<tr valign="top" class="row"> 
<th scope="row"><label for="wpts_options[nav]"><?php _e('Navigation','wptspro'); ?></label></th> 
<td><input name="wpts_options[nav]" type="checkbox" id="wpts_options_nav" value="1" <?php checked("1", $wpts['nav']); ?> />
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('Enable Navigation Links','wptspro'); ?>
	</div>
</span>
</td> 
</tr> 

<tr valign="top" class="row even">
<th scope="row"><label for="wpts_options[next_text]"><?php _e('\'Next\' navigation text','wptspro'); ?></label></th>
<td><input type="text" name="wpts_options[next_text]" id="wpts_options_next_text" class="regular-text code" value="<?php echo $wpts['next_text']; ?>" />
</td>
</tr>

<tr valign="top" class="row">
<th scope="row"><label for="wpts_options[prev_text]"><?php _e('\'Prev\' navigation text','wptspro'); ?></label></th>
<td><input type="text" name="wpts_options[prev_text]" id="wpts_options_prev_text" class="regular-text code" value="<?php echo $wpts['prev_text']; ?>" /></td>
</tr> 

<tr valign="top" class="row even"> 
<th scope="row"><label for="wpts_options[ext_link_icon]"><?php _e('External Link Icon','wptspro'); ?></label></th> 
<td><input name="wpts_options[ext_link_icon]" type="checkbox" id="wpts_options_ext_link_icon" value="1" <?php checked("1", $wpts['ext_link_icon']); ?> />
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('Enable External Link Icon','wptspro'); ?>
	</div>
</span>
</td> 
</tr>

<tr valign="top" class="row"> 
<th scope="row"><label for="wpts_options[linktarget]"><?php _e('Open tab links in New window','wptspro'); ?> </label></th> 
<td><input name="wpts_options[linktarget]" type="checkbox" id="wpts_options_linktarget" value="1" <?php checked("1", $wpts['linktarget']); ?> />
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('This checkbox should be checked when you are linking the tab to an External Link and would want to open that link in a new window.','wptspro'); ?>
	</div>
</span>
</td> 
</tr>

<tr valign="top" class="row even"> 
<th scope="row"><label for="wpts_options[taburl]"><?php _e('Linkable tabs','wptspro'); ?></label></th> 
<td><input name="wpts_options[taburl]" type="checkbox" id="wpts_options_taburl" value="1" <?php checked("1", $wpts['taburl']); ?> />
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('You can directly link to a particular tab on the page by putting a tick in this checkbox.','wptspro'); ?>
	</div>
</span>
</td> 
</tr>

<tr valign="top" class="row"> 
<th scope="row"><label for="wpts_options[showurl]"><?php _e('Show tab # in url','wptspro'); ?></label></th> 
<td><input name="wpts_options[showurl]" type="checkbox" id="wpts_options_showurl" value="1" <?php checked("1", $wpts['showurl']); ?> />
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('In case you want the tabs # i.e. link shown in the page url, you can put a tick in this checkbox.','wptspro'); ?>
	</div>
</span>
</td> 
</tr>

<tr valign="top" class="row even"> 
<th scope="row"><label for="wpts_options[tabtop]"><?php _e('Scroll to tab content','wptspro'); ?></label></th> 
<td><input name="wpts_options[tabtop]" type="checkbox" id="wpts_options_tabtop" value="1"  <?php checked("1", $wpts['tabtop']); ?> />
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('The page will smoothly scroll to the selected tab content directly if this checkbox is ticked.','wptspro'); ?>
	</div>
</span>
</td> 
</tr> 

<tr valign="top" class="row"> 
<th scope="row"><label for="wpts_options[reload]"><?php _e('Reload on click','wptspro'); ?></label></th> 
<td><input name="wpts_options[reload]" type="checkbox" id="wpts_options_reload" value="1"  <?php checked("1", $wpts['reload']); ?> />
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('This may increase your pageviews.','wptspro'); ?>
	</div>
</span>
</td> 
</tr> 

<tr valign="top" class="row even"> 
<th scope="row"><label for="wpts_options[showtitle]"><?php _e('Title Attribute for tab links','wptspro'); ?></label></th> 
<td><input name="wpts_options[showtitle]" type="checkbox" id="wpts_options_showtitle" value="1" <?php checked("1", $wpts['showtitle']); ?> />
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('If you want title attribute to be displayed when the user hovers on Tab Title.','wptspro'); ?>
	</div>
</span>
</td> 
</tr>

<tr valign="top" class="row"> 
<th scope="row"><label for="wpts_options[disable_fouc]"><?php _e('Disable FOUC','wptspro'); ?></label></th> 
<td><input name="wpts_options[disable_fouc]" type="checkbox" id="wpts_options_disable_fouc" value="1" <?php checked("1", $wpts['disable_fouc']); ?> />
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('If you disable FOUC i.e. Flash Of Unstyled Content, tabs may not be displayed on the browser on which Javascript is disabled.','wptspro'); ?>
	</div>
</span>
</td> 
</tr> 

<tr valign="top" class="row even"> 
<th scope="row"><label for="wpts_options[jquerynoload]"><?php _e('Disable \'jquery\'','wptspro'); ?></label></th> 
<td><input name="wpts_options[jquerynoload]" type="checkbox" id="wpts_options_jquerynoload" value="1" <?php checked("1", $wpts['jquerynoload']); ?> />
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('In case jQuery.js is added by hardcoding in active theme or plugin. This will avoid js conflict','wptspro'); ?>
	</div>
</span>
</td> 
</tr> 
</table> 

<p class="submit">
<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
</p>
</div>
</div>


<div class="postbox">
<h3 class="hndle" style="font-size: 18px;"><?php _e('Advance Options','wptspro'); ?></h2>
 
<h3 style="background: #CCC; color: #222;margin: 20px 0;"><?php _e('Disable Plugin Resources','wptspro'); ?> </h3> 

<div style="padding:10px">
<small><?php _e('This will help you avoid loading the plugin files (js,css) on all pages and posts. You would get an option (custom checkbox) on edit post/page panel to individually load resources on selected posts and pages only. If the below checkboxes are not checked, the plugin files will load on every page/post of your wordpress site.','wptspro'); ?></small> 
 
<table class="form-table"> 
 
<tr valign="top"> 
<th scope="row"><label for="wpts_options[posts]"><?php _e('Disable resources on all Posts','wptspro'); ?></label></th> 
<td><input name="wpts_options[posts]" type="checkbox" id="wpts_options_posts" value="0" <?php checked("0", $wpts['posts']); ?> />
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('You would get a custom box on your edit post panel to enable tabs on that particular post.','wptspro'); ?>
	</div>
</span>
</td> 
</tr> 
 
<tr valign="top"> 
<th scope="row"><label for="wpts_options[pages]"><?php _e('Disable resources on all Pages','wptspro'); ?></label></th> 
<td><input name="wpts_options[pages]" type="checkbox" id="wpts_options_pages" value="0" <?php checked("0", $wpts['pages']); ?>  />
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('You would get a custom box on your edit page panel to enable tabs on that particular page.','wptspro'); ?>
	</div>
</span>
</td> 
</tr> 
</table> 
</div> 
<h3 style="background: #CCC; color: #222;margin: 20px 0;"><?php _e('Custom Shortcodes','wptspro'); ?></h3> 
<div style="padding:10px">
<small><?php _e('The default shortcodes are [wptab] for adding a tab and [end_wptabset] to end particular set/group of tabs. Do not use spaces in the custom shortcodes. To check how to insert the tabs in your post/page, please refer the','wptspro'); ?> <a href="http://guides.tabbervilla.com/wordpress-post-tabs-pro/"><?php _e('plugin guide','wptspro'); ?></a>.</small> 
<p style="color:#F04A4F"><?php _e('IMPORTANT: While changing these values to  new values, you would need to check if you have used old shortcode values in any of the posts.','wptspro'); ?></p> 
 
<table class="form-table"> 
 
<tr valign="top"> 
<th scope="row"><label for="wpts_options[tab_code]"><?php _e('Replace [wptab] shortcode with','wptspro'); ?></label></th> 
<td>[<input type="text" name="wpts_options[tab_code]" id="wpts_options_tab_code" value="<?php echo $wpts['tab_code']; ?>" />]<small> &nbsp; &nbsp; <?php _e('(For example, you can enter: mytabs)','wptspro'); ?></small></td> 
</tr> 
<tr valign="top"> 
<th scope="row"><label for="wpts_options[tab_end_code]"><?php _e('Replace [end_wptabset] shortcode with','wptspro'); ?></label></th> 
<td>[<input type="text" name="wpts_options[tab_end_code]" id="wpts_options_tab_end_code" value="<?php echo $wpts['tab_end_code']; ?>" />]<small> &nbsp; &nbsp; <?php _e('(For example, you can enter: end_mytabs)','wptspro'); ?></small></td> 
</tr> 

</table>

<h3 style="background: #CCC; color: #222;margin: 20px 0;"><?php _e('Miscellaneous','wptspro'); ?> </h3> 

<table class="form-table"> 

<tr valign="top">
<th scope="row"><?php _e('Custom Styles','wptspro'); ?></th>
<td><textarea name="wpts_options[css]"  rows="5" cols="32" class="regular-text code"><?php echo $wpts['css']; ?></textarea>
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('custom css styles that you would want to be applied to the tab  elements.','wptspro'); ?>
	</div>
</span>
</td>
</tr>
<?php do_action('wptspro_misc_settings');?>

<tr valign="top">
<th scope="row"><?php _e('Show Promotionals on Admin Page','wptspro'); ?></th>
<td><select name="wpts_options[support]" >
<option value="1" <?php if ($wpts['support'] == "1"){ echo "selected";}?> ><?php _e('Yes','wptspro'); ?></option>
<option value="0" <?php if ($wpts['support'] == "0"){ echo "selected";}?> ><?php _e('No','wptspro'); ?></option>
</select>
</td>
</tr>
 
</table> 

<p class="submit">
<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
</p>
</div>
</div>
</form>	
	
	
<div style="border:1px solid #ccc;padding:10px;background:#fff;margin:0;" id="import">
<?php if (isset ($imported_settings_message))echo $imported_settings_message;?>
	<h3><?php _e('Import Settings Set by uploading a Settings File','wordpress-post-tabs-pro'); ?></h3>
	<form style="margin-right:10px;font-size:14px;" method="post" enctype="multipart/form-data">
		<input type="hidden" name="MAX_FILE_SIZE" value="30000" />
		<input type="file" name="settings_file" id="settings_file" style="font-size:13px;width:50%;padding:0 5px;" />
		<input type="submit" value="Import" name="import"  onclick="return confirmSettingsImport()" title="<?php _e('Import Settings from a file','wordpress-post-tabs-pro'); ?>" class="button-primary" />
	</form>
</div>
<div style="clear:both;"></div>
<div id="saveSettingsResult"></div>
</div>


<div class="right_panel"> 
			<form style="margin-right:10px;font-size:14px;width:100%;" action="" method="post">

<input type="submit" value="Export" name="export" title="<?php _e('Export this Settings Set to a file','wordpress-post-tabs-pro'); ?>" class="tvilla_button" />
<a href="#import" title="<?php _e('Go to Import Settings Form','wordpress-post-tabs-pro'); ?>" class="tvilla_button">Import</a>
<div class="svilla_cl"></div>
</form>
			<div class="postbox"> 
			  <h3 class="hndle"><span><?php _e('About this Plugin:','wptspro'); ?></span></h3> 
			  <div class="inside">
			  
			  
			  <div>
                <ul>
                <li><a href="http://tabbervilla.com/wordpress-post-tabs-pro/" title="<?php _e('WordPress Post Tabs PRO Homepage','wptspro'); ?>" ><?php _e('Plugin Homepage','wptspro'); ?></a></li>
                <li><a href="http://support.tabbervilla.com/" title="<?php _e('Support Forum for WordPress Post Tabs','wptspro'); ?>
" ><?php _e('Support Forum','wptspro'); ?></a></li>
                <li><a href="http://keencodes.com/" title="<?php _e('WordPress Post Tabs Author Page','wptspro'); ?>" ><?php _e('About the Author','wptspro'); ?></a></li>
				<li><a href="http://tabbervilla.com" title="<?php _e('Visit TabberVilla','wptspro'); ?>
" ><?php _e('Plugin Parent Site','wptspro'); ?></a></li>
                </ul> 
			  </div>	
			  
			 
			  <div class="clear"></div>
				
              </div> 
			</div> 

			<div class="clear"></div>
	<?php if($wpts['support'] == "1") { ?>
           <div class="postbox"> 
				<h3 class="hndle"><span></span><?php _e('Recommended WP Sliders','wptspro'); ?></h3>
     		  <div class="inside">
				<div style="margin:10px auto;">
							<a href="http://slidervilla.com" title="Premium WordPress Slider Plugins" target="_blank"><img src="<?php echo wpts_pro_plugin_url('core/images/slidervilla.jpg');?>" alt="Premium WordPress Slider Plugins" width="100%" /></a>
				</div>
				<p><a href="http://slidervilla.com/" title="Recommended WordPress Sliders" target="_blank">SliderVilla slider plugins</a> are feature rich and stylish plugins to embed a nice looking featured content slider in your existing or new theme template. 100% customization options available on WordPress Settings page of the plugin.</p>
						<p><strong>Stylish Sliders, <a href="http://slidervilla.com/blog/testimonials/" target="_blank">Happy Customers</a>!</strong></p>
                        <p><a href="http://slidervilla.com/" title="Recommended WordPress Sliders" target="_blank">For more info visit SliderVilla</a></p>
            </div></div>
	<?php } ?>
</div> <!--end of poststuff -->

<!--**** Script Added For Validations ***** -->
<script tpe="text/javaScript">
	
jQuery(document).ready(function() {
	jQuery('#wpts_form').submit(function() {  
	      jQuery(this).ajaxSubmit({
		beforeSubmit:  function(formData, jqForm, options) { 
			var tab_start_code=jQuery(jqForm).eq(0).find("#wpts_options_tab_code").val();
			if(tab_start_code=='') {
				alert("Please Enter Tab Start Shortcode !"); 
				jQuery(jqForm).eq(0).find("#wpts_options_tab_code").addClass('error');
				//window.scrollTo("#dbox_slider_speed");
				jQuery("html,body").animate({scrollTop:jQuery('#wpts_options_tab_code').offset().top-50}, 600);
				return false;
			}
			var tab_end_code=jQuery(jqForm).eq(0).find("#wpts_options_tab_end_code").val();
			if(tab_end_code=='') {
				alert("Please Enter Tab End Shortcode !"); 
				jQuery(jqForm).eq(0).find("#wpts_options_tab_end_code").addClass('error');
				//window.scrollTo("#dbox_slider_speed");
				jQuery("html,body").animate({scrollTop:jQuery('#wpts_options_tab_end_code').offset().top-50}, 600);
				return false;
			}
		} ,							
		success: function(){		
		    jQuery("html,body").animate({scrollTop:jQuery('#poststuff').offset().top-50}, 600);
		    jQuery('#saveSettingsResult').html("<div id='saveMessage' class='successModal'></div>");
		    jQuery('#saveMessage').append("<p><?php echo htmlentities(__('Settings Saved Successfully','wp'),ENT_QUOTES); ?></p>").show();
		 }, 
		 timeout: 5000
	      }); 
	      setTimeout("jQuery('#saveMessage').hide('slow');", 5000);
	      return false; 
	   });
});
</script>
<!--**** Script Ended For Validations ***** -->
</div> <!--end of float wrap -->

<?php	
}
// Hook for adding admin menus
if ( is_admin() ){ // admin actions
  add_action('admin_menu', 'wpts_pro_settings');
  add_action( 'admin_init', 'register_wpts_pro_settings' ); 
} 
function register_wpts_pro_settings() { // whitelist options
	register_setting( 'wpts-group', 'wpts_options' );
	register_setting( 'wpts-style-group', 'wpts_style_options' );
}
?>
