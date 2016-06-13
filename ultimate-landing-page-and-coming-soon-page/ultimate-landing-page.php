<?php
/*
Plugin Name: Ultimate Landing Page and Coming Soon Page
Plugin URI: http://www.thinklandingpages.com
Description: Create a landing page, coming soon page, or maintenance page and collect emails.
Version: 1.1.29
Author: Jeff Bullins
Author URI: http://www.thinklandingpages.com
*/

/*  Copyright 2012, 2013, 2014 Jeff Bullins (email : support@thinklandingpages.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// ------------------------------------------------------------------------
// REQUIRE MINIMUM VERSION OF WORDPRESS:                                               
// ------------------------------------------------------------------------
// THIS IS USEFUL IF YOU REQUIRE A MINIMUM VERSION OF WORDPRESS TO RUN YOUR
// PLUGIN. IN THIS PLUGIN THE WP_EDITOR() FUNCTION REQUIRES WORDPRESS 3.3 
// OR ABOVE. ANYTHING LESS SHOWS A WARNING AND THE PLUGIN IS DEACTIVATED.                    
// ------------------------------------------------------------------------
/*
function requires_wordpress_version() {
	global $wp_version;
	$plugin = plugin_basename( __FILE__ );
	$plugin_data = get_plugin_data( __FILE__, false );

	if ( version_compare($wp_version, "3.3", "<" ) ) {
		if( is_plugin_active($plugin) ) {
			deactivate_plugins( $plugin );
			wp_die( "'".$plugin_data['Name']."' requires WordPress 3.3 or higher, and has been deactivated! Please upgrade WordPress and try again.<br /><br />Back to <a href='".admin_url()."'>WordPress admin</a>." );
		}
	}
}
add_action( 'admin_init', 'requires_wordpress_version' );
*/
// ------------------------------------------------------------------------
// PLUGIN PREFIX:                                                          
// ------------------------------------------------------------------------
// A PREFIX IS USED TO AVOID CONFLICTS WITH EXISTING PLUGIN FUNCTION NAMES.
// WHEN CREATING A NEW PLUGIN, CHANGE THE PREFIX AND USE YOUR TEXT EDITORS 
// SEARCH/REPLACE FUNCTION TO RENAME THEM ALL QUICKLY.
// ------------------------------------------------------------------------

// 'ulp_' prefix is derived from [p]plugin [o]ptions [s]tarter [k]it

// ------------------------------------------------------------------------
// REGISTER HOOKS & CALLBACK FUNCTIONS:
// ------------------------------------------------------------------------
// HOOKS TO SETUP DEFAULT PLUGIN 




// HANDLE CLEAN-UP OF OPTIONS WHEN
// PLUGIN IS DEACTIVATED AND DELETED, INITIALISE PLUGIN, ADD OPTIONS PAGE.
// ------------------------------------------------------------------------

// Set-up Action and Filter Hooks
register_activation_hook(__FILE__, 'ulp_add_defaults');
register_uninstall_hook(__FILE__, 'ulp_delete_plugin_options');
add_action('admin_init', 'ulp_init' );
add_action('admin_menu', 'ulp_add_options_page');
add_filter( 'plugin_action_links', 'ulp_plugin_action_links', 10, 2 );

// --------------------------------------------------------------------------------------
// CALLBACK FUNCTION FOR: register_uninstall_hook(__FILE__, 'ulp_delete_plugin_options')
// --------------------------------------------------------------------------------------
// THIS FUNCTION RUNS WHEN THE USER DEACTIVATES AND DELETES THE PLUGIN. IT SIMPLY DELETES
// THE PLUGIN OPTIONS DB ENTRY (WHICH IS AN ARRAY STORING ALL THE PLUGIN OPTIONS).
// --------------------------------------------------------------------------------------

// Delete options table entries ONLY when plugin deactivated AND deleted
function ulp_delete_plugin_options() {
	delete_option('ulp_options');
}

// ------------------------------------------------------------------------------
// CALLBACK FUNCTION FOR: register_activation_hook(__FILE__, 'ulp_add_defaults')
// ------------------------------------------------------------------------------
// THIS FUNCTION RUNS WHEN THE PLUGIN IS ACTIVATED. IF THERE ARE NO THEME OPTIONS
// CURRENTLY SET, OR THE USER HAS SELECTED THE CHECKBOX TO RESET OPTIONS TO THEIR
// DEFAULTS THEN THE OPTIONS ARE SET/RESET.
//
// OTHERWISE, THE PLUGIN OPTIONS REMAIN UNCHANGED.
// ------------------------------------------------------------------------------

