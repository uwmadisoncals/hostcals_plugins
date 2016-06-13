<?php
/*
Plugin Name: Weaver II Maintenance
Plugin URI: http://weavertheme.com/plugins/
Description: Weaver II Maintenance - Maintenance Tools for Weaver II
Author: Bruce Wampler
Author URI: http://weavertheme.com/about/
Version: 1.2
License: GPL

GPL License: http://www.opensource.org/licenses/gpl-license.php

This program is distributed in the hope that it will be useful, but
WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*/

function wpwii_request_handler() {

    if (!function_exists('weaverii_submitted'))
        return;

    if ( weaverii_submitted('resetm_weaverii') ) {
	weaverii_maint_save_msg('Weaver II Settings Cleared');

	delete_option('weaverii_settings');
	delete_option('weaverii_pro');
	delete_option('weaverii_pro_backup');
	delete_option('weaverii_settings_backup');
	delete_option('weaverii_settings_mobile');
	if (weaverii_init_base()) {
	    delete_option( 'theme_mods_weaver-ii-pro' . WEAVERII_PRO_SLUG );
	} else {
	    delete_option( 'theme_mods_weaver-ii' . WEAVERII_PRO_SLUG );
	}

    }

    if ( weaverii_submitted('reset_old_weaver') ) {
	weaverii_maint_save_msg('OLD Weaver Settings Cleared');
	delete_option('ttw_options');
	delete_option('ttw_myoptions');
	delete_option('ttw_adminoptions');
	delete_option('weaver-settings');
	delete_option('widget_weaver_login');
	delete_option('widget_weaver_text');
	delete_option('widget_weaver_login');
	delete_option('weaver_advanced_settings');
	delete_option('weaver_main_settings');
    }

    if (weaverii_submitted('convertm_weaver')) {
	require_once(dirname( __FILE__ ) . '/weaver-ii-convert-old-weaver.php');
	weaverii_convert_old_weaver();
    }

}

    add_action('init', 'wpwii_request_handler');

function weaverii_maint_save_msg($msg) {
    echo '<div id="message" class="updated fade" style="width:70%;"><p><strong>' . $msg .
	    '</strong></p></div>';
}
function weaverii_maint_error_msg($msg) {
    echo '<div id="message" class="updated fade" style="background:#F88;" style="width:70%;"><p><strong>' . $msg .
	    '</strong></p></div>';
}

function wpwii_maint_admin() {
    if (!function_exists('weaverii_submitted'))
        return;
?>
<div class="wrap">
    <h1>Weaver II Maintenance Tools Version 1.1 (<?php echo WEAVERII_THEMEVERSION; ?>)</h1>
	<h2>Clean Weaver II Settings</h2>
	    <p>When you click "Clear All Weaver II Settings", <strong>all</strong> Weaver II settings will be cleared from the data base. This includes both Weaver II Free version and Weaver II Pro settings.</p>
	    <form id="wpw_maintweaver" name="wpw_maintweaver" action="<?php get_bloginfo('wpurl');?>/wp-admin/themes.php?page=weaverii_maintenance" method="post">
		<span class="submit"><input type="submit" name="resetm_weaverii" value="Clear All Weaver II Settings"/></span>
		<?php weaverii_nonce_field('resetm_weaverii'); ?>
	    </form>
	    <br /><br />
	<hr />
<?php
    if (get_option('weaver_main_settings') ||		// any old Weaver Settings around?
	get_option('ttw_options') ||
	get_option('ttw_myoptions') ||
	get_option('ttw_adminoptions') ||
	get_option('weaver-settings') ||
	get_option('widget_weaver_login') ||
	get_option('widget_weaver_text') ||
	get_option('widget_weaver_login') ||
	get_option('weaver_advanced_settings')) {
?>

	<h2>Clean Old Weaver Version Settings</h2>
	    <p>Settings from previous versions of the Weaver theme have been detected. When you click "Clean OLD Weaver", <strong>all</strong> settings from previous versions of Weaver will be cleared from the data base. This includes 2010 Weaver, Weaver 1.x, Weaver 2.x, and Weaver Plus.</p>
	    <p>
		<strong style="color:red">IMPORTANT!</strong> If you need to upgrade your Weaver 2.2.x settings to Weaver II, be sure to do that BEFORE you clear your old settings!</p>
	    <form id="wpw_cleanweaver" name="wpw_cleanweaver" action="<?php get_bloginfo('wpurl');?>/wp-admin/themes.php?page=weaverii_maintenance" method="post">
		<span class="submit"><input type="submit" name="reset_old_weaver" value="Clear OLD Weaver"/></span>
		<?php weaverii_nonce_field('reset_old_weaver'); ?>
	    </form>
	    <br /><br />
	<hr />
<?php
    }
?>
	<h2>Upgrade Weaver 2.2.x to Weaver II</h2>
<?php
    /* see if old Weaver 2.0 settings found, and tell them how to upgrade */
    if (!get_option('weaver_main_settings')) {
?>
      <div style="background-color:#FFEEEE; border: 1px solid red; margin: 0px 60px 0px 20px; padding-left:10px;width:70%">
	<p><strong>Notice:</strong> No existing settings from an older version of Weaver have been found, no upgrade possible.
	</p>
      </div>
      <?php

    } else {
?>
      <div style="background-color:#EEFFEE; border: 1px solid green; margin: 0px 60px 0px 20px; padding-left:10px;width:70%">
	<p><strong>Notice:</strong> Existing settings from an older version of Weaver have been found.
	Click the ? Help button for more information.
	<?php weaverii_help_link('help.html#UpgradingWeaver','Help for Advanced Options'); ?>
	</p>
      </div>
      <br /> <br />
      <form name="resetweaverii_form" method="post">
	    <strong>Click the Convert Old Weaver Settings button to convert the settings from the previous version to the
	    new Weaver II settings.</strong><br />
<?php	    if (weaverii_init_base()) {
?>
	    This will also convert Weaver Plus settings if they are present.<br />
<?php	    } ?>
	    <em>Warning: You will lose all current settings.</em> You should use the
	    Save/Restore tab to save a copy of your current settings before converting!<br /><br />
	    <span class="submit"><input type="submit" name="convertm_weaver" value="Convert Old Weaver Settings"/></span>
	    <?php weaverii_nonce_field('convertm_weaver'); ?>
	</form> <!-- wii_resetweaverii_form -->
      <?php
      }
?>
</div>
<?php
}

function wpwii_maint_admin_menu() {
    if (function_exists('weaverii_submitted'))
	add_theme_page(
	  'Weaver II Maintenance Tools','Maintain Weaver II','manage_options','weaverii_maintenance', 'wpwii_maint_admin');
}

    add_action('admin_menu', 'wpwii_maint_admin_menu');
?>
