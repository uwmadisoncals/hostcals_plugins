<?php 
function wpts_pro_skins_styler_page() { 
	global $wpts_style;
	//$url = $url = admin_url('admin.php?page=post-tabs-pro-skins-styler');
	if(isset($_GET['skin'])) $curr_skin=$_GET['skin'];
	else $curr_skin='default';
	
?>
	<div class="top_head">
	<div style="float:right;"><strong style="color:#ccc;font-size:9px;">powered by</strong> <a style="margin-left:5px;" href="http://tabbervilla.com/" target="_blank" rel="nofollow"><img src="<?php echo wpts_pro_plugin_url('core/images/tabbervilla.png');?>" width="120"/></a> </div>
	<h2 class="wppts_title">WordPress Post Tabs PRO</h2>
	</div>
	<div class="outer_skin_styler">
        	<div class="menu menu-nav">
		<?php 
		$directory = WP_PLUGIN_DIR.'/'.basename(WPTSPRO_PLUGIN_BASENAME,'.php').'/skins';
		if ($handle = opendir($directory)) {
			while (false !== ($file = readdir($handle))) { 
			 if($file != '.' and $file != '..') { 
				if($file==$curr_skin) $active_class="active";
				else $active_class="";
			?>
			  <div class="<?php echo $active_class;?>" onmouseover="document.getElementById('place-holder-<?php echo $file;?>').src='<?php echo WPTSPRO_URLPATH; ?>skins/<?php echo $file;?>/screenshot.png';"
  onmouseout="document.getElementById('place-holder-<?php echo $file;?>').src='';"><a href="<?php echo add_query_arg( 'skin', $file );?>" ><?php echo str_replace("_"," ",$file);?><img src="" id="place-holder-<?php echo $file;?>" style="z-index: 100; position: absolute;top:0;left:0;margin-left: 60%;" /></a></div>
		 <?php  } }
			closedir($handle);
		}
		?>
		</div>
		
	<div class="wpts-skin-settings">
		<div class="skin_title"> 
			<h2 class="skin_head">Skin Name: 
			<span><?php $display_currentSkin=str_replace("_"," ",$curr_skin);echo $display_currentSkin;?></span></h2>
		</div>
		<h2 class="sub-heading">Background Color</h2>
		<form action="options.php" method="post" id="skinsStyler">
		<?php $directory = WP_PLUGIN_DIR.'/'.basename(WPTSPRO_PLUGIN_BASENAME,'.php').'/skins';

	if ($handle = opendir($directory)) {
		while (false !== ($file = readdir($handle))) { 
			if($file != '.' and $file != '..') { 
				settings_fields('wpts-style-group');
				$wpts_style = get_option('wpts_style_options');
				if($file==$curr_skin){
					 $css="display:block";	
					?>
					<table id="<?php echo $file;?>" class="form-table">
					<tr valign="top">
						<td>Default</td>
						<td><input type="text" name="wpts_style_options[<?php echo $file;?>][bg]" id="color_value_1" value="<?php echo $wpts_style[$file]['bg'];?>" />&nbsp;
						<img id="color_picker_1" src="<?php echo WPTSPRO_URLPATH; ?>core/images/color_picker.png" alt="Pick the color of your choice"/>
						<div class="color-picker-wrap" id="colorbox_1"></div>
						</td>
					</tr>
					<tr valign="top">
						<td>Active State</td>
						<td><input type="text" name="wpts_style_options[<?php echo $file;?>][active_bg]" id="color_value_2" value="<?php echo $wpts_style[$file]['active_bg'];?>" />&nbsp;
						<img id="color_picker_2" src="<?php echo WPTSPRO_URLPATH; ?>core/images/color_picker.png" alt="Pick the color of your choice"/>
						<div class="color-picker-wrap" id="colorbox_2"></div>
						</td>
					</tr>
					<tr valign="top">
						<td>Hover State</td>
						<td><input type="text" name="wpts_style_options[<?php echo $file;?>][hover_bg]" id="color_value_3" value="<?php echo $wpts_style[$file]['hover_bg'];?>" />&nbsp;
						<img id="color_picker_3" src="<?php echo WPTSPRO_URLPATH; ?>core/images/color_picker.png" alt="Pick the color of your choice"/>
						<div class="color-picker-wrap" id="colorbox_3"></div>
						</td>
					</tr>
					</table>
					<h2 class="sub-heading">Tab Name Color</h2>
					<table class="form-table">
					<tr valign="top">
						<td>Default</td>
						<td><input type="text" name="wpts_style_options[<?php echo $file;?>][color]" id="color_value_4" value="<?php echo $wpts_style[$file]['color'];?>" />&nbsp;
						<img id="color_picker_4" src="<?php echo WPTSPRO_URLPATH; ?>core/images/color_picker.png" alt="Pick the color of your choice"/>
						<div class="color-picker-wrap" id="colorbox_4"></div>
						</td>
					</tr>
					<tr valign="top">
						<td>Active State</td>
						<td><input type="text" name="wpts_style_options[<?php echo $file;?>][active_color]" id="color_value_5" value="<?php echo $wpts_style[$file]['active_color'];?>" />&nbsp;
						<img id="color_picker_5" src="<?php echo WPTSPRO_URLPATH; ?>core/images/color_picker.png" alt="Pick the color of your choice"/>
						<div class="color-picker-wrap" id="colorbox_5"></div>
						</td>
					</tr>
					<tr valign="top">
						<td>Hover State</td>
						<td><input type="text" name="wpts_style_options[<?php echo $file;?>][hover_color]" id="color_value_6" value="<?php echo $wpts_style[$file]['hover_color'];?>" />&nbsp;
						<img id="color_picker_6" src="<?php echo WPTSPRO_URLPATH; ?>core/images/color_picker.png" alt="Pick the color of your choice"/>
						<div class="color-picker-wrap" id="colorbox_6"></div>
						</td>
					</tr>
					</table>
			<?php }	else { ?>
					<input type="hidden" name="wpts_style_options[<?php echo $file;?>][bg]" value="<?php echo $wpts_style[$file]['bg'];?>" />
					<input type="hidden" name="wpts_style_options[<?php echo $file;?>][active_bg]"  value="<?php echo $wpts_style[$file]['active_bg'];?>" />
					<input type="hidden" name="wpts_style_options[<?php echo $file;?>][hover_bg]" value="<?php echo $wpts_style[$file]['hover_bg'];?>" />
					<input type="hidden" name="wpts_style_options[<?php echo $file;?>][color]"  value="<?php echo $wpts_style[$file]['color'];?>" />
					<input type="hidden" name="wpts_style_options[<?php echo $file;?>][active_color]"  value="<?php echo $wpts_style[$file]['active_color'];?>" />
					<input type="hidden" name="wpts_style_options[<?php echo $file;?>][hover_color]" value="<?php echo $wpts_style[$file]['hover_color'];?>" />		
			<?php	} 
			 } 
		} 
		closedir($handle); 
	}?>

		<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>"  />
		</p>
		</form>
		<div id="saveResult"></div>
	
	</div>
	<div class="right_panel_skins">
		<div class="postbox"> 
		<h3 class="hndle"><span><?php _e('About this Plugin:','wptspro'); ?></span></h3> 
			<div class="inside">
					<ul>
						<li><a href="http://tabbervilla.com/wordpress-post-tabs-pro/" title="<?php _e('WordPress Post Tabs PRO Homepage','wptspro'); ?>" ><?php _e('Plugin Homepage','wptspro'); ?></a></li>
						<li><a href="http://support.tabbervilla.com/" title="<?php _e('Support Forum for WordPress Post Tabs','wptspro'); ?>
						" ><?php _e('Support Forum','wptspro'); ?></a></li>
						<li><a href="http://keencodes.com/" title="<?php _e('WordPress Post Tabs Author Page','wptspro'); ?>" ><?php _e('About the Author','wptspro'); ?></a></li>
								<li><a href="http://tabbervilla.com" title="<?php _e('Visit TabberVilla','wptspro'); ?>
						" ><?php _e('Plugin Parent Site','wptspro'); ?></a></li>
					</ul> 	
				<div class="clear"></div>
			</div> 
		</div> 
		<div class="clear"></div>	
	</div>
	
</div>


<?php 
	if ( is_admin() ){ // admin actions
 	 // Settings page only
	if ( isset($_GET['page']) && 'post-tabs-pro-skins-styler' == $_GET['page'] ) {
		wp_enqueue_script( 'wpts_admin_js', wpts_pro_plugin_url( 'core/js/admin.js' ),	array('jquery'), WPTSPRO_VER, false);
		wp_enqueue_style( 'wpts_admin_css', wpts_pro_plugin_url( 'core/css/admin.css' ),false, WPTSPRO_VER, 'all');
		wp_enqueue_script( 'jquery-form' );
		wp_print_scripts( 'farbtastic' );
		wp_print_styles( 'farbtastic' );
		}
       } 
?>
<script type="text/javascript">
	// <![CDATA[
	jQuery(document).ready(function() {
		jQuery('#skinsStyler').submit(function() {  
		      jQuery(this).ajaxSubmit({
			 success: function(){
			    jQuery('#saveResult').html("<div id='saveMessage' class='successModal'></div>");
			    jQuery('#saveMessage').append("<p><?php echo htmlentities(__('Settings Saved Successfully','wp'),ENT_QUOTES); ?></p>").show();
			 }, 
			 timeout: 5000
		      }); 
		      setTimeout("jQuery('#saveMessage').hide('slow');", 5000);
		      return false; 
		   });
			
	//for colorpicker
		jQuery('#colorbox_1').farbtastic('#color_value_1');
		jQuery('#color_picker_1').click(function () {
		   if (jQuery('#colorbox_1').css('display') == "block") {
			  jQuery('#colorbox_1').fadeOut("slow"); }
		   else {
			  jQuery('#colorbox_1').fadeIn("slow"); }
		});
		var colorpick_1 = false;
		jQuery(document).mousedown(function(){
			if (colorpick_1 == true) {
				return; }
			jQuery('#colorbox_1').fadeOut("slow");
		});
		jQuery(document).mouseup(function(){
			colorpick_1 = false;
		});
		//for colorpicker
		jQuery('#colorbox_2').farbtastic('#color_value_2');
		jQuery('#color_picker_2').click(function () {
		   if (jQuery('#colorbox_2').css('display') == "block") {
			  jQuery('#colorbox_2').fadeOut("slow"); }
		   else {
			  jQuery('#colorbox_2').fadeIn("slow"); }
		});
		var colorpick_2 = false;
		jQuery(document).mousedown(function(){
			if (colorpick_2 == true) {
				return; }
			jQuery('#colorbox_2').fadeOut("slow");
		});
		jQuery(document).mouseup(function(){
			colorpick_2 = false;
		});
		//for colorpicker
		jQuery('#colorbox_3').farbtastic('#color_value_3');
		jQuery('#color_picker_3').click(function () {
		   if (jQuery('#colorbox_3').css('display') == "block") {
			  jQuery('#colorbox_3').fadeOut("slow"); }
		   else {
			  jQuery('#colorbox_3').fadeIn("slow"); }
		});
		var colorpick_3 = false;
		jQuery(document).mousedown(function(){
			if (colorpick_3 == true) {
				return; }
			jQuery('#colorbox_3').fadeOut("slow");
		});
		jQuery(document).mouseup(function(){
			colorpick_3 = false;
		});
		//for colorpicker
		jQuery('#colorbox_4').farbtastic('#color_value_4');
		jQuery('#color_picker_4').click(function () {
		   if (jQuery('#colorbox_4').css('display') == "block") {
			  jQuery('#colorbox_4').fadeOut("slow"); }
		   else {
			  jQuery('#colorbox_4').fadeIn("slow"); }
		});
		var colorpick_4 = false;
		jQuery(document).mousedown(function(){
			if (colorpick_4 == true) {
				return; }
			jQuery('#colorbox_4').fadeOut("slow");
		});
		jQuery(document).mouseup(function(){
			colorpick_4 = false;
		});
		//for colorpicker
		jQuery('#colorbox_5').farbtastic('#color_value_5');
		jQuery('#color_picker_5').click(function () {
		   if (jQuery('#colorbox_5').css('display') == "block") {
			  jQuery('#colorbox_5').fadeOut("slow"); }
		   else {
			  jQuery('#colorbox_5').fadeIn("slow"); }
		});
		var colorpick_5 = false;
		jQuery(document).mousedown(function(){
			if (colorpick_5 == true) {
				return; }
			jQuery('#colorbox_5').fadeOut("slow");
		});
		jQuery(document).mouseup(function(){
			colorpick_5 = false;
		});
		//for colorpicker
		jQuery('#colorbox_6').farbtastic('#color_value_6');
		jQuery('#color_picker_6').click(function () {
		   if (jQuery('#colorbox_6').css('display') == "block") {
			  jQuery('#colorbox_6').fadeOut("slow"); }
		   else {
			  jQuery('#colorbox_6').fadeIn("slow"); }
		});
		var colorpick_6 = false;
		jQuery(document).mousedown(function(){
			if (colorpick_6 == true) {
				return; }
			jQuery('#colorbox_6').fadeOut("slow");
		});
		jQuery(document).mouseup(function(){
			colorpick_6 = false;
		});
	
});
	
</script>
<style type="text/css">
	.color-picker-wrap {position: absolute;	display: none; background: #fff;border: 3px solid #ccc;	padding: 3px;z-index: 1000;}
</style>
<?php
}
//Global Styles 
global $wpts_style,$default_tab_styles;
$wpts_style = get_option('wpts_style_options');
$default_tab_styles=array('red' =>array(
				'bg'=>'#',
				'color'=>'#',
				'active_bg'=>'#',
				'active_color'=>'#',
				'hover_bg'=>'#',
				'hover_color'=>'#'
			 ),
	                 'default' =>array(
				'bg'=>'#',
				'color'=>'#',
				'active_bg'=>'#',
				'active_color'=>'#',
				'hover_bg'=>'#',
				'hover_color'=>'#'
			 ),
			 'nice_pink' =>array(
				'bg'=>'#',
				'color'=>'#',
				'active_bg'=>'#',
				'active_color'=>'#',
				'hover_bg'=>'#',
				'hover_color'=>'#'
			 ),
			 'simple_gray' =>array(
				'bg'=>'#',
				'color'=>'#',
				'active_bg'=>'#',
				'active_color'=>'#',
				'hover_bg'=>'#',
				'hover_color'=>'#'
			 ),
			 'pepper_grinder' =>array(
				'bg'=>'#',
				'color'=>'#',
				'active_bg'=>'#',
				'active_color'=>'#',
				'hover_bg'=>'#',
				'hover_color'=>'#'
			 ),
			 'clean_blue' =>array(
				'bg'=>'#',
				'color'=>'#',
				'active_bg'=>'#',
				'active_color'=>'#',
				'hover_bg'=>'#',
				'hover_color'=>'#'
			 ),
			 'lightness' =>array(
				'bg'=>'#',
				'color'=>'#',
				'active_bg'=>'#',
				'active_color'=>'#',
				'hover_bg'=>'#',
				'hover_color'=>'#'
			 ),
			 'minimal' =>array(
				'bg'=>'#',
				'color'=>'#',
				'active_bg'=>'#',
				'active_color'=>'#',
				'hover_bg'=>'#',
				'hover_color'=>'#'
			 ),
			 'gray' =>array(
				'bg'=>'#',
				'color'=>'#',
				'active_bg'=>'#',
				'active_color'=>'#',
				'hover_bg'=>'#',
				'hover_color'=>'#'
			 ),
			 'blocks' =>array(
				'bg'=>'#',
				'color'=>'#',
				'active_bg'=>'#',
				'active_color'=>'#',
				'hover_bg'=>'#',
				'hover_color'=>'#'
			 ),
			'sliding' =>array(
				'bg'=>'#',
				'color'=>'#',
				'active_bg'=>'#',
				'active_color'=>'#',
				'hover_bg'=>'#',
				'hover_color'=>'#'
			 ),
			 'darkness' =>array(
				'bg'=>'#',
				'color'=>'#',
				'active_bg'=>'#',
				'active_color'=>'#',
				'hover_bg'=>'#',
				'hover_color'=>'#'
			 )
		);
?>
