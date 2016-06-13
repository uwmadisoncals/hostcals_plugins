<?php
global $SimpleContentTemplates;
$templates = $SimpleContentTemplates->get_templates();

$titles = array(
	'Learn More',
	'Buy Now',
	'Upgrade Now',
);

$the_title = array_rand($titles);
$the_title = $titles[$the_title];
?>
<select name="sct_template" id="sct_template">
	<?php foreach($templates as $template): ?>
		<option value="<?php echo $template->id; ?>" <?php if($sct_auto == $template->id) echo "selected='selected'"; ?>><?php echo $template->title; ?></option>
	<?php endforeach; ?>
</select>
<input class="button-primary" name="sct_load_template" id="sct_load_template" type="button" onclick="return confirm('Are you sure? Loading this template may wipe out existing changes.')" value="Load Template" />

<h4>Advanced Content Templates</h4>
<p>You're missing out on powerful features like custom post type and taxonomy support.</p>
<a class="button-secondary" target="_blank" href="<?php echo $SimpleContentTemplates->bpt_url; ?>&utm_medium=Sidecar&utm_content=<?php echo urlencode($the_title); ?>"><?php echo $the_title; ?></a>

<script>
jQuery(document).ready(function() {
	jQuery("#sct_load_template").click(function() {
		var template = jQuery("#sct_template option:selected").val();
		window.location = 'post-new.php?post_type=<?php echo empty($_GET['post_type']) ? 'post' : $_GET['post_type']; ?>&sct_template_load=' + template;
	})
});
</script>