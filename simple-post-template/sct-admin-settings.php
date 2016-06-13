<?php
global $wpdb, $SimpleContentTemplates;
$templates = $SimpleContentTemplates->get_templates();
$sct_auto = get_option('sct_auto');

$headlines = array(
	'Be A Lifelong Learner',
	'Learn More',
	'Newsletter',
	'Join Our Newsletter',
	'Subscribe Now'
);

$the_headline = array_rand($headlines);
$the_headline = $headlines[$the_headline];
?>
<div class="wrap">
	<h2>Simple Content Templates Settings</h2>
	<div class="postbox-container" style="width:60%; margin-right: 20px;">
		<div class="metabox-holder">
			<div id="bpt_settings_box" class="postbox">
				<h3><?php _e('Settings'); ?></h3>
				<div class="inside">
					<form method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
						<table class="form-table">
							<tbody>
								<tr valign="top">
									<th scope="row">
										<label for="sct_auto">Auto Insert?</label>
									</th>
									<td>
										<input type="hidden" name="sct_auto" value="no">
										<label for="sct_auto">
											<select name="sct_auto">
												<?php if( count($templates) == 0 ): ?>
													<option disabled="disabled" value="no" <?php if($sct_auto == "no") echo "selected='selected'"; ?>>No Templates Exist</option>
												<?php else: ?>
													<option value="no" <?php if($sct_auto == "no") echo "selected='selected'"; ?>>Manual Insert Only</option>
													<?php foreach($templates as $template): ?>
														<option value="<?php echo $template->id; ?>" <?php if($sct_auto == $template->id) echo "selected='selected'"; ?>><?php echo $template->title; ?></option>
													<?php endforeach; ?>
												<?php endif; ?>
											</select>
										</label>
									</td>
								</tr>
							</tbody>
						</table>
						<input type="hidden" name="sct_action" value="save-settings" />
						<p><input class="button-primary" type="submit" value="Save Settings" /></p>
					</form>

					<h4 style="margin-top: 40px">Need More?</h4>
					<img class="alignright" src="http://cgd.io/wp-content/uploads/edd/2014/02/logo2-300x300.png" style="width:300px" />
					<p><b>Go pro!</b>If you need support for custom post templates, taxonomies, featured images, etc, check out our premium plugin <a href="<?php echo $SimpleContentTemplates->bpt_url; ?>">Advanced Content Templates</a>.</p>

					<p>Here are a few of the features:</p>
					<ul style="list-style: disc; margin-left: 20px;">
						<li>Custom Post Types</li>
						<li>Categories, Tags, and other taxonomies</li>
						<li>Features Images / Attachments</li>
						<li>Template Settings (for pages)</li>
						<li>Custom Fields</li>
						<li>And more!</li>
					</ul>

					<a class="button-primary" href="<?php echo $SimpleContentTemplates->bpt_url; ?>&utm_medium=SettingsPage">Learn More</a>
				</div>
			</div>
		</div>
	</div>
	<div class="postbox-container" style="width:25%;">
		<div class="metabox-holder">
			<div id="bpt_newsletter_box" class="postbox">
				<h3><?php _e( $the_headline ); ?></h3>
				<div class="inside">
					<!-- Begin MailChimp Signup Form -->
					<div id="mc_embed_signup">
						<form action="http://clifgriffin.us2.list-manage.com/subscribe/post?u=2b8f419ccd0352278e3485899&amp;id=d105898a94" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>

							<div class="mc-field-group">
								<label for="mce-EMAIL">Email Address</label><br>
								<input style="width: 100%" type="email" value="" name="EMAIL" class="required email" id="mce-EMAIL">
								<input type="hidden" name="HEADLINE" value="<?php echo $the_headline; ?>" />
							</div>

							<div style="padding-top: 10px;">
								<p style="float:left; margin-top: 0;"><small>Powered by <a href="http://eepurl.com/CuaW9" title="MailChimp - email marketing made easy and fun">MailChimp</a></small></p>
								<input type="submit" value="Subscribe" class="button-primary right" name="subscribe" id="mc-embedded-subscribe" class="button">
								<div style="clear:both"></div>
							</div>

							<p>The least intrusive newsletter you'll ever subscribe to. Find out about new plugin releases and updates.</p>
						</form>
					</div>
					<!--End mc_embed_signup-->
					<div style="clear:both"></div>
				</div>
			</div>
		</div>
		<div class="metabox-holder">
			<div id="bpt_settings_box" class="postbox">
				<h3><?php _e( 'About' ); ?></h3>
				<div class="inside">
					<p><strong>Simple Content Templates</strong> is a product of Clif Griffin Development Inc, who enjoys building plugins like this for money.</p> You may <a href="http://clifgriffin.com/contact">get in touch here</a>.
				</div>
			</div>
		</div>
	</div>
</div>