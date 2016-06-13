<?php
/*
Plugin Name: Simple Content Templates
Plugin URI: http://cgd.io
Description:  The simplest post content templates of them all.
Version: 2.0.16
Author: CGD, Inc.
Author URI: http://cgd.io
*/

class SimpleContentTemplates {

	static $instance = false;
	var $table_name;
	var $bpt_url = "http://cgd.io/downloads/advanced-content-templates/?utm_campaign=SPTUpgradeNag";

	public function __construct() {
		global $wpdb;

		register_activation_hook( __FILE__, array($this, 'activate') );
		add_action('admin_menu', array($this, 'add_menu') );
		add_action('admin_init', array($this, 'process_forms'));

		$this->table_name = $wpdb->prefix . "simple_post_templates";

		if(get_option("sct_auto") != "no" || isset($_REQUEST['sct_template_load']))
		{
		  add_filter('default_content', array($this, 'content') );
		  add_filter('default_title', array($this, 'title') );
		  add_filter('default_excerpt', array($this, 'excerpt') );
		}

		add_action('add_meta_boxes', array($this, 'boxes'));

		if( get_option('spt_version',false) === false || get_option('spt_version') != '2.0.16' ) {
			$this->activate();
		}

		add_action( 'admin_menu' , array($this, 'add_bpt_link') );
	}

	public static function getInstance() {
		if ( !self::$instance ) {
		  self::$instance = new self;
		}
		return self::$instance;
	}

	function boxes() {
		add_meta_box( 'sct_side_car', 'Simple Content Template', array($this, 'render_side_car'), 'post', 'side', 'high');
		add_meta_box( 'sct_side_car', 'Simple Content Template', array($this, 'render_side_car'), 'page', 'side', 'high');
	}

	public function activate() {
		global $wpdb;
		add_option("sct_auto", "no");

		/* Create Table */
		$sql = "CREATE TABLE $this->table_name (
		  id int(11) NOT NULL AUTO_INCREMENT,
		  title varchar(100) NOT NULL,
		  excerpt varchar(500) NOT NULL,
		  content varchar(60000) NOT NULL,
		  created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		  PRIMARY KEY  (id)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);

		if(strlen(get_option('sct_title')) > 0) {
			// Create new template
			$wpdb->insert(
				$this->table_name,
				array(
					'title' => get_option('sct_title'),
					'excerpt' => get_option('sct_excerpt'),
					'content' => get_option('sct_template')
				),
				array(
					'%s',
					'%s',
					'%s'
				)
			);

			delete_option('sct_title');
			delete_option('sct_excerpt');
			delete_option('sct_template');
		}

		if(get_option('sct_auto') == "yes") {
			update_option('sct_auto', "1"); // should always be true in this instance
		}

