<?php
/*
Plugin Name: CALS Page Copycat
Description: Allows pages to copy the content of other on rendering.
Version: 0.6
Author: Vidal Quevedo
Author URI: http://cals.wisc.edu
*/

/*README

Copycat: (noun) someone who copies the words or behavior of another.

The CALS Page Copycat plugin allows Wordpress pages to "copy" the content of other pages, as a workaround for WP's inability to assign two parent pages to the same child page. This way, two child pages with different parent pages can have the same content by having one of them "copy" the content of the other. This helps reduce redundancy (content only needs to be edited in one page and not both) and helps to physically keep the page hierarchy of complex websites.

Example:

-Parent 1
--child a
--child b
--child n..

-Parent 2
--child d
--child e(a) <--copies the content of child page "a", while still remaining a child of Parent 2 



USES:


INSTALLATION INSTUCTIONS: (last updated 10/19/10)

What you will need:
	1.- A valid Google CSE account
	2.- A WP page to display search results

1.- Google CSE
	a.- Register for a Google CSE account at http://www.google.com/cse/. Follow the 3 basic steps to set up the search engine.
	b.- Get the Search Engine's unique ID (Go to Google Custom Search home > Manage your existing search engines > [Your Engine's]
		Control Panel
	c.- Copy your Search Engine's Unique ID (e.g. '016039371683713681917:pyykxxxx-xx');
	
2.- Search Results page
	a.- Login to Wordpress
	b.- Go to Pages > Add New to add a new page for your search resuls. Give an identifiable title (e.g. "Search Results)
	c.- Publish the page

3.- Setting up the CALS Google CSE plugin
	a.- Download plugin to your local plugins/ directory and upload it to your server
	b.- Go to WP Admin > Plugins to activate the plugin
	c.- Go to Settings > CALS Google Custom Search
	d.- Paste the Search Engine Unique ID in the indicated field
	e.- Select a Search Results Page where search results will be displayed. 


NOTE ON CONFIGURING THE LOOK AND FEEL OF YOUR GOOGLE CSE IMPLEMENTATION
The look and feel of the Search Form can be altered by adding new CSS rules to your style.css file in your WP theme. The Search Results, however, are more easily customized by using the available tools in the "Look and Feel" section of the Google CSE's Control Panel


*/


/*TODO:
- Add help text
- On clone pages
--- Disable meta boxes using disable_meta_boxes() wp function and not jQuery
- On Page list
--- Fade link of clone page next to "See ---- page" message
- Advanced: cloning pages from a subsite (e.g. cals.wisc.edu cloning a page from students.cals.wisc.edu)
*/

