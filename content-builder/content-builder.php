<?php
/*
Plugin Name: Content Builder
Plugin URI: http://cals.wisc.edu/developers/
Description: Create a wide array of page layouts, and it is simple as drag & drop.
Version: 1.0.0
Author: Al Nemec
Author URI: http://alnemec.com
*/


if ( !class_exists('ContentBuilderWP') ) {

	if( !function_exists('pa') ) {
		function pa($a) { echo "<pre>".print_r($a, true)."</pre>"; }
	}

	final class ContentBuilderWP {

		/**
		 * Class instance
		 * @var	ContentBuilderWP
		 */
		private static $instance;

		/**
		 * Content Builder
		 * @var ContentBuilder
		 */
		public 	$builder = null;

		/**
		 * Enable builder at listed post types
		 * @var array
		 */
		public	$postTypes = array('page','post');

		/**
		 * Is builder enabled
		 * @var bool
		 */
		public	$builderEnabled = true;

		/**
		 * Singleton pattern
		 * @static
		 * @return 	ContentBuilderWP
		 */
		public static function getInstance() {
			if (!isset(self::$instance)) {
				$class = __CLASS__;
				self::$instance = new $class();
			}
			return self::$instance;
		}

		/**
		 * 	Ajax call
		 */
		public function ajax() {

			$request = array();
			$request['action'] = (isset($_REQUEST['action'])?$_REQUEST['action']:null);
			$request['block'] = (isset($_REQUEST['block'])?$_REQUEST['block']:null);
			$request['params'] = isset($_REQUEST['params'])?$_REQUEST['params']:array();

			if( $request['action'] == 'css' ) {
				header("Content-type: text/css");
				$theme = wp_remote_get( plugin_dir_url(__FILE__).'theme/theme.css' );
				$css = $theme['body'];
				$css = preg_replace('/\n([\#\_0-9a-z\:\(\[\]\)\-\=\"\*\.]+)/', "\n.cb-theme $1", $css );
				$css = preg_replace('/url\("..\/img\//', 'url("'.plugin_dir_url(__FILE__).'img/', $css );
				$css .= '#TB_overlay{z-index:10000;}#TB_window{z-index: 10002;}#TB_load{z-index:10003;}#TB_HideSelect {z-index:9999;}';
				$css .= '.post-edit-link{left:-40px!important;top:20px!important;}#main{padding-top:0;}.entry-content,.singular.page .hentry{padding-top:20px;}';
				if( isset($_REQUEST['builder']) && intval($_REQUEST['builder'])==1 ) {
					$css .= '#content-cb,#ed_toolbar,#post-status-info,#wp-content-editor-tools{display:none;}#wp-content-editor-container{border-radius:0px;border:none;}.content-builder-mode{display:block;margin-left:10px;float:left;height:30px;line-height:30px;}';
				}
				echo $css;
				exit();
			}

			if( $request['action'] == 'js-config') {
				header("Content-type: text/javascript");
				$textareaName = 'content-cb';
				if( isset($_GET['textarea']) ) {
					$textareaName = $_GET['textarea'];
				}
				$this->init();
				$blocks = array();
				foreach( $this->builder->blocks as $oBlock ) {
					$oBlock = unserialize( serialize( $oBlock ) );
					$cfg = $oBlock->config();
					$blocks[ $oBlock->name ] = $cfg;
				}
				$builder = array();
				$builder['ajaxUrl'] = plugin_dir_url(__FILE__).'content-builder.php';
				$builder['basePath'] = $this->builder->basePath;
				$builder['blocks'] = $blocks;
				$builder['version'] = $this->builder->version;
				$builder['trails'] = $this->builder->trails['byUrl'];
				$builder['toolbar'] = $this->builder->toolbar();
				$builder['width'] = $this->builder->width;
				$script = '';
				$script .= 'jQuery(document).ready(function($){';
				$script .= ' if( window.ContentBuilder !== undefined && $(\'#'.$textareaName.'\').length == 1 ) { window.ContentBuilder.init('.json_encode($builder).'); window.ContentBuilder.replace(\''.$textareaName.'\'); } ';
				$script .= "".
					"	$('#content-mode-button').insertBefore('#wp-content-editor-tools'); ".
					"		$('#content-mode-button').click(function(){".
					"		var answer = confirm( $(this).attr('title') );".
					"		if (answer){".
					"			if( $('#wp-content-wrap').hasClass('tmce-active') ) {".
					"				$('#content-html').trigger('click');".
					"			}".
					"			$('#content-builder-mode').val( ($(this).data('mode') ) );".
					"			$('#publish').trigger('click');".
					"		}".
					"		return false; ".
					"	}); ";
				$script .= ' });';
				echo $script;
				exit();
			}

			if( is_null($request['action']) ) {
				$request['errors'] = 'Empty action ...';
				header('Content-type: application/json');
				echo json_encode($request);
				exit();
			}

			if( isset( $request['action'] ) && $request['action'] == 'pagelist' && !empty($_GET['term']) ) {
				$term = strip_tags($_GET['term']);
				$results = $this->getPostsLinks($term);
				if( !empty($results) ) {
					echo json_encode($results);
					exit();
				}
				exit('No results');
			}

			if( !is_array($request['block']) ) {
				$request['errors'] = 'Empty block...';
				header('Content-type: application/json');
				echo json_encode($request);
				exit();
			}

			if( !isset($request['block']['id']) ) {
				$request['block']['id'] = 2;
			}

			//create root node
			$root = array(
				'block'=>'root',
				'id'=>'1',
				'layout'=>'a',
				'childs'=>array()
			);
			$root['childs'][] = $request['block'];
			$this->init();
			$oRoot = $this->builder->loadData( $root );
			$oBlock = $this->builder->getBlockById( $request['block']['id'] );

			$retVal = array();
			$retVal['action'] = $request['action'];
			$retVal['block'] = $request['block'];
			$retVal['response'] = false;

			if( !is_null($oBlock) ) {
				switch( $request['action'] ) {
					case 'html':
						$retVal['response'] = $oBlock->html();
						break;
					case 'store':
						//
						break;
					default:
						$response = $oBlock->execute($request['action'],(isset($request['params'])?$request['params']:array()));
						if( !is_null($response) ) {
							$retVal['response'] = $response;
						}
						break;
				}
				header('Content-type: application/json');
				echo json_encode($retVal);
				exit();
			} else {
				$request['errors'] = 'Wrong block data...';
				header('Content-type: application/json');
				echo json_encode($request);
				exit();
			}

		}

		/**
		 * Retrieve public posts and pages
		 * @param string $term Search term
		 * @return array
		 */
		public function getPostsLinks( $term ) {
			$args = array();
			if ( isset( $term ) )
				$args['s'] = $term;

			require( ABSPATH . WPINC . '/class-wp-editor.php');
			$results = _WP_Editors::wp_link_query( $args );
			foreach( $results as $k => $result ) {
				if( empty($result['title']) ) {
					unset($results[$k]);
					continue;
				}
				$results[$k]['label'] = $result['title'];
				$results[$k]['value'] = $result['permalink'];
				unset($results[$k]['title']);
				unset($results[$k]['permalink']);
			}

			return array_values($results);
		}

		/**
		 *	Initialize
		 */
		public function init() {

			if ( !class_exists('ContentBuilder') ) {
				include_once( dirname(__FILE__).'/cb/ContentBuilder.php' );
			}

			$this->builder = new ContentBuilder();
			$this->builder->basePath = $this->url();
			$this->builder->ajaxUrl = plugin_dir_url(__FILE__).'content-builder.php';
			$width = get_option('cb_width');
			if( intval($width) > 0 ) {
				$this->builder->width = intval($width);
			}
			$uploads = wp_upload_dir();
			$this->builder->addTrail('builder', $this->url(), $this->path() );
			$this->builder->addTrail('cache', rtrim($uploads['baseurl'],'/').'/content-builder/', rtrim($uploads['basedir'],'/').'/content-builder/' );
			$this->builder->addTrail('wp-uploads', rtrim($uploads['baseurl'],'/').'/', rtrim($uploads['basedir'],'/').'/' );
			$this->builder->create();

			// Start this plugin once all other plugins are fully loaded
			add_action( 'plugins_loaded', array(&$this, 'ready') );

		}

		/**
		 *	Ready event after plugins loaded
		 */
		public function ready() {

			add_thickbox();

			// Check admin
			if ( is_admin() ) {

				// Settings field
				add_action( 'admin_menu', array(&$this, 'admin_menu') );
				add_filter( 'plugin_action_links', array(&$this, 'admin_action_links'), 10, 2);

				// Add the script and style files
				add_filter( 'the_editor', array( $this, 'wp_editor'), 999 );
				add_action( 'admin_head', array(&$this, 'admin_head'));
				add_action( 'admin_print_scripts', array(&$this, 'admin_print_scripts') );
				add_action( 'admin_print_styles', array(&$this, 'admin_print_styles') );

				// Post content processing
				add_filter('tiny_mce_before_init', array(&$this, 'tiny_mce_before_init' ) );
				add_filter( 'wp_insert_post_data', array(&$this, 'insert_post_data' ), 10, 2 );
				add_filter( 'edit_post_content', array(&$this, 'edit_post_content' ), 10, 2 );

			} else {

				//site
				add_action( 'the_content', array(&$this, 'site_the_content'), 1 );
				add_action( 'wp_enqueue_scripts', array(&$this, 'site_enqueue_scripts') );

			}

		}

		/**
		 * Site theme enque scripts & styles
		 */
		public function site_enqueue_scripts(){

			wp_register_style( 'content-builder-theme-css', $this->builder->ajaxUrl.'?action=css', false, $this->builder->version );
			wp_enqueue_style( 'content-builder-theme-css');
			wp_register_script('content-builder-theme-js', plugin_dir_url(__FILE__).'theme/theme.js', false, $this->builder->version );
			wp_enqueue_script( 'content-builder-theme-js' );

		}

		/**
		 * Site theme render content builder content
		 * @param $content
		 * @return string
		 */
		public function site_the_content($content) {
			$retVal = $content;
			preg_match('/\[content\-builder\](.*?)\[\/content\-builder\]/', $content, $match );
			if( count($match)==2 ) {
				$retVal = $this->site_builder_shortcode(array(), $match[1] );
			};
			return($retVal);
		}

		/**
		 * Site render content
		 * @param $atts
		 * @param null $content
		 * @return string
		 */
		public function site_builder_shortcode( $atts, $content = null ) {
			global $post;
			$data = json_decode($content, true );
			$trail = $this->builder->getTrail('cache');
			$this->builder->addTrail('upload', $trail['url'].'/post-'.$post->ID, $trail['path'].'/post-'.$post->ID );
			$this->builder->loadData( $data );
			$html = '<div class="cb-theme">'.do_shortcode( $this->builder->html() ).'</div>';
			return $html;
		}

		/**
		 * Extend admin menu
		 */
		public function admin_menu() {
			add_submenu_page('plugins.php', 'Content Builder Configuration', 'Content Builder', 'manage_options', basename(__FILE__), array(&$this, 'plugin_settings_page'));
		}

		/**
		 * Admin action links
		 * @param $links
		 * @param $file
		 * @return array
		 */
		public function admin_action_links($links, $file) {
			if ( $file == plugin_basename( dirname(__FILE__) . '/content-builder.php' ) ) {
				$links[]  = '<a href="plugins.php?page=content-builder.php">'.__('Settings').'</a>';
			}
			return $links;
		}

		/**
		 * Plugin settings page
		 */
		public function plugin_settings_page() {
			global $wpdb;
			require_once('settings.php');
		}

		/**
		 * Admin Header
		 */
		public function admin_head() {

			if( $this->builderEnabled ) {
				add_filter( 'wp_default_editor', create_function('', 'return "html";') );
			}

		}

		/**
		 * Before tiny mce init, disable editor if builder enabled
		 * @param $initArray
		 * @return array
		 */
		public function tiny_mce_before_init($initArray) {
			if( $this->builderEnabled ) {
				$initArray = array();
			}
			return $initArray;
		}

		/**
		 * Admin editor
		 * @param $content
		 * @return string
		 */
		public function wp_editor( $content ){

			if ( strpos($content, 'editorcontainer') !== false OR strpos($content, 'wp-content-editor-container') !== false ) {
				$textareaName = 'content';
				$content .= '<input type="hidden" id="content-builder-mode" name="content-builder-mode" value="" />';
				$modeLink = '<a class="content-builder-mode" id="'.$textareaName.'-mode-button" data-mode="'.($this->builderEnabled?'off':'on').'" href="#" title="Are you sure to move content to '.($this->builderEnabled?'Classic Editor? All content will be rendered as TEXT.':'Content Builder?').'">'.($this->builderEnabled?'Switch To Classic Editor':'Switch To Content Builder').'</a><div class="cb-clear"></div>';
				if( $this->builderEnabled ) {
					$builder = '';
					$builder .= $this->builder->toolbar();
					//change textarea id to disable tinymce for better performance
					$builder .= str_replace('id="'.$textareaName.'"','id="'.$textareaName.'-cb"', $content );
					$content = $builder;
				}
				$content = $modeLink.$content;
			}
			return( $content );

		}

		/**
		 * Admin zone load scripts
		 */
		public function admin_print_scripts() {
			global $post_type;

			if ( in_array($post_type, $this->postTypes ) ) {
			
				wp_register_script( 'content-builder-initjs', $this->url('init.js'), false, $this->builder->version );
				wp_enqueue_script( 'content-builder-initjs' );

				wp_register_script( 'content-builder-core', $this->url('contentbuilder.js'), array(
					'json2','jquery','jquery-ui-sortable','jquery-ui-draggable','jquery-ui-droppable','jquery-ui-selectable','jquery-ui-autocomplete'
				), $this->builder->version );
				wp_enqueue_script( 'content-builder-core' );

				foreach( $this->builder->blocks as $key=>$oBlock ) {
					wp_register_script('content-builder-'.$oBlock->name, $this->url('blocks/'.$oBlock->name.'.js'), false, $this->builder->version );
					wp_enqueue_script( 'content-builder-'.$oBlock->name );
				}
				wp_register_script( 'content-builder-redactor', $this->url('redactor/redactor.js'), false, $this->builder->version );
				wp_enqueue_script( 'content-builder-redactor' );
				
				wp_register_script( 'content-builder-themejs', $this->url('../theme/theme.js'), false, $this->builder->version );
				wp_enqueue_script( 'content-builder-themejs' );

				/*wp_register_script( 'content-builder-config', $this->builder->ajaxUrl.'?action=js-config&textarea=content-cb&builder='.($this->builderEnabled?'1':'0'), false, $this->builder->version, true );
				wp_enqueue_script( 'content-builder-config' );*/

			}
		}

		/**
		 *	Admin zone load styles
		 */
		public function admin_print_styles() {
			global $post_type;
			if ( in_array($post_type, $this->postTypes ) ) {
				wp_register_style( 'content-builder-redactor-css', $this->url('redactor/redactor.css'), false, $this->builder->version, 'all' );
				wp_enqueue_style( 'content-builder-redactor-css' );
				/*wp_register_style( 'content-builder-theme-css', $this->builder->ajaxUrl.'?action=css&builder='.($this->builderEnabled?'1':'0'), false, $this->builder->version );
				wp_enqueue_style( 'content-builder-theme-css');*/
				wp_register_style( 'content-builder-theme-css', $this->url('init.css'), false, $this->builder->version );
				wp_register_style( 'content-builder-core-css', $this->url('assets/contentbuilder.css'), false, $this->builder->version );
				wp_register_style( 'content-builder-coreadditions-css', $this->url('assets/contentbuilder_additions.css'), false, $this->builder->version );
				wp_enqueue_style( 'content-builder-theme-css');
				wp_enqueue_style( 'content-builder-core-css');
				wp_enqueue_style( 'content-builder-coreadditions-css');
			}
		}

		/**
		 * Populate the edit box with the unfiltered post content.
		 *
		 * This runs right before the edit form is populated with the post and
		 * populates the edit box with the unfiltered post (if it needs to).
		 *
		 * @param  string $content the post's content
		 * @param  int    $id      the post's ID
		 * @return string          json
		 */
		public function edit_post_content( $content, $id ) {

			$post = get_post( $id );
			$this->builderEnabled = false;
			preg_match('/\[content-builder\](.*?)\[\/content-builder\]/', $content, $result );
			if( isset($result[1]) ) {
				$this->builderEnabled = true;
				$trail = $this->builder->getTrail('cache');
				$this->builder->addTrail('upload', $trail['url'].'/post-'.$id, $trail['path'].'/post-'.$id );
				$data = json_decode($result[1], true );
				$encode = '';
				if($data['block'] == 'root' ) {
					$oRoot = $this->builder->loadData( $data );
					$encode = json_encode($oRoot->getData());
				}
				$content = '[content-builder]'.$encode.'[/content-builder]';
			}
			return $content;
		}

		/**
		 * A filter hook called by the wp_insert_post function prior to inserting into or updating the database.
		 *
		 * @param 	array $data		Sanitized post data.
		 * @param 	array $postarr 	Raw post data.
		 * @return 	array
		 */
		public function insert_post_data( $data, $postarr ) {

			//turn on content builder
			if( isset($postarr['content-builder-mode']) &&  $postarr['content-builder-mode'] == 'on' ) {
				//run once
				remove_filter( 'wp_insert_post_data', array(&$this, 'insert_post_data' ), 10, 2 );
				$rte = array(
					'block'=>'rte',
					'content'=>stripslashes($data['post_content'])
				);
				$oBlock = $this->builder->loadBlock( $rte );
				if( !is_null($oBlock) ) {
					$content = addslashes( $this->builder->toString()."[content-builder]".$this->builder->data(true)."[/content-builder]");
					//$a = json_decode( stripslashes( addslashes($this->builder->data(true)) ), true );
					$data['post_content'] = $content;
				}
				return( $data );
			}

			if( isset($postarr['content']) ) {
				//check if posting data is from content builder
				preg_match('/\[content-builder\](.*?)\[\/content-builder\]/', $postarr['content'], $result );
				if( isset($result[1]) ) {
					//run once
					remove_filter( 'wp_insert_post_data', array(&$this, 'insert_post_data' ), 10, 2 );
					$json = stripslashes( $result[1] );
					$builderData = json_decode( $json, true );
					if( $builderData['block'] == 'root' && isset($builderData['version']) ) {
						$trail = $this->builder->getTrail('cache');
						$id = ( isset( $postarr['ID']) ) ? $postarr['ID'] : 0;
						$this->builder->addTrail('upload', $trail['url'].'/post-'.$id, $trail['path'].'/post-'.$id );
						$oRoot = $this->builder->loadData( $builderData );
						if( !is_null($oRoot) ) {
							$this->builder->store();
							$data['post_content'] = addslashes( $this->builder->toString().'[content-builder]'.$this->builder->data(true).'[/content-builder]' );
						}
						if( $postarr['content-builder-mode'] == 'off' ) {
							$data['post_content'] = addslashes( $this->builder->toString() );
						}
					}
				}
			}

			return $data;
		}

		/**
		 * Get Plugin File Url
		 * @static
		 * @param $file
		 * @return string
		 */
		public static function url( $file = null ) {
			return ( plugin_dir_url(__FILE__).'cb/'.(!is_null($file)?$file:'') );
		}

		/**
		 * Get Plugin File Path
		 * @static
		 * @param $file
		 * @return string
		 */
		public static function path( $file = null ) {
			return ( plugin_dir_path(__DIR__).'cb/'.(!is_null($file)?$file:'')  );
		}

	}

	if (!function_exists('add_action')) {

		require_once( "../../../wp-config.php");
		$content_builder = ContentBuilderWP::getInstance();
		$content_builder->ajax();
		
		
				
		exit();
	}

	$content_builder = ContentBuilderWP::getInstance();
	$content_builder->init();
}

?>