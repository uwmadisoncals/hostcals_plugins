<?php
$result = '';

if ($_POST) {
	if (isset($_POST['button-id']) && isset($_POST['bulk-action-select'])) {
		if ($_POST['bulk-action-select'] == 'trash') {
			$count = 0;
			
			foreach ($_POST['button-id'] as $id) {
				mbpro_button_move_to_trash($id);
				$count++;
			}
			
			if ($count == 1) {
				$result = __('Moved 1 button to the trash.', 'maxbuttons-pro');
			}
			
			if ($count > 1) {
				$result = sprintf(__('Moved %s buttons to the trash.', 'maxbuttons-pro'), $count);
			}
		}
	}
}

if (isset($_GET['message']) && $_GET['message'] == '1') {
	$result = __('Moved 1 button to the trash.', 'maxbuttons-pro');
}

$published_buttons = mbpro_get_published_buttons();
$published_buttons_count = mbpro_get_published_buttons_count();
$trashed_buttons_count = mbpro_get_trashed_buttons_count();
?>

<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery("#bulk-action-all").click(function() {
			jQuery("#maxbuttons input[name='button-id[]']").each(function() {
				if (jQuery("#bulk-action-all").is(":checked")) {
					jQuery(this).attr("checked", "checked");
				}
				else {
					jQuery(this).removeAttr("checked");
				}
			});
		});
		
		<?php if ($result != '') { ?>
			jQuery("#maxbuttons .message").show();
		<?php } ?>
	});
</script>

<div id="maxbuttons">
	<div class="wrap">
		<div class="icon32">
			<a href="http://maxbuttons.com" target="_blank"><img src="<?php echo MAXBUTTONS_PRO_PLUGIN_URL ?>/images/mb-32.png" alt="MaxButtons" /></a>
		</div>
		
		<h2 class="title"><?php _e('MaxButtons Pro: Buttons', 'maxbuttons-pro') ?></h2>
		
		<div class="logo">
			<?php _e('Brought to you by', 'maxbuttons-pro') ?>
			<a href="http://maxfoundry.com" target="_blank"><img src="<?php echo MAXBUTTONS_PRO_PLUGIN_URL ?>/images/max-foundry.png" alt="Max Foundry" /></a>
			<?php printf(__('makers of %sMaxGalleria%s and %sMaxInbound%s', 'maxbuttons-pro'), '<a href="http://maxgalleria.com/?ref=mbpro" target="_blank">', '</a>', '<a href="http://maxinbound.com/?ref=mbpro" target="_blank">', '</a>') ?>
		</div>
		
		<div class="clear"></div>
		
		<div class="main">
			<h2 class="tabs">
				<span class="spacer"></span>
				<a class="nav-tab nav-tab-active" href="<?php echo admin_url() ?>admin.php?page=maxbuttons-controller&action=list"><?php _e('Buttons', 'maxbuttons-pro') ?></a>
				<a class="nav-tab" href="<?php echo admin_url() ?>admin.php?page=maxbuttons-packs"><?php _e('Packs', 'maxbuttons-pro') ?></a>
				<a class="nav-tab" href="<?php echo admin_url() ?>admin.php?page=maxbuttons-export"><?php _e('Export', 'maxbuttons-pro') ?></a>
				<a class="nav-tab" href="<?php echo admin_url() ?>admin.php?page=maxbuttons-support"><?php _e('Support', 'maxbuttons-pro') ?></a>
				<a class="nav-tab" href="<?php echo admin_url() ?>admin.php?page=maxbuttons-license"><?php _e('License', 'maxbuttons-pro') ?></a>
			</h2>

			<div class="form-actions">
				<a class="button-primary" href="<?php echo admin_url() ?>admin.php?page=maxbuttons-controller&action=button"><?php _e('Add New', 'maxbuttons-pro') ?></a>
			</div>

			<?php if ($result != '') { ?>
				<div class="message"><?php echo $result ?></div>
			<?php } ?>
			
			<p class="status">
				<strong><?php _e('All', 'maxbuttons-pro') ?></strong> <span class="count">(<?php echo $published_buttons_count ?>)</span>

				<?php if ($trashed_buttons_count > 0) { ?>
					<span class="separator">|</span>
					<a href="<?php echo admin_url() ?>admin.php?page=maxbuttons-controller&action=list&status=trash"><?php _e('Trash', 'maxbuttons-pro') ?></a> <span class="count">(<?php echo $trashed_buttons_count ?>)</span>
				<?php } ?>
			</p>

			<form method="post">
				<select name="bulk-action-select" id="bulk-action-select">
					<option value=""><?php _e('Bulk Actions', 'maxbuttons-pro') ?></option>
					<option value="trash"><?php _e('Move to Trash', 'maxbuttons-pro') ?></option>
				</select>
				<input type="submit" class="button" value="<?php _e('Apply', 'maxbuttons-pro') ?>" />
				
				<div class="button-list">		
					<table cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<th><input type="checkbox" name="bulk-action-all" id="bulk-action-all" /></th>
							<th><?php _e('Button', 'maxbuttons-pro') ?></th>
							<th><?php _e('Name and Description', 'maxbuttons-pro') ?></th>
							<th><?php _e('Shortcode', 'maxbuttons-pro') ?></th>
							<th><?php _e('Actions', 'maxbuttons-pro') ?></th>
						</tr>
						<?php foreach ($published_buttons as $b) { ?>
							<tr>
								<td valign="center">
									<input type="checkbox" name="button-id[]" id="button-id-<?php echo $b->id ?>" value="<?php echo $b->id ?>" />
								</td>
								<td>
									<?php echo do_shortcode('[maxbutton id="' . $b->id . '" externalcss="false" ignorecontainer="true"]') ?>
								</td>
								<td>
									<a class="button-name" href="<?php admin_url() ?>admin.php?page=maxbuttons-controller&action=button&id=<?php echo $b->id ?>"><?php echo $b->name ?></a>
									<br />
									<p><?php echo $b->description ?></p>
								</td>
								<td>
									[maxbutton id="<?php echo $b->id ?>"]
								</td>
								<td>
									<a href="<?php admin_url() ?>admin.php?page=maxbuttons-controller&action=button&id=<?php echo $b->id ?>"><?php _e('Edit', 'maxbuttons-pro') ?></a>
									<span class="separator">|</span>
									<a href="<?php admin_url() ?>admin.php?page=maxbuttons-controller&action=copy&id=<?php echo $b->id ?>"><?php _e('Copy', 'maxbuttons-pro') ?></a>
									<span class="separator">|</span>
									<a href="#" onclick="window.open('<?php echo MAXBUTTONS_PRO_PLUGIN_URL ?>/maxbuttons-button-css.php?id=<?php echo $b->id ?>', 'ButtonCSS', 'width=800, height=650, scrollbars=1'); return false;"><?php _e('View CSS', 'maxbuttons-pro') ?></a>
									<span class="separator">|</span>
									<a href="<?php admin_url() ?>admin.php?page=maxbuttons-controller&action=trash&id=<?php echo $b->id ?>"><?php _e('Move to Trash', 'maxbuttons-pro') ?></a>
								</td>
							</tr>
						<?php } ?>
					</table>
				</div>
			</form>
		</div>
		
		<div class="offers">
			<?php include 'maxbuttons-offers.php' ?>
		</div>
	</div>
</div>