/*LOG:

v 0.6:
- 04/12/11: Modifications to suit WP 3.1: changed jQuery code to show hihglight message usgin newly ID'd elements in WP 3.1: a): '#post-' instead of "#page-" on Page list page, and b) "#postdivrich" instead of "#title" on editor


*/
if (!class_exists(CALSPageCopycat)){
	
	class CALSPageCopycat {
	
		function CALSPageCopycat(){
							
			//Load actions and filters according to context
			
			if(is_admin()){
				
				//run back-end stuff
				
					//Add Page Copycat meta box to page editor
					add_action('add_meta_boxes', array($this, 'calspagecopycat_add_custom_box'));
				
					//On post save, save plugin's data
					add_action('save_post', array($this, 'calspagecopycat_save_postdata'));	
					

					//Add "this page is a clone" message to list of wp messages. Might just do it via jQuery instead
					//add_filter('post_updated_messages', array($this, 'add_messages'), 1);
				
					
					
					//add_filter('the_editor_content', array($this, 'replace_editor_content'));

					//indicate users which pages are cloning other pages on "Pages" list
					add_action('calspagecopycat_print_jquery_on_ready', array($this, 'highlight_clone_pages'));

					//indicate users which pages are cloning other pages on edit.php
					add_action('calspagecopycat_print_jquery_on_ready', array($this, 'highlight_cloned_pages'));
										
					//add plugin's jquery scripts to admin footer
					add_action('admin_footer', array($this, 'print_jquery_scripts'));


		} else {
				
				//run front-end stuff
				
					add_filter('the_content', array($this,'replace_page_content'));
					
					add_filter('get_the_excerpt', array($this,'replace_page_excerpt'),1);				
					
					//If cals_custom_title_url plugin is active, enable permalink replacement
					//so the custom title url of the original post can be used to replace 
					//the permalink of the cloning page
					if (class_exists("CALSCustomPostTitleURL")){
						add_filter('the_permalink', array($this, 'replace_page_permalink'));
					}
					
					//add filter/action that updates the $messages variable 	
										
					//SCRIPTS
						
						//try to enqueue jquery in footer
						wp_enqueue_script('jquery', '','','',true); 
					
						//add plugin's jquery scripts to wp footer					
						//add_action('wp_footer', array($this, 'print_jquery_scripts'));
			}
		}
		
		function add_messages($messages){
			$messages['page'][] = 'hello';
			return $messages;
		}
		
		
		function replace_editor($the_editor){
			global $post;
			$this->original_page_id = get_post_meta($post->ID, '_calspagecopycat_original_page_id', true);
			
			//if current page is cloning another, replace editor content with message
			if ($this->original_page_id !=''){
				
				
				//remove media buttons
				remove_action('media_buttons', 'media_buttons');
				
				$the_editor = '<div id="calspagecopycat_editor">
									<h3>The post editor has been disabled</h3>
									<p>This page is a clone of the [...] page.</p>
							   </div>';
			}
		
			return $the_editor;
		}
		
		function blank_media_buttons(){
			return '';
		}
		
		

		function replace_editor_content2($content){
			global $post;
			$this->original_page_id = get_post_meta($post->ID, '_calspagecopycat_original_page_id', true);
			
			//if current page is cloning another, replace editor content with message
			if ($this->original_page_id !=''){
				$original_page = get_page($this->original_page_id);
				$this->original_page_title = $original_page->post_title;
				$content = '<span style="border:1px solid red;">This page is a clone of the <em><strong>'.$this->original_page_title.'</strong></em> page. Please edit that page instead to modify its content. Thank you.</span>';
			
				 
			//add jquery code needed to disable textarea#content (the Editor's main box)
			//add_action('calspagecopycat_print_jquery_on_ready', array($this, 'admin_scripts_on_ready'));
			
			}
		
			return $content;
		}
		
		function admin_scripts_on_ready($the_editor){
        	//$output = '<div><h3>Page Editor Disabled</h3><p>This page is a clone of the <em><strong>'.$this->original_page_title.'</strong></em> page. Please edit that page instead to modify its content. Thank you.</p></div>';
			//echo 'jQuery("#postdivrich").html("'.$output.'");';

			$the_editor = '<div >hello</div>';
			
			return $the_editor;
			
		
		}
		
		
		/* Replaces content of copycat page with that of the original page
		 *
		 * @param string $content
		 * @uses global $post
		*/
		function replace_page_content($content){
			
			global $post;
			//hold original $post object in temp var 
			$tmp_post = $post;

			$this->original_page_id = get_post_meta($post->ID, '_calspagecopycat_original_page_id', true);
			
			if (is_page() && $this->original_page_id!=''){
				$original_post = get_posts('include='.$this->original_page_id.'&post_type=page');
				foreach($original_post as $post){
					setup_postdata($post);
					//$content = $post->post_content;				
					$content = apply_filters('the_content', get_the_content());
				}
			}
			
			//restore original $post object so it can be used on rest of script
			$post = $tmp_post;
			
			return $content;
		}

		/* Replaces excerpt of copycat page with that of the original page (if page excerpts are
		 * are supported and available)
		 *
		 * @param string $excerpt
		 * @uses global $post
		*/
		function replace_page_excerpt($output){

			global $post;
			
			//hold original $post object in temp var 
			$tmp_post = $post;
			
			$this->original_page_id = get_post_meta($post->ID, '_calspagecopycat_original_page_id', true);
			
			if (is_page() && $this->original_page_id!=''){
				$original_page = get_posts('include='.$this->original_page_id.'&post_type=page');
				
				foreach($original_page as $page){
					$output = $page->post_excerpt;				
				}
			}

			//restore original $post object so it can be used on rest of script
			$post = $tmp_post;
			
			return $output;
		}


		/* Replaces the permalink of a copycat page with the custom title url of the original page (if
		 * cals_custom_post_title_url is enabled)
		 *
		 * This function is only executed if CALSCustomPostTitleURL class available thorugh the 
		 * cals_custom_post_title_url plugin
		 *
		 *
		 * @uses global $post
		 * @uses global $cals_custom_title_url object, defined by cals_custom_title_url plugin
		*/
		function replace_page_permalink($post_link){
			
			//echo 'b.-'.$post_link;
			global $post;
						
			//get the $cals_custom_title_url 
			//so we can use it
			global $cals_custom_title_url;
			if (!isset($cals_custom_title_url)){
				$cals_custom_title_url = new CALSCustomPostTitleURL;
			}			
			//hold original $post object in temp var 
			
			//echo 'pid2:'.$post->ID;
			
			$tmp_post = $post;
			
			$this->original_page_id = get_post_meta($post->ID, '_calspagecopycat_original_page_id', true);
			
			//if($this->original_page_id==''){ echo $post->ID.' doens\' have original page<br><br>';} else {echo $post->ID.' has yes original page<br><br>';} 
			
			//echo 'there'.$post->ID.$this->original_page_id;
			
			if (is_page() && $this->original_page_id!=''){
				$original_page = get_posts('include='.$this->original_page_id.'&post_type=page');
				foreach($original_page as $post){
					setup_postdata($post);
					$post_link = $cals_custom_title_url->calscustomtitleurl_replace_permalink($post_link);
				}
			}

			//restore original $post object so it can be used on rest of script
			$post = $tmp_post;
			
			//unset $cals_custom_title_url
			unset($cals_custom_title_url);
			
			return $post_link;
		}


		
		/* Adds Page Copycat meta box to page editor
		 *
		 *
		*/
		function calspagecopycat_add_custom_box(){
			if ( post_type_supports('page', 'page-attributes') ){
				add_meta_box('calspagecopycat', __('Page Cloning'), array($this,'calspagecopycat_inner_custom_box'), 'page', 'side', 'core');
			}
		}
		
		
		/* Populates Page Copycat meta box in page editor
		 *
		 * Based on function page_attributes_meta_box().
		 *
		 *
		 * @param object $post
		*/
		function calspagecopycat_inner_custom_box($post){
			
			$this->original_page_id = get_post_meta($post->ID, '_calspagecopycat_original_page_id', true);
			
			$post_type_object = get_post_type_object($post->post_type);
			if ( $post_type_object->hierarchical ) {
				$pages = wp_dropdown_pages(array('post_type' => $post->post_type, 'exclude_tree' => $post->ID, 'selected' => $this->original_page_id, 'name' => '_calspagecopycat_original_page_id', 'show_option_none' => __('(none)'), 'sort_column'=> 'menu_order, post_title', 'echo' => 0));
				if ( ! empty($pages) ) {
					
				  	// Use nonce for verification
				    wp_nonce_field( plugin_basename(__FILE__), 'calspagecopycat_noncename' );?>
                  
					<p id="calspagecopycat_help" class="hide-if-no-js">
                        	This option allows you to make the current page "clone" the content another page in Wordpres, making them identical when viewed.
                    	<h4>This page copies its content from:</h4>                     
					<label class="screen-reader-text" for="_calspagecopycat_original_page_id"><?php _e('This page copies its content from:') ?></label>
					<?php echo $pages; ?>
                   		
                        <?php 
							/*echo '<h4>Pages copying content from this one:</h4>';
							$cloners = get_posts('post_type=page&meta_key=_calspagecopycat_original_page_id&meta_value='.$post->ID);
							echo 'meta_key=_calspagecopycat_original_page_id&meta_value='.$post->ID;
							print_r($cloners);*/?>
                    </p>
		<?php	} // end empty pages check
			} // end hierarchical check.
		}
		
		/* Saves the plugin's custom data when the post is saved 
		 *
		 *
		 * @param int $post_id
		 * @reference http://codex.wordpress.org/Function_Reference/add_meta_box#Example
		*/
		function calspagecopycat_save_postdata($post_id){
		  
		  // Verify this came from the our screen and with proper authorization,
		  // because save_post can be triggered at other times
		
		  if ( !wp_verify_nonce( $_POST['calspagecopycat_noncename'], plugin_basename(__FILE__) )) {
			return $post_id;
		  }
		
		  // Verify if this is an auto save routine. If it is our form has not been submitted, so we dont want
		  // to do anything
		  if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
			return $post_id;
		
		  
		  // Check permissions to edit pages
		  if ( 'page' == $_POST['post_type'] ) {
			if ( !current_user_can( 'edit_page', $post_id ) )
			  return $post_id;
		  } 
		
		  // OK, we're authenticated: we need to find and save the data
		  $original_page_id = $_POST['_calspagecopycat_original_page_id'];
		
		  // save original page id
		  update_post_meta($post_id, '_calspagecopycat_original_page_id', $original_page_id); 
		
		}
		
		
		/* If current page is a clone, defines jQuery code to show message asking to edit original page instead.
		 * 
		 * Disables editor and other meta boxes using jQuery. NOTE: Could have done using disable_meta_box()
		 * function, but need to determine first $original_page_id before the 'admin_menu' hook is invoked 
		 *
		 *
		 * 
		 * @uses do_action('admin_footer')
		 * @uses global array $posts
		 * @uses global string $parent_file, defined in edit.php and post.php
		*/
		
		function highlight_clone_pages(){
			global $post, $posts;
			global $parent_file;
			
			$uri = explode('/', $_SERVER['REQUEST_URI']);
			
			$current_admin_page = $uri[count($uri)-1];
			
			
			if (substr_count($current_admin_page, 'post_type=page')==1 && substr_count($current_admin_page, 'edit.php?')==1){ // We're on "Pages" main page 

					foreach($posts as $post){
						
						$original_page_id = get_post_meta($post->ID, '_calspagecopycat_original_page_id', true);
						
						if( $original_page_id != ''){
							
							$original_page = get_page($original_page_id);
					 
							//add the following jQuery code to plugin's ready() wrapper
							 ?>
							
							//highlight which pages are cloning other pages
							<?php
								$original_page_path = $this->get_page_path($original_page_id);
							?>

							$('#post-<?php echo $post->ID;?> strong').append('<span class="description"> (See <a href="post.php?post=<?php echo $original_page_id;?>&amp;action=edit" title=\'Edit "<?php echo $original_page_path;?>" page\'><?php 
										echo $original_page_path;
										?></a> page to edit.)</span>').css('font-weight', 'normal');
                                        
                                       $('#post-<?php echo $post->ID;?> a.row-title').css('color', '#666');
							
							
						<?php }
					}
				
				} else if(strstr($current_admin_page, 'post.php?post='.$post->ID.'&action=edit')){ // WE are on "Edit Page" page 
				
					
					$original_page_id = get_post_meta($post->ID, '_calspagecopycat_original_page_id', true);
					
					if( $original_page_id != ''){
							
						$original_page_path = $this->get_page_path($original_page_id);
					
						//disable editor and add message?>
                    
                    
                	    $('#postdivrich').html('<div class="updated below-h2">' +
                    					   		'<p><strong>This page is a clone of the <a href="post.php?post=<?php echo $original_page_id;?>&amp;action=edit" title=\'Edit "<?php echo $original_page_path;?>"\'><?php echo $original_page_path;?></a> page.</strong></p>' +
                                            	'<p>For that reason, the page editor has been disabled. Please edit the <a href="post.php?post=<?php echo $original_page_id;?>&amp;action=edit" title=\'Edit "<?php echo $original_page_path;?>"\'><?php echo $original_page_path;?></a> page instead to make updates. </p>' +
                    	                       '</div>');
                        
                        //hide unnecessary meta boxes
                        $('#calscustomtitleurl, #postcustom, #normal-sortables, #commentsdiv, #authordiv, #revisionsdiv, #postexcerpt').hide();
										
			<?php } 
				
				}	
				
				
		
		}
		

		function highlight_cloned_pages(){
			
			global $post;
			
			/*$uri = explode('/', $_SERVER['REQUEST_URI']);
			$current_admin_page = $uri[count($uri)-1];
		
			if(strstr($current_admin_page, 'post.php?post='.$post->ID.'&action=edit'))*/
			
			//only run on post editor page
			
			if (strstr($_SERVER['REQUEST_URI'], 'post-new.php') || (get_post_type($_GET['post']) == 'page' && strstr($_SERVER['REQUEST_URI'], 'post.php') && $_GET['action']=='edit'))			
			
			{ // WE are on "Edit Page" page 			
				$pages = get_posts('post_type=page&meta_key=_calspagecopycat_original_page_id&meta_value='.$post->ID);				
				if(count($pages)>0){
					foreach($pages as $page){
						$output.= '<li>- <a href="post.php?post='.$page->ID.'&amp;action=edit" title="Edit '.$this->get_page_path($page->ID).'">'.$this->get_page_path($page->ID).'</a></li>';
					}
					
					
					//print JQuery code?>
					$('#postdivrich').before('<div class="updated below-h2"><p><strong>Attention: </strong></p><p>This page is being cloned by the following page(s): <ul><?php echo $output; ?></ul></p><p><br />Please note that any changes made to this page will be reflected on the page(s) listed above.<br /></p>');
                    
			<?php
            	}
            } 
			
			
		}
			
			
		/* Adds action hooks to which all jQuery scripts in the plugin can be added (either in admin 
		 * or outside of it.
		 *
		 * - The 'calspagecopycat_print_jquery_on_ready' action hook can be used to add jQuery
		 *	 code to be executed within the jQuery(document).ready(function($){} wrapper.
		 *   NOTE: the code included on ready() can use the $ identifier.
		 *
		 *
		 * - The 'calspagecopycat_print_jquery_regular' action hook can be user to add jQuery code
		 *   that doesn't need to be executed on load.
		 *
		 *	
		*/
		function print_jquery_scripts(){?>
			<script type="text/javascript">
        		//[Code from CALSPageCopycat plugin]
				jQuery(document).ready(function($){
					<?php do_action('calspagecopycat_print_jquery_on_ready');?>
				});
					<?php do_action('calspagecopycat_print_jquery_regular');?>
				//[End of Code CALSPageCopycat plugin]
            </script>
		<?php }
	
	
		/**
		 * Get page path (e.g. 'Alumni/Departments/')
		 *
		 *
		 * @param $page_id int the id of the page 
		 * @param $escape bool whether escape returned path string (e.g.  'Alumni\/Departments\/')
		*/ 
		function get_page_path($page_id, $escape = true){
			$permalink = explode('/',get_permalink($page_id));
			$permalink = array_map('ucwords',$permalink);
			$page_path = implode('/',array_slice($permalink,3));
			
			if($escape==true){
				$page_path = addslashes($page_path);
			}
			
			return $page_path;
		}
	
	} //end of CALSPageCopycat class definition
} 

//Create $cals_page_copycat object
if(class_exists("CALSPageCopycat")){	
	$cals_page_copycat = new CALSPageCopycat();
}
?>