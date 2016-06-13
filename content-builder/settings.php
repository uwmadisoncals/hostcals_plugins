<?php
/*
 * Content Builder plugin settings page
 */
if ( isset($_POST['submit']) ) {
	if ( function_exists('current_user_can') && !current_user_can('manage_options') )
		die('Cheatin&#8217; uh?');

	if( isset($_POST['width']) ) {
		$value = $wpdb->escape($_POST['width']);
		update_option('cb_width', $value);
	}
}
?>

<?php if ( !empty($_POST['submit'] ) ) : ?>
<div id="message" class="updated fade"><p><strong><?php echo('Options saved.') ?></strong></p></div>
<?php endif; ?>
<div class="wrap">
	<h2><?php echo('Content Builder Configuration'); ?></h2>
	<div class="narrow">
		<form action="" method="post" id="newplug-conf">
			<p>Content Builder settings</p>
			<p>
				<label for="cb_width"><?php echo('Content Width (in px):'); ?></label>
				<input name="width" id="cb_width" type="text" value="<?php echo get_option('cb_width'); ?>" />
			</p>
			<p class="submit"><input type="submit" name="submit" value="<?php echo('Update options &raquo;'); ?>" /></p>
		</form>
	</div>
</div>