// Define default option settings
function ulp_add_defaults() {
	$tmp = get_option('ulp_options');
    if(($tmp['chk_default_options_db']=='1')||(!is_array($tmp))) {
		delete_option('ulp_options'); // so we don't have to reset all the 'off' checkboxes too! (don't think this is needed but leave for now)
		$arr = array(	
						"title" => "This is the title of your page",
						"logo" => "",
						"background-image" => "",
						"headline" => "This is a sample headline",
						"description" => "<center>This is the description.  Put whatever you want here.</center>",
						"feedburner_address" => "",
						"drp_select_box" => "feedburner",
						"info_background_color" => "#",
						"my_background_color" => "#ffffff",
						"url-slug" => "landing-page",
						"headline_font_color" => "#000000",
						"description_font_color" => "#000000"
		);
		update_option('ulp_options', $arr);
	}
}

// ------------------------------------------------------------------------------
// CALLBACK FUNCTION FOR: add_action('admin_init', 'ulp_init' )
// ------------------------------------------------------------------------------
// THIS FUNCTION RUNS WHEN THE 'admin_init' HOOK FIRES, AND REGISTERS YOUR PLUGIN
// SETTING WITH THE WORDPRESS SETTINGS API. YOU WON'T BE ABLE TO USE THE SETTINGS
// API UNTIL YOU DO.
// ------------------------------------------------------------------------------

// Init plugin options to white list our options
function ulp_init(){
	register_setting( 'ulp_plugin_options', 'ulp_options', 'ulp_validate_options' );
}

// ------------------------------------------------------------------------------
// CALLBACK FUNCTION FOR: add_action('admin_menu', 'ulp_add_options_page');
// ------------------------------------------------------------------------------
// THIS FUNCTION RUNS WHEN THE 'admin_menu' HOOK FIRES, AND ADDS A NEW OPTIONS
// PAGE FOR YOUR PLUGIN TO THE SETTINGS MENU.
// ------------------------------------------------------------------------------

// Add menu page
function ulp_add_options_page() {
	add_options_page('Ultimate Landing Page Options', 'Ultimate Landing Page', 'manage_options', 'ultimate-landing-page', 'ulp_render_form');
}

// ------------------------------------------------------------------------------
// CALLBACK FUNCTION SPECIFIED IN: add_options_page()
// ------------------------------------------------------------------------------
// THIS FUNCTION IS SPECIFIED IN add_options_page() AS THE CALLBACK FUNCTION THAT
// ACTUALLY RENDER THE PLUGIN OPTIONS FORM AS A SUB-MENU UNDER THE EXISTING
// SETTINGS ADMIN MENU.
// ------------------------------------------------------------------------------

