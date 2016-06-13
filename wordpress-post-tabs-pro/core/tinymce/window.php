<?php
// look up for the path
require_once( dirname( dirname(__FILE__) ) . '/wpts-config.php');
// check for rights
if ( !current_user_can('edit_pages') && !current_user_can('edit_posts') ) 
	wp_die(__("You are not allowed to be here"));
global $wpdb,$wpts;
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>WordPress Post Tabs Pro</title>
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />
	<?php wp_print_scripts('jquery');?>
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/utils/mctabs.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/utils/form_utils.js"></script>
	<script type="text/javascript">	var wptsadminL10n={'tab':'<?php echo $wpts['tab_code']; ?>','end':'<?php echo $wpts['tab_end_code']; ?>'};</script>
	<script language="javascript" type="text/javascript" src="<?php echo WPTSPRO_URLPATH; ?>core/tinymce/tinymce.js"></script> 
	<base target="_self" />
	<style type="text/css">
		.form-table,.form-table td{font-family:'Century Gothic', 'Avant Garde', 'Trebuchet MS', sans-serif;font-size:11px;font-weight:bold;}
		.form-table tr{background:#ddd;}
		.button-primary{background-color: #21759B !important;
			background-image: -webkit-gradient(linear,left top,left bottom,from(#2A95C5),to(#21759B)) !important;
			background-image: -webkit-linear-gradient(top,#2A95C5,#21759B) !important;
			background-image: -moz-linear-gradient(top,#2A95C5,#21759B) !important;
			background-image: -ms-linear-gradient(top,#2A95C5,#21759B) !important;
			background-image: -o-linear-gradient(top,#2A95C5,#21759B) !important;
			background-image: linear-gradient(to bottom,#2A95C5,#21759B) !important;
			border-color: #21759B !important;
			border-bottom-color: #1E6A8D !important;
			-webkit-box-shadow: inset 0 1px 0 rgba(120, 200, 230, 0.5) !important;
			box-shadow: inset 0 1px 0 rgba(120, 200, 230, 0.5) !important;
			color: white !important;
			text-decoration: none !important;
			text-shadow: 0 1px 0 rgba(0, 0, 0, 0.1) !important;
			}
		.select_skin {text-transform: capitalize;}
	</style>
	<script type="text/javascript">
		jQuery(document).ready(function() {
			jQuery("#skin").change(function() {
				var skinval=jQuery("#skin").val();
				if( skinval ){
					var atts_file='../../skins/'+skinval+'/inc/atts.php';
					jQuery("#skin-atts").load(atts_file);
				}
			})
		})
	</script>
</head>
<body id="link" onLoad="tinyMCEPopup.executeOnLoad('init();');document.body.style.display='';document.getElementById('tabname').focus();" style="display: none">
	<form name="WPTSPRO" id="WPTSPRO" action="#" style="font-family:'Century Gothic', 'Avant Garde', 'Trebuchet MS', sans-serif;font-size:10px;">
	
		<div style="float:right;"><strong style="color:#ccc;font-size:9px;">powered by</strong> <a style="margin-left:5px;" href="http://tabbervilla.com/" target="_blank" rel="nofollow"><img src="<?php echo wpts_pro_plugin_url('core/images/tabbervilla.png');?>" width="120"/></a> </div>
		
		<table class="form-table" >
        <tr valign="top">
			<td nowrap="nowrap"><label for="tabname">Enter Tab Names</label></td>
			<td><input type="text" id="tabname" name="tabname" value="" size="50" /></td>
        </tr>
		<tr valign="top">
			<td></td>
            <td nowrap="nowrap"><label for="ex">e.g. Overview, Features, Test, Results</label></td>
        </tr>
        </table>
		<div style="margin:10px 0;font-weight:bold;color:#999;"><?php _e('All fields below are optional. If not populated, the default values from the Settings Panel will be picked.','wptspro');?></div>
		<table class="form-table" id="skins">
		<tr valign="top">
		<td scope="row"><label for="skin"><?php _e('Select Skin','wptspro'); ?></label></td> 
		<td><select name="skin" id="skin" class="select_skin">
			 <option value="" >Select a skin</option>
		<?php 
		$directory = WP_PLUGIN_DIR.'/'.basename(WPTSPRO_PLUGIN_BASENAME,'.php').'/skins';
		if ($handle = opendir($directory)) {
			while (false !== ($file = readdir($handle))) { 
			 if($file != '.' and $file != '..') { ?>
			  <option value="<?php echo $file;?>" ><?php echo str_replace("_"," ",$file);?></option>
		 <?php  } }
			closedir($handle);
		}
		?>
		</select>
		</td>
		</tr>
		</table>
		
		<table class="form-table" id="skin-atts">
		</table>

	<div class="mceActionPanel" style="margin:20px 0 0 0;">
		<div style="float: left">
			<input type="submit" class="button-primary" id="insert" name="insert" value="Insert" onClick="insertWPTSPROLink();" />
		</div>
		<!--<div style="float: right">
			<input type="button" id="cancel" name="cancel" value="Cancel" onClick="tinyMCEPopup.close();" />
		</div>-->
	</div>
</form>
</body>
</html>
