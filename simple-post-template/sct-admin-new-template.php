<?php global $wpdb, $SimpleContentTemplates; ?>
<div class="wrap">
<?php if(isset($_REQUEST['id'])): ?>
	<h2>Edit Template</h2>
	<?php
	$existing_template = $SimpleContentTemplates->get_template($_REQUEST['id']);
	$existing_template->content = stripslashes($existing_template->content);
	?>
<?php else: ?>
	<h2>New Template</h2>
<?php endif; ?>
<form method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
	<table class="form-table">
		<tbody>
			<tr valign="top">
				<th scope="row">
					<label for="sct-title">Title</label>
				</th>
				<td>
					<input type="text" name="sct-title" size="39" id="sct-title" value="<?php echo htmlspecialchars(stripslashes($existing_template->title)); ?>" />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="sct-excerpt">Excerpt</label>
				</th>
				<td>
					<textarea type="text" cols="40" name="sct-excerpt" id="sct-excerpt"><?php echo stripslashes($existing_template->excerpt); ?></textarea>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="sct-excerpt">Content</label>
				</th>
				<td>
					<?php wp_editor( stripslashes($existing_template->content), "sct-content"); ?>
				</td>
			</tr>
		</tbody>
	</table>
	<p><input class="button-primary" type="submit" value="Save Template" /></p>
	<?php if(isset($_REQUEST['id'])): ?>
		<input type="hidden" name="sct_action" value="edit-template" />
		<input type="hidden" name="sct-edit-id" value="<?php echo $_REQUEST['id']; ?>" />
	<?php else: ?>
		<input type="hidden" name="sct_action" value="create-template" />
	<?php endif; ?>
</form>
</div>