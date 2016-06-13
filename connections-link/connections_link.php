<?php
/*
Plugin Name: Connections Link
Plugin URI: http://www.connections-pro.com
Description: Connections Link
Version: 1.0.2
Author: Steven A. Zahm
Author URI: http://www.connections-pro.com
Text Domain: connections_link
Domain Path: /lang

Copyright 2013  Steven A. Zahm  (email : helpdesk@connections-pro.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if ( ! class_exists( 'Connections_Link' ) ) {

	class Connections_Link {

		/**
		* @var (object) Connections_Link stores the instance of this class.
		*/
		private static $instance;

		/**
		* @var (int) Stores the WP user id linked to an entry. Values can be NULL, FALSE or (int)
		*/
		private static $userID = NULL;

		/**
		 * A dummy constructor to prevent Connections_Link from being loaded more than once.
		 *
		 * @access private
		 * @since 1.0
		 * @see Connections_Link::instance()
		 * @see Connections_Link();
		 */
		private function __construct() { /* Do nothing here */ }

		/**
		 * Main Connections_Link Instance
		 *
		 * Insures that only one instance of Connections_Link exists in memory at any one time.
		 *
		 * @access public
		 * @since 1.0
		 * @return object Connections_Link
		 */
		public static function getInstance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new Connections_Link;
				self::$instance->init();
			}
			return self::$instance;
		}

		/**
		 * Initiate the plugin.
		 *
		 * @access private
		 * @since 1.0
		 * @return void
		 */
		private function init() {
			self::defineConstants();
			self::setUserID();

			// register_activation_hook( dirname(__FILE__) . '/connections_link.php', array( __CLASS__, 'activate' ) );
			// register_deactivation_hook( dirname(__FILE__) . '/connections_link.php', array( __CLASS__, 'deactivate' ) );

			load_plugin_textdomain( 'connections_link' , false , CNLNK_DIR_NAME . 'lang' );

			// Add the entry form menu item to the Users/Profile admin menu.
			// Set priority as 11 because the Connections adminMenu method runs at default priority and I want to add the page hook after Connections has been loaded.
			// NOTE: This action has to be registered before the admin_init hook which is why it is here and not in the adminInit method.
			add_action( 'admin_menu', array( __CLASS__, 'adminMenu' ), 11 );

			// Run the admin action/filters.
			add_action( 'admin_init', array( __CLASS__, 'adminInit' ) );

			// Add the entry from menu item to the admin bar under the My Account section.
			add_action( 'wp_before_admin_bar_render', array( __CLASS__, 'adminBarMenuItem' ) );

			// Add the current user ID to the cnEntry object that is passed by the filter.
			add_filter( 'cn_pre_process_add-entry', array( __CLASS__, 'processAddUser') );
			add_filter( 'cn_pre_process_update-entry', array( __CLASS__, 'processUpdateUser') );

			// Add the entry ID to the current users meta.
			add_action( 'cn_post_process_add-entry', array( __CLASS__, 'processAddUserMeta') );
			add_action( 'cn_post_process_update-entry', array( __CLASS__, 'processUpdateUserMeta') );

			// Remove the entry ID from the current users meta when an entry is deleted.
			add_action( 'cn_process_delete-entry', array( __CLASS__, 'processDeleteUserMeta') );

			// Upon user login, search the Connections email table for a matching email and then process to link the user to that entry.
			add_action( 'wp_login', array( __CLASS__, 'processLinkOnLogin'), 10, 2 );
		}

		/**
		 * Define the constants.
		 *
		 * @access private
		 * @since 1.0
		 * @return void
		 */
		private function defineConstants() {

			define( 'CNLNK_VERSION', '1.0.2' );
			define( 'CNLNK_DB_VERSION', '1.0' );

			define( 'CNLNK_DIR_NAME', plugin_basename( dirname( __FILE__ ) ) );
			define( 'CNLNK_BASE_NAME', plugin_basename( __FILE__ ) );
			define( 'CNLNK_BASE_PATH', plugin_dir_path( __FILE__ ) );
			define( 'CNLNK_BASE_URL', plugin_dir_url( __FILE__ ) );

		}

		/**
		 * Called when activating Connections Link via the activation hook.
		 *
		 * @access private
		 * @since 1.0
		 * @return void
		 */
		public static function activate() {

			$currentVersion = get_option( 'connections_link_version' );
			$currentDBVersion = get_option( 'connections_link_db_version' );

			if ( $currentVersion === FALSE ) {
				add_option( 'connections_link_version', CNLNK_VERSION );
			} else {
				update_option( 'connections_link_version', CNLNK_VERSION );
			}

			if ( $currentDBVersion === FALSE ) {
				add_option( 'connections_link_db_version', CNLNK_DB_VERSION );
			} else {
				update_option( 'connections_link_db_version', CNLNK_DB_VERSION );
			}
		}

		/**
		 * Called when deactivating Connections Link via the deactivation hook.
		 *
		 * @access private
		 * @since 1.0
		 * @return void
		 */
		public static function deactivate() {

		}

		/**
		 * Init the admin ... add all tha admin action/filters here.
		 *
		 * @access private
		 * @since 1.0
		 * @return void
		 */
		public static function adminInit() {
			global $connections;

			// Enqueue the core Connections / WordPress scripts
			add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueueAdminScripts' ) );

			// Enqueue the core Connections admin CSS file.
			add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueueAdminStyles' ) );

			// Register the form metaboxes.
			add_action( 'load-' . $connections->pageHook->link, array( __CLASS__, 'registerEditMetaboxes' ) );

			add_action( 'cn_admin_form_edit_entry_before', array( __CLASS__, 'editFormBefore' ), 10, 2 );
		}

		/**
		 * If the user has been linked to a Connections entry, return the Connections id.
		 *
		 * @access public
		 * @since 1.0
		 * @return (int) Entry id
		 */
		public static function setUserID() {
			global $wpdb, $connections;

			// Get the entry ID of the current user from the user meta.
			$userID = get_user_meta( $connections->currentUser->getID(), 'connections_entry_id', TRUE );

			// Just in case the user meta is missing, check CN_ENTRY_TABLE
			if ( $userID <= 0 ){
				self::$userID = $wpdb->get_var( $wpdb->prepare( 'SELECT id FROM ' . CN_ENTRY_TABLE . ' WHERE user = %d', $connections->currentUser->getID() ) );
			} else {
				self::$userID = $userID;
			}

		}

		/**
		 * Add the sub menu item to the Users / Profile top level menu.
		 *
		 * @access private
		 * @since 1.0
		 * @return void
		 */
		public static function adminMenu() {
			global $connections;

			$connections->pageHook->link = add_users_page( __( 'Your Directory Entry', 'connections_link' ), __( 'Your Directory Entry', 'connections_link' ), 'read', 'connections_link', array( __CLASS__, 'showPage' ) );

		}

		/**
		 * Add the menu items to the My Account section of the admin bar
		 *
		 * @access private
		 * @since 1.0
		 * @return void
		 */
		public static function adminBarMenuItem() {
			global $wp_admin_bar;

			$wp_admin_bar->add_menu( array(
				'parent' => 'my-account',						// use 'false' for a root menu, or pass the ID of the parent menu
				'id'     => 'connections-link',					// link ID, defaults to a sanitized title value
				'title'  => __( 'Edit My Directory Entry', 'connections_link' ),				// link title
				'href'   => admin_url( 'users.php?page=connections_link' ),				// name of file
				'meta'   => false 								// array of any of the following options: array( 'html' => '', 'class' => '', 'onclick' => '', target => '', title => '' );
			));

			// $wp_admin_bar->add_menu( array(
			// 	'parent' => 'my-account-with-avatar',			// use 'false' for a root menu, or pass the ID of the parent menu
			// 	'id'     => 'connections-link',					// link ID, defaults to a sanitized title value
			// 	'title'  => __( 'Edit My Directory Entry', 'connections_link' ),				// link title
			// 	'href'   => admin_url( 'users.php?page=connections_link' ),				// name of file
			// 	'meta'   => false 								// array of any of the following options: array( 'html' => '', 'class' => '', 'onclick' => '', target => '', title => '' );
			// ));
		}

		/**
		 * Loads the Connections Link JavaScripts only on required admin pages.
		 *
		 * @access private
		 * @since 1.0
		 * @return void
		 */
		public static function enqueueAdminScripts( $pageHook ) {
			global $connections;

			if ( $pageHook == $connections->pageHook->link ) {

				wp_enqueue_script( 'jquery-preloader' );
				wp_enqueue_script( 'cn-ui-admin' );

				wp_enqueue_script( 'jquery-gomap-min' );
				wp_enqueue_script( 'jquery-ui-datepicker' );
				wp_enqueue_script( 'jquery-chosen-min' );

				wp_enqueue_script( 'common' );
				wp_enqueue_script( 'wp-lists' );
				wp_enqueue_script( 'postbox' );
				wp_enqueue_script( 'cn-widget' );
			}
		}

		/**
		 * Loads the Connections CSS only on Link admin page.
		 *
		 * @access private
		 * @since 1.0
		 * @return void
		 */
		public static function enqueueAdminStyles( $pageHook ) {
			global $connections;

			if ( $pageHook == $connections->pageHook->link ) {
				wp_enqueue_style( 'cn-admin' );
				wp_enqueue_style( 'cn-admin-jquery-ui' );
				wp_enqueue_style( 'connections-chosen' );
			}

		}

		/**
		 * Register the metaboxes used for editing an entry.
		 *
		 * Action added in connectionsLoad::adminInit
		 *
		 * @access private
		 * @since 1.0
		 * @return void
		 */
		public static function registerEditMetaboxes() {

			// The meta boxes do not need diplayed/registered if no action is being taken on an entry. Such as copy/edit.
			//if ( $_GET['page'] === 'connections_link' &&  ! isset( $_GET['action'] ) )  return;

			$form = new cnFormObjects();

			$form->registerEditMetaboxes( substr( current_filter(), 5 ) );

			add_filter( 'screen_layout_columns', array( __CLASS__, 'screenLayout' ), 10, 2 );
		}

		/**
		 * Register the number of columns permitted for metabox use on the edit entry page.
		 *
		 * Filter added in connectionsLoad::registerEditMetaboxes
		 *
		 * @access private
		 * @since 1.0
		 * @return array
		 */
		public static function screenLayout( $columns, $screen ) {
			global $connections;

			$columns[ $connections->pageHook->link ] = 2;

			return $columns;
		}

		/**
		 * Add the hidden fields that are necessary the core edit form to maintain
		 * a link between a Connections entry and a registered WP User.
		 *
		 * @access private
		 * @since 1.0
		 * @param  (object) $entry A instance of the cnEntry class.
		 * @param  (object) $form A instance of the cnForm class.
		 * @return void
		 */
		public static function editFormBefore( $entry, $form ) {
			$userID = $entry->getUser();

			// Add the current user nonce
			if ( ! empty( $userID ) ) {
				$form->tokenField( 'update_user', $userID, '_cn_update_user_nonce', FALSE );
				echo '<input type="hidden" name="update_user_id" value="' . $userID . '" />';
			}
		}

		/**
		 * Limit the entry type choices to individual and organization.
		 *
		 * @access private
		 * @since 1.0
		 * @param  (array) $atts The configuration options array of the publish metabox.
		 * @return (array)
		 */
		public static function alterEntryTypes( $atts ){

			$atts['entry_type'] = array(
				__( 'Individual', 'connections' )   => 'individual',
				__( 'Organization', 'connections' ) => 'organization'
				);

			return $atts;
		}

		/**
		 * Change the action of the publish metabox when a user is editting their entry.
		 *
		 * @access private
		 * @since 1.0
		 * @param  (array) $atts The configuration options array of the publish metabox.
		 * @return (array)
		 */
		public static function publishActionEdit( $atts ) {

			$atts['action'] = 'edit';

			return $atts;
		}

		/**
		 * Search the Connections email table for a matching email and then process to link the user to that entry.
		 *
		 * @access private
		 * @since 1.0
		 * @uses get_user_meta()
		 * @uses add_user_meta()
		 * @uses absint()
		 * @param  (string) $user_login Users login.
		 * @param  (object) $user       An instance of WP_User.
		 * @return void
		 */
		public static function processLinkOnLogin( $user_login, $user ) {
			global $wpdb;

			$metaData = get_user_meta( $user->ID, 'connections_entry_id', TRUE );

			// The link on login only occurs if the user logging has not already been linked to an entry.
			if ( empty( $metaData ) ) {

				// Search the Connections email table for matching address.
				$id = $wpdb->get_var( $wpdb->prepare( 'SELECT cne.entry_id FROM ' . CN_ENTRY_TABLE . ' AS cn INNER JOIN ' . CN_ENTRY_EMAIL_TABLE . ' AS cne ON cn.id = cne.entry_id WHERE cne.address = %s AND cn.user = 0', $user->data->user_email ) );

				if ( empty( $id ) ) {
					// If there was not a matching email address, set a negative value just so we know this user was already checked and don't check them again.
					add_user_meta( $user->ID, 'connections_entry_id', -1, TRUE );
				} else {
					// $entry = new cnEntry();

					$id = absint( $id );
					// $entry->set( $id );

					// $entry->setUser( $user->ID );
					// $entry->update();
					// unset( $entry );

					$wpdb->query( $wpdb->prepare( 'UPDATE ' . CN_ENTRY_TABLE . ' SET user = %d WHERE id = %d', $user->ID, $id ) );

					add_user_meta( $user->ID, 'connections_entry_id', $id, TRUE );
				}

			}

		}

		/**
		 * Verify this nonce if it exists to support a link with an entry and a WP user.
		 *
		 * @access private
		 * @since 1.0
		 * @uses wp_verify_nonce()
		 * @uses wp_die()
		 * @uses add_user_meta()
		 * @param  (object) $entry An instance of the cnEntry class.
		 * @return (object)
		 */
		public static function processAddUser( $entry ) {
			$form = new cnFormObjects();

			// If the current user ID nonce field is set and is valid set the current user ID.
			if ( isset( $_POST['_cn_add_user_nonce'] ) &&
				 isset( $_POST['add_user_id'] ) &&
				 wp_verify_nonce( $_POST['_cn_add_user_nonce'], $form->getNonce( 'add_user', $_POST['add_user_id'] ) ) ) {
				$entry->setUser( absint( $_POST['add_user_id'] ) );
			} else {
				$entry->setUser( 0 );
			}

			// Add the Connections entry ID to the user's meta data.
			// add_user_meta( $_POST['update_user_id'], 'connections_entry_id', $entry->getID(), TRUE );

			return $entry;
		}

		/**
		 * Verify this nonce if it exists to support a link with an entry and a WP user.
		 *
		 * @access private
		 * @since 1.0
		 * @uses wp_verify_nonce()
		 * @uses wp_die()
		 * @uses update_user_meta()
		 * @param  (object) $entry An instance of the cnEntry class.
		 * @return (object)
		 */
		public static function processUpdateUser( $entry ) {
			$form = new cnFormObjects();

			// If the current user ID nonce field is set and is valid set the current user ID.
			if ( isset( $_POST['_cn_update_user_nonce'] ) &&
				 isset( $_POST['update_user_id'] ) &&
				 wp_verify_nonce( $_POST['_cn_update_user_nonce'], $form->getNonce( 'update_user', $_POST['update_user_id'] ) ) ) {
				$entry->setUser( absint( $_POST['update_user_id'] ) );
			} else {
				$entry->setUser( 0 );
			}

			// Update the Connections entry ID to the user's meta data.
			// update_user_meta( $_POST['update_user_id'], 'connections_entry_id', $entry->getID(), $entry->getID() );

			return $entry;
		}

		/**
		 * Add the linked entry ID to the current users meta.
		 *
		 * @access private
		 * @since 1.0
		 * @uses add_user_meta()
		 * @param  (object) $entry An instance of the cnEntry class.
		 * @return void
		 */
		public static function processAddUserMeta( $entry ) {

			update_user_meta( $_POST['add_user_id'], 'connections_entry_id', $entry->getID() );
		}

		/**
		 * Update the linked entry ID to the current users meta.
		 *
		 * @access private
		 * @since 1.0
		 * @uses update_user_meta()
		 * @param  (object) $entry An instance of the cnEntry class.
		 * @return void
		 */
		public static function processUpdateUserMeta( $entry ) {

			update_user_meta( $_POST['update_user_id'], 'connections_entry_id', $entry->getID() );
		}

		/**
		 * Delete the linked entry ID from the current users meta.
		 *
		 * @access private
		 * @since 1.0
		 * @uses delete_user_meta()
		 * @param  (object) $entry An instance of the cnEntry class.
		 * @return void
		 */
		public static function processDeleteUserMeta( $entry ) {

			$userID = $entry->getUser();
			if ( ! empty( $userID ) ) delete_user_meta( $entry->getUser(), 'connections_entry_id' );
		}

		/**
		 * Show the edit entry form.
		 *
		 * @access private
		 * @since 1.0
		 * @return void
		 */
		public static function showPage() {
			global $wpdb, $connections;

			// Set the action if an entry ID was found.
			$action = self::$userID <= 0 ? 'add' : 'edit';

			include_once CNLNK_BASE_PATH . '/submenus/manage.php';
			connectionsLinkManage( $action, self::$userID );

		}

	}

	/**
	 * The main function responsible for returning the cnLink instance
	 * to functions everywhere.
	 *
	 * Use this function like you would a global variable, except without needing
	 * to declare the global.
	 *
	 * Example: <?php $cn_link = Connections_Link(); ?>
	 *
	 * If the main Connections class exists, fire up Link. If not, throw an admin error notice.
	 *
	 * @access public
	 * @since 1.0
	 * @return mixed (object) || (bool) Connections_Link Instance or FALSE if Connections is not active.
	 */
	function Connections_Link() {
		if ( class_exists('connectionsLoad') ) {
			return Connections_Link::getInstance();
		} else {
			add_action(
				'admin_notices',
				 create_function(
				 	'',
					'echo \'<div id="message" class="error"><p><strong>ERROR:</strong> Connections must be installed and active in order to run Link.</p></div>\';'
					)
			);

			return FALSE;
		}
	}

	/**
	 * Start the plugin.
	 *
	 * Since Connections loads at default priority 10, and this add-on is dependent on Connections,
	 * we'll load with priority 11 so we know Connections will be loaded and ready first.
	 */
	add_action( 'plugins_loaded', 'Connections_Link', 11 );

}