		update_option('spt_version', '2.0.16');
	}

	public function add_menu() {
		add_menu_page( "Templates", "Simple Content Templates", 'edit_others_pages', 'sct-templates', array($this, 'show_templates'), 'dashicons-format-aside' );
		add_submenu_page ( "sct-templates", "Settings", "Settings", "edit_others_pages", "sct-settings", array($this, 'show_settings')  );
		add_submenu_page ( "sct-templates", "Help", "Help", "edit_others_pages", "sct-help", array($this, 'show_help')  );
		add_submenu_page( null, "Create Template", "Create Template", "edit_others_pages", "sct-new-template", array($this, 'show_new_template') );
	}

	public function add_bpt_link() {
		if ( ! current_user_can('edit_others_pages') ) return;
		
		global $submenu;
		$submenu['sct-templates'][500] = array( 'Upgrade to Advanced Content Templates!', 'edit_others_pages' , $this->bpt_url );
	}

	public function show_templates() {
		$this->upgrade_nag();
		include 'sct-admin-templates.php';
	}

	public function show_settings() {
		include 'sct-admin-settings.php';
	}

	public function show_new_template() {
		$this->upgrade_nag();
		include 'sct-admin-new-template.php';
	}

	public function show_help() {
		include 'sct-admin-help.php';
	}

	public function strip_fugging_slashes() {
		$_POST      = array_map('stripslashes_deep', $_POST);
		$_GET       = array_map('stripslashes_deep', $_GET);
		$_REQUEST   = array_map('stripslashes_deep', $_REQUEST);
	}

	public function process_forms() {
		global $wpdb;

		if(isset($_REQUEST['sct_action'])) {
			$action = $_REQUEST['sct_action'];

			if($action == "create-template") {
				$wpdb->insert(
					$this->table_name,
					array(
						'title' => $_REQUEST['sct-title'],
						'excerpt' => $_REQUEST['sct-excerpt'],
						'content' => $_REQUEST['sct-content']
					),
					array(
						'%s',
						'%s',
						'%s'
					)
				);

				wp_redirect('admin.php?page=sct-templates');
				exit();
			} else if($action == "edit-template") {
				$wpdb->update(
					$this->table_name,
					array(
						'title' => $_REQUEST['sct-title'],
						'excerpt' => $_REQUEST['sct-excerpt'],
						'content' => $_REQUEST['sct-content']
					),
					array( 'id' => $_REQUEST['sct-edit-id']),
					array(
						'%s',
						'%s',
						'%s'
					)
				);
				wp_redirect('admin.php?page=sct-templates');
				exit();
			} else if($action == "delete") {
				$template_id = $_REQUEST['id'];

				if($template_id > 0) {
					$wpdb->query($wpdb->prepare("DELETE FROM $this->table_name WHERE id = %d", $template_id));
				}
			} else if($action == "save-settings") {
				update_option('sct_auto', $_REQUEST['sct_auto']);
			}
		}
	}

	public function get_templates() {
		global $wpdb;
		$templates = $wpdb->get_results("SELECT * FROM $this->table_name ORDER BY title ASC");
		return $templates;
	}

	public function get_template($template_id) {
		global $wpdb;

		$templates = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $this->table_name WHERE id = %d", $template_id) );
		$template = $templates[0];

		// Stripslashes
		$template->title = stripslashes($template->title);
		$template->excerpt = stripslashes($template->excerpt);
		$template->content = stripslashes($template->content);

		return $templates[0];
	}

	function content() {
		if(isset($_REQUEST['sct_template_load'])) {
			$template_id = $_REQUEST['sct_template_load'];
		} else {
			$template_id = get_option("sct_auto");
		}

		ob_start();
		$template = $this->get_template( $template_id );
		$content = $template->content;

		eval('?>' . str_ireplace(array('&lt;?php', '?&gt;'), array('<?php', '?>'), $content) );
		$content = ob_get_clean();

		return $content;
	}

	function title() {
		if(isset($_REQUEST['sct_template_load'])) {
			$template_id = $_REQUEST['sct_template_load'];
		} else {
			$template_id = get_option("sct_auto");
		}

		ob_start();
		$template = $this->get_template( $template_id );
		$title = $template->title;

		eval('?>' . str_ireplace(array('&lt;?php', '?&gt;'), array('<?php', '?>'), $title) );
		$title =  ob_get_clean();

		return trim($title);
	}

	function excerpt() {
		if(isset($_REQUEST['sct_template_load'])) {
			$template_id = intval( $_REQUEST['sct_template_load'] );
		} else {
			$template_id = get_option("sct_auto");
		}

		$template = $this->get_template($template_id );
		$excerpt = $template->excerpt;

		if(strlen($excerpt) > 0)
		{
			ob_start();
			eval('?>' . str_ireplace(array('&lt;?php', '?&gt;'), array('<?php', '?>'), $excerpt) );
			$excerpt =  ob_get_clean();
		}

		return trim($excerpt);
	}

	function render_side_car() {
		ob_start();

		include 'sct-admin-sidecar.php';
		echo ob_get_clean();
	}

	function upgrade_nag() {
		$titles = array(
			'Learn More',
			'Buy Now',
			'Upgrade Now',
		);

		$the_title = array_rand($titles);
		$the_title = $titles[$the_title];
		?>
		<div id="spt-activation-nag" class="update-nag">
			<p class="nag">
				<big>Go Pro with Advanced Content Templates!</big>
				<p>
				You're missing out on custom post types, taxonomies, custom fields, featured images, and much more!<br/>Join hundreds of other bloggers and purchase <b>Advanced Content Templates</b> today.
				</p>
				<a href="<?php echo $this->bpt_url; ?>?utm_medium=NagBar&utm_content=<?php echo urlencode($the_title); ?>" class="button button-primary" target="_blank"><big><?php echo $the_title; ?></big></a>
			</p>
		</div>
		<?php
	}
}

$SimpleContentTemplates = SimpleContentTemplates::getInstance();