// Render the Plugin options form
function ulp_render_form() {
	?>
	<div class="wrap">
		
		<!-- Display Plugin Icon, Header, and Description -->
		<div class="icon32" id="icon-options-general"><br></div>
		<h2>Ultimate Landing Page</h2>
		<!-- <p>Choose your options</p> -->
		
		
	<div id="poststuff" class="metabox-holder has-right-sidebar">
		<div id="side-info-column" class="inner-sidebar">
			<div id="side-sortables" class="meta-box-sortables ui-sortable">
				<a target="_blank" href="http://www.thinklandingpages.com/landingpage/ultimate-landing-page-advanced-features/?utm_source=inside%20landing%20page%20plugin%20banner-to-feature-page&utm_medium=plugin&utm_campaign=inside%20landing%20page%20plugin%20banner-to-feature-page">
<img src="<?php echo plugins_url( 'upgrade.png' , __FILE__ ) ?>">
</a>
<br>
<br>
				
				<div class="postbox support-postbox">
				</div>	
				<div class="postbox like-postbox">
				</div>
				<div class="postbox rss-postbox">
				</div>
			</div>
		</div>
		<div id="post-body">
			<div id="post-body-content">
				<div class="meta-box-sortables ui-sortable">
		<!-- Beginning of the Plugin Options Form -->
		<form method="post" action="options.php">
			<?php settings_fields('ulp_plugin_options'); ?>
			<?php $options = get_option('ulp_options'); ?>

			<!-- Table Structure Containing Form Controls -->
			<!-- Each Plugin Option Defined on a New Table Row -->
			<table class="form-table">
				<!-- Preview link -->
				<tr>
					<th scope="row"><h3>Preview</h3></th>
					<td><a href="<?php echo '/'.$options['url-slug'] ?>" target="_blank">Preview Page</a>
					</td>
				</tr>
			
			<!-- coming soon -->
				<tr valign="top">
					<th scope="row"><h3>Coming Soon</h3></th>
					<td>
						<!-- First checkbox button -->
						<label><input name="ulp_options[coming_soon]" type="checkbox" value="1" <?php if (isset($options['coming_soon'])) { checked('1', $options['coming_soon']); } ?> />Enable Coming Soon</label><br /><span style="color:#666666;margin-left:2px;float: left;width: 440px;">This will cause all URLs to load this screen for non-logged in users. Only enable coming soon page if you want the whole site to go to this page.  If you just want a landing page, leave this unchecked and put a url slug in below.</span>
					</td>
				</tr>

			
			
			<!-- Title -->
				<tr>
					<th scope="row"><h3>Title</h3></th>
					<td>
						<input name="ulp_options[title]" size="80" type='text' value="<?php echo stripslashes($options['title']); ?>" /><br /><span style="color:#666666;margin-left:2px;">This is the html page title.  It will appear at the top of the browser.</span>
					</td>
				</tr>
				
			<!-- URL -->
				<tr>
					<th scope="row"><h3>URL Slug</h3></th>
					<td>
						<input name="ulp_options[url-slug]" size="50" type='text' value="<?php echo $options['url-slug']; ?>" /><br /><span style="color:#666666;margin-left:2px;">No spaces, use hyphens.  This will be appended to your domain and be the permalink for your landing page (http://www.yoursite.com/the-url-slug)</span>
					</td>
				</tr>
			
			
			
			<tr valign="top">
				<th scope="row"><h3>Logo</h3></th>
				<td><label for="logo">
				<input id="logo" type="text" size="36" name="ulp_options[logo]" value="<?php echo $options['logo']; ?>" />
				<input id="upload_logo_button" type="button" value="Upload Logo" />
				<br />Enter an URL or upload an image for your logo.
				</label></td>
			</tr>
			
			
			<!-- color picker -->
			<tr valign="top">
			  <th scope="row">
			    <h3>Background Color</h3>
			    <small>Click on each field to display the color picker. Click again to close it.</small>
			  </th>
			  <td>
			    <label for="color"><input type="text" id="color" name="ulp_options[my_background_color]" value="<?php echo $options['my_background_color']; ?>" /> Pick background color</label>
			    <div id="ilctabscolorpicker"></div>
			  </td>
			</tr>
			
			<script type="text/javascript">
			 
			  jQuery(document).ready(function() {
			    jQuery('#ilctabscolorpicker').hide();
			    jQuery('#ilctabscolorpicker').farbtastic("#color");
			    jQuery("#color").click(function(){jQuery('#ilctabscolorpicker').slideToggle()});
			  });
			 
			</script>

<!-- end color picker -->



			<!-- Enable info background -->
				<tr valign="top">
					<th scope="row"><h3>Show Info Background Color</h3></th>
					<td>
						<!-- Enable info background checkbox button -->
						<label><input name="ulp_options[show_info_background_color]" type="checkbox" value="1" <?php if (isset($options['show_info_background_color'])) { checked('1', $options['show_info_background_color']); } ?> />Enable</label><br />
						<span style="color:#666666;margin-left:2px;">This is a semi-transparent box that will appear behind the text.</span>
					</td>
				</tr>

			<!-- color picker -->
<!-- comment out for now
			<tr valign="top">
			  <th scope="row">
			    <h3>Information Background Color</h3>
			    <small>Click on each field to display the color picker. Click again to close it.</small>
			  </th>
			  <td>
			    <label for="info-color"><input type="text" id="info-color" name="ulp_options[info_background_color]" value="<?php echo $options['info_background_color']; ?>" /> Pick background color</label>
			    <div id="infocolorpicker"></div>
			  </td>
			</tr>
			
			<script type="text/javascript">
			 
			  jQuery(document).ready(function() {
			    jQuery('#infocolorpicker').hide();
			    jQuery('#infocolorpicker').farbtastic("#info-color");
			    jQuery("#info-color").click(function(){jQuery('#infocolorpicker').slideToggle()});
			  });
			 
			</script>
-->
<!-- end color picker -->


			<!-- Background Image -->
			<tr valign="top">
				<th scope="row"><h3>Background Image</h3></th>
				<td><label for="background-image">
				<input id="background-image" type="text" size="36" name="ulp_options[background-image]" value="<?php echo $options['background-image']; ?>" />
				<input id="background_image_button" type="button" value="Upload Background Image" />
				<br />Enter an URL or upload an image for your background.
				</label></td>
			</tr>
			<!-- End Background Image -->

				<!-- Textbox Control -->
				<tr>
					<th scope="row"><h3>Headline</h3></th>
					<td>
						<input type="text" size="130" name="ulp_options[headline]" value="<?php echo stripslashes($options['headline']); ?>" />
					</td>
				</tr>
				
				<tr>
					<th scope="row"><h3>Headline Font</h3></th>
					<td>
						<select name='ulp_options[headline_font]'>
							<?php createFonts($options, 'headline_font'); ?>	
						</select>
					</td>
				</tr>
				
				<!-- Headline Font Color -->
				<tr valign="top">
					<th scope="row"><h3>Headline Font Color</h3></th>
					<td>
						<!-- First radio button -->
						<label><input name="ulp_options[headline_font_color]" type="radio" value="#000000" <?php checked('#000000', $options['headline_font_color']); ?> />Black</label><br />

						<!-- Second radio button -->
						<label><input name="ulp_options[headline_font_color]" type="radio" value="#ffffff" <?php checked('#ffffff', $options['headline_font_color']); ?> />White</label><br />
					</td>
				</tr>

						
				
				

				<!-- Text Area Using the Built-in WP Editor -->
				<tr>
					<th scope="row"><h3>Description</h3></th>
					<td>
						<?php
							$args = array("textarea_name" => "ulp_options[description]");
							wp_editor( $options['description'], "ulp_options_description", $args );
						?>
						<br /><span style="color:#666666;margin-left:2px;"></span>
					</td>
				</tr>
				<!-- Description font color -->
				<tr>
					<th scope="row"><h3>Description Font</h3></th>
					<td>
						<select name='ulp_options[description_font]'>
							<?php createFonts($options, 'description_font'); ?>	
						</select>
					</td>
				</tr>
				
				<!-- Description Font Color -->
				<tr valign="top">
					<th scope="row"><h3>Description Font Color</h3></th>
					<td>
						<!-- First radio button -->
						<label><input name="ulp_options[description_font_color]" type="radio" value="#000000" <?php checked('#000000', $options['description_font_color']); ?> />Black</label><br />

						<!-- Second radio button -->
						<label><input name="ulp_options[description_font_color]" type="radio" value="#ffffff" <?php checked('#ffffff', $options['description_font_color']); ?> />White</label><br />
					</td>
				</tr>

				
				
				<!-- Textbox Control -->
				<tr>
					<th scope="row"><h3>Feedburner Id</h3></th>
					<td>
						<input type="text" size="50" name="ulp_options[feedburner_address]" value="<?php echo $options['feedburner_address']; ?>" /><br /><span style="color:#666666;margin-left:2px;">Only needed if you are using feedburner to collect email addresses.</span>
					</td>
				</tr>


				<!-- Select Drop-Down Control -->
				<tr>
					<th scope="row"><h3>Email Management</h3></th>
					<td>
						<select name='ulp_options[drp_select_box]'>
							<option value='feedburner' <?php selected('feedburner', $options['drp_select_box']); ?>>FeedBurner</option>
							<option disabled='disabled' value='aweber' <?php selected('aweber', $options['drp_select_box']); ?>>Aweber</option>
							<option disabled='disabled' value='mailchimp' <?php selected('mailchimp', $options['drp_select_box']); ?>>Mailchimp</option>
							
						</select>
						<span style="color:#666666;margin-left:2px;">Aweber, MailChimp, Constant Contact, GetResponse, and other email list management software are supported in Ultimate Landing Page Advanced and Pro Versions</span></br>
						<span style="color:#666666;margin-left:2px;"><a href="http://www.thinklandingpages.com/landingpage/ultimate-landing-page-advanced-features/?utm_source=inside%20landing%20page%20plugin%20inline-to-feature-page&utm_medium=plugin&utm_campaign=inside%20landing%20page%20plugin%20inline-to-feature-page">Free Upgrade to the Advanced Lite version or get a full version</a></span>
					</td>
				</tr>

				<tr><td colspan="2"><div style="margin-top:10px;"></div></td></tr>
				<tr valign="top" style="border-top:#dddddd 1px solid;">
					<th scope="row">Database Options</th>
					<td>
						<label><input name="ulp_options[chk_default_options_db]" type="checkbox" value="1" <?php if (isset($options['chk_default_options_db'])) { checked('1', $options['chk_default_options_db']); } ?> /> Restore defaults upon plugin deactivation/reactivation</label>
						<br /><span style="color:#666666;margin-left:2px;">Only check this if you want to reset plugin settings upon Plugin reactivation</span>
					</td>
				</tr>
			</table>
			<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
		</form>
				</div>
			</div> <!-- end post-body-content -->
		</div> <!-- end post-body -->

		
	</div><!-- end poststuff -->
	</div><!-- end wrap -->
	<?php	
}

// Sanitize and validate input. Accepts an array, return a sanitized array.
function ulp_validate_options($input) {
	 // strip html from textboxes
	$input['headline'] =  wp_filter_nohtml_kses($input['headline']); // Sanitize textarea input (strip html tags, and escape characters)
	$input['title'] =  wp_filter_nohtml_kses($input['title']); // Sanitize textbox input (strip html tags, and escape characters)
	$input['url-slug'] =  wp_filter_nohtml_kses($input['url-slug']); // Sanitize textbox input (strip html tags, and escape characters)
	return $input;
}

// Display a Settings link on the main Plugins page
function ulp_plugin_action_links( $links, $file ) {

	if ( $file == plugin_basename( __FILE__ ) ) {
		$ulp_links = '<a href="'.get_admin_url().'options-general.php?page=ultimate-landing-page.php">'.__('Settings').'</a>';
		// make the 'Settings' link appear first
		array_unshift( $links, $ulp_links );
	}

	return $links;
}

// ------------------------------------------------------------------------------
// SAMPLE USAGE FUNCTIONS:
// ------------------------------------------------------------------------------
// THE FOLLOWING FUNCTIONS SAMPLE USAGE OF THE PLUGINS OPTIONS DEFINED ABOVE. TRY
// CHANGING THE DROPDOWN SELECT BOX VALUE AND SAVING THE CHANGES. THEN REFRESH
// A PAGE ON YOUR SITE TO SEE THE UPDATED VALUE.
// ------------------------------------------------------------------------------


add_action('template_redirect', 'render_landing_page');
add_action('template_redirect', 'render_coming_soon');
//add_action('template_include', 'render_landing_page');

function redirect_user_now(){
}
function render_landing_page(){
$my_options = get_option('ulp_options');

global $wp;
//$mypage = $wp->query_vars["pagename"] == $my_options['url-slug'];
//	if($mypage){
//	if(preg_match('/^\/\b'.$my_options['url-slug'].'\b$/', $_SERVER['REQUEST_URI'], $match)){
      if(preg_match('#^/'.$my_options['url-slug'].'$#', $_SERVER['REQUEST_URI'], $match)){
		$file = plugin_dir_path(__FILE__).'template/template1.php';
	                        //include($file);
	                        include_wordpress_template($file);
		//exit;
	}
}

function include_wordpress_template($t) {
    global $wp_query;
    
    if ($wp_query->is_404) {
        $wp_query->is_404 = false;
        $wp_query->is_archive = true;
        $wp_query->is_post_type_archive = true;
    }
    
    header("HTTP/1.1 200 OK");
    include($t);
}


function ulp_add_content($text) {
	$options = get_option('ulp_options');
	$select = $options['drp_select_box'];
	$text = "<p style=\"color: #777;border:1px dashed #999; padding: 6px;\">Select box Plugin option is: {$select}</p>{$text}";
	return $text;
}

add_action('init', 'ilc_farbtastic_script');
function ilc_farbtastic_script() {
  wp_enqueue_style( 'farbtastic' );
  wp_enqueue_script( 'farbtastic' );
}



//ajax call

add_action( 'init', 'my_script_enqueuer' );
	 
	function my_script_enqueuer() {
	   wp_register_script( "my_script", plugin_dir_url(__FILE__).'template/template1Script.js', array('jquery') );
	   //wp_localize_script( 'my_voter_script', 'myAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));       
	 
	   wp_enqueue_script( 'jquery' );
	   wp_enqueue_script( 'my_script' );
	 
	}





//image uploader
function my_admin_scripts() {
wp_enqueue_script('media-upload');
wp_enqueue_script('thickbox');
wp_register_script('my-upload', plugin_dir_url(__FILE__).'template/template1Script.js', array('jquery','media-upload','thickbox'));
wp_enqueue_script('my-upload');
}
 
function my_admin_styles() {
wp_enqueue_style('thickbox');
}
 
//if (isset($_GET['page']) && $_GET['page'] == 'myplugin/myplugin.php') {
add_action('admin_print_scripts', 'my_admin_scripts');
add_action('admin_print_styles', 'my_admin_styles');
//}


function render_coming_soon() {
			$my_options = get_option('ulp_options');

	            if(!is_admin()){
	                if(!is_feed()){
	                    if ( !is_user_logged_in() && (isset($my_options['coming_soon']))) {
	                        $file = plugin_dir_path(__FILE__).'template/template1.php';
	                        //include($file);
	                        include_wordpress_template($file);
	                    }
	                }
	            }
        }

function getFonts(){
$fonts = array('ABeeZee','Aclonica','Alef','Alegreya Sans','Allan','Allerta','Allerta Stencil','Amaranth','Annie Use Your Telescope','Anonymous Pro','Anton','Architects Daughter','Arimo','Artifika','Arvo','Asset','Astloch','Audiowide','Bangers','Bentham','Bevan','Bigelow Rules','Bigshot One','Bowlby One SC','Brawler','Buda','Cabin','Cabin Sketch','Calligraffitti','Candal','Cantarell','Cardo','Carter One','Caudex','Cedarville Cursive','Ceviche One','Cherry Cream Soda','Chewy','Coda','Coming Soon','Copse','Corben','Cousine','Covered By Your Grace','Crafty Girls','Crimson Text','Crushed','Cuprum','Damion','Dancing Script','Dawning of a New Day','Didact Gothic','Diplomata SC','Droid Sans','Droid Sans Mono','Droid Serif','EB Garamond','Expletus Sans','Fontdiner Swanky','Forum','Francois One','Gafata','Gabriela','Geo','Goblin One','Goudy Bookletter','Gravitas One','Gruppo','Hammersmith One','Holtwood One SC','Homemade Apple','IM Fell','Inconsolata','Indie Flower','Irish Grover','Josefin Sans','Josefin Slab','Judson','Jura','Just Another Hand','Just Me Again Down Here','Kameron','Kenia','Kite One','Kranky','Kreon','Kristi','La Belle Aurore','Lato','League Script','Lekton', 'Libre Baskerville','Limelight','Lobster','Lobster Two','Lora','Love Ya Like A Sister','Loved by the King','Luckiest Guy','Maiden Orange','Mako','Maven Pro','Meddon','MedievalSharp','Megrim','Merriweather','Metrophobic','Michroma','Miltonian','Molengo','Monofett','Montez','Mountains of Christmas','Muli','Neucha','Neuton','News Cycle','Nobile','Nova','Nunito','OFL Sorts Mill Goudy TT','Old Standard TT','Open Sans','Orbitron','Oswald','Over the Rainbow','PT Sans','PT Serif','Pacifico','Patrick Hand','Paytone One','Permanent Marker','Philosopher','Play','Playfair Display','Podkova','Prosto One','Puritan','Quattrocento','Quattrocento Sans','Radley','Raleway','Redressed','Reenie Beanie','Revalia','Roboto','Rock Salt','Rokkitt','Ruslan Display','Schoolbell','Shadows Into Light','Shanti','Sigmar One','Sintony','Sniglet','Six Caps','Slackey','Smythe','Sniglet','Sofadi One','Special Elite','Stardos Stencil','Sue Ellen Francisco','Sunshiney','Swanky and Moo Moo','Syncopate','Tangerine','Tauri','Tenor Sans','Terminal Dosis Light','The Girl Next Door','Tinos','Ubuntu','Ultra','UnifrakturCook','UnifrakturMaguntia','Unkempt','VT','Varela','Vibur','Vollkorn','Waiting for the Sunrise','Wallpoet','Walter Turncoat','Wellfleet','Wire One','Yanone Kaffeesatz','Zeyada');
return $fonts;
}

function createFonts($options, $element){
	$fonts = getFonts();
	foreach($fonts as $v){		
		echo "<option value='".$v."'".selected($v, $options[$element]).">".$v."</option>";
	}
}

function getDescriptionFonts($options){
}

