<div class='w3eden'>
	<div class='container-fluid'>
		<div class='row'>
			<div class='col-md-12'>
				<select name='contact[agent]' class='form-control'>
					<option value=''>Select an agent</option>
					<?php foreach ($agents as $agent) { ?>
					<?php $agent_data = $agent->data; ?>
					<option <?php echo ($agent_data->ID == $agent_id ? 'selected="selected"' : '') ?> value='<?php echo $agent_data->ID ?>'><?php echo $agent_data->display_name ?></option>
					<?php } ?>
				</select>
			</div>
		</div>
	</div>
</div>