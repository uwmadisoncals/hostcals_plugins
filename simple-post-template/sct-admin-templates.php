<?php 
global $wpdb, $SimpleContentTemplates;
$templates = $SimpleContentTemplates->get_templates();	
?>
<div class="wrap">
<h2>Simple Content Templates</h2>
<p><a href="admin.php?page=sct-new-template" class="button">New Template</a></p>
<table class="widefat">
<thead>
	<tr>
		<th>Title</th>
		<th>Excerpt</th>
		<th>Content</th>
		<th></th>
	</tr>
</thead>
<tfoot>
	<tr>
		<th>Title</th>
		<th>Excerpt</th>
		<th>Content</th>
		<th></th>
	</tr>
</tfoot>
<?php if(count($templates) == 0): ?>
<tr> 
	<td>Looks like you don't have any templates! Why not <a href="admin.php?page=sct-new-template">create one</a>?</td>
	<td></td>
	<td></td>
	<td></td>
</tr>
<?php endif; ?>
<?php foreach($templates as $template) : ?>
	<tr>
		<td><?php echo stripslashes($template->title); ?></td>
		<td><?php echo strip_tags(stripslashes($template->excerpt)); ?></td>
		<td><?php echo strip_tags(stripslashes(substr($template->content, 0, 160))); ?>...</td>
		<td><a href="admin.php?page=sct-new-template&id=<?php echo $template->id; ?>">Edit</a> / <a onclick="return confirm('Are you sure?')" href="admin.php?page=sct-templates&sct_action=delete&id=<?php echo $template->id; ?>">Delete</a></td>
	</tr>
<?php endforeach; ?>
</table>

</div>