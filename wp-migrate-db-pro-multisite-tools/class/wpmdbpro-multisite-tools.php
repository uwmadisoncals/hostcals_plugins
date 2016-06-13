<?php

class WPMDBPro_Multisite_Tools extends WPMDBPro_Addon {
	protected $wpmdbpro;

	/**
	 * @param string $plugin_file_path
	 */
	function __construct( $plugin_file_path ) {
		parent::__construct( $plugin_file_path );
		$this->plugin_slug    = 'wp-migrate-db-pro-multisite-tools';
		$this->plugin_version = $GLOBALS['wpmdb_meta']['wp-migrate-db-pro-multisite-tools']['version'];
		if ( ! $this->meets_version_requirements( '1.5.2' ) ) {
			return;
		}

		add_action( 'wpmdb_before_migration_options', array( $this, 'migration_form_controls' ) );
		add_action( 'wpmdb_load_assets', array( $this, 'load_assets' ) );
		add_action( 'wpmdb_diagnostic_info', array( $this, 'diagnostic_info' ) );
		add_filter( 'wpmdb_accepted_profile_fields', array( $this, 'accepted_profile_fields' ) );

		add_filter( 'wpmdb_exclude_table', array( $this, 'filter_table_for_subsite' ), 10, 2 );
		add_filter( 'wpmdb_tables', array( $this, 'filter_tables_for_subsite' ), 10, 2 );
		add_filter( 'wpmdb_table_sizes', array( $this, 'filter_table_sizes_for_subsite' ), 10, 2 );
		add_filter( 'wpmdb_target_table_name', array( $this, 'filter_target_table_name' ), 10, 3 );
		add_filter( 'wpmdb_table_row', array( $this, 'filter_table_row' ), 10, 4 );
		add_filter( 'wpmdb_find_and_replace', array( $this, 'filter_find_and_replace' ), 10, 3 );

		global $wpmdbpro;
		$this->wpmdbpro = $wpmdbpro;
	}

	/**
	 * Adds the multisite tools settings to the migration setting page in core.
	 */
	function migration_form_controls() {
		$this->template( 'migrate' );
	}

	/**
	 * Whitelist multisite tools setting fields for use in AJAX save in core
	 *
	 * @param array $profile_fields
	 *
	 * @return array
	 */
	function accepted_profile_fields( $profile_fields ) {
		$profile_fields[] = 'multisite_subsite_export';
		$profile_fields[] = 'select_subsite';
		$profile_fields[] = 'new_prefix';

		return $profile_fields;
	}

	/**
	 * Get translated strings for javascript and other functions.
	 *
	 * @return array
	 */
	function get_strings() {
		static $strings;

		if ( ! empty( $strings ) ) {
			return $strings;
		}

		$strings = array(
			'migration_failed'        => __( "Migration failed", 'wp-migrate-db-pro-multisite-tools' ),
			'please_select_a_subsite' => __( "Please select a subsite.", 'wp-migrate-db-pro-multisite-tools' ),
			'please_enter_a_prefix'   => __( "Please enter a new table prefix.", 'wp-migrate-db-pro-multisite-tools' ),
			'new_prefix_contents'     => __( "Please only enter letters, numbers or underscores for the new table prefix.", 'wp-migrate-db-pro-multisite-tools' ),
		);

		return $strings;
	}

	/**
	 * Retrieve a specific translated string.
	 *
	 * @param string $key
	 *
	 * @return string
	 */
	function get_string( $key ) {
		$strings = $this->get_strings();

		return ( isset( $strings[ $key ] ) ) ? $strings[ $key ] : '';
	}

	/**
	 * Load multisite tools related assets in core plugin.
	 */
	function load_assets() {
		$plugins_url = trailingslashit( plugins_url() ) . trailingslashit( $this->plugin_folder_name );
		$version     = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? time() : $this->plugin_version;

		$src = $plugins_url . 'asset/css/styles.css';
		wp_enqueue_style( 'wp-migrate-db-pro-multisite-tools-styles', $src, array( 'wp-migrate-db-pro-styles' ), $version );

		$src = $plugins_url . 'asset/js/script.js';
		wp_enqueue_script( 'wp-migrate-db-pro-multisite-tools-script',
			$src,
			array(
				'jquery',
				'wp-migrate-db-pro-common',
				'wp-migrate-db-pro-hook',
				'wp-migrate-db-pro-script',
			),
			$version,
			true );

		wp_localize_script( 'wp-migrate-db-pro-multisite-tools-script', 'wpmdbmst_strings', $this->get_strings() );
	}

	/**
	 * Adds extra information to the core plugin's diagnostic info
	 */
	function diagnostic_info() {
		echo 'Sites: ';
		echo number_format( get_blog_count() );
		echo "\r\n";
	}

	/**
	 * Return subsite id if subsite export in progress.
	 *
	 * @return int
	 */
	function subsite_for_export() {
		$blog_id = 0;

		if ( ! is_multisite() ) {
			return $blog_id;
		}

		$this->wpmdbpro->set_post_data();
		$subsite_export   = $this->wpmdbpro->profile_value( 'multisite_subsite_export' );
		$selected_subsite = $this->wpmdbpro->profile_value( 'select_subsite' );

		if ( empty( $blog_id ) &&
		     ! empty( $this->wpmdbpro->state_data['intent'] ) &&
		     'savefile' === $this->wpmdbpro->state_data['intent'] &&
		     ! empty( $subsite_export ) &&
		     ! empty( $selected_subsite ) &&
		     is_numeric( $selected_subsite )
		) {
			$blog_id = $selected_subsite;
		}

		global $loaded_profile;
		if ( empty( $blog_id ) &&
		     ! empty( $loaded_profile['action'] ) &&
		     'savefile' === $loaded_profile['action'] &&
		     ! empty( $loaded_profile['multisite_subsite_export'] ) &&
		     ! empty( $loaded_profile['select_subsite'] ) &&
		     is_numeric( $loaded_profile['select_subsite'] )
		) {
			$blog_id = $loaded_profile['select_subsite'];
		}

		return $blog_id;
	}

	/**
	 * Should the given table be excluded from a subsite migration.
	 *
	 * @param bool $exclude
	 * @param string $table_name
	 *
	 * @return bool
	 */
	function filter_table_for_subsite( $exclude, $table_name ) {
		$blog_id = $this->subsite_for_export();

		if ( 0 < $blog_id ) {
			// wp_users and wp_usermeta are relevant to all sites, shortcut out.
			if ( $this->wpmdbpro->table_is( '', $table_name, 'non_ms_global' ) ) {
				return $exclude;
			}

			// Following tables are Multisite setup tables and can be excluded from migration.
			if ( $this->wpmdbpro->table_is( '', $table_name, 'ms_global' ) ) {
				return true;
			}

			global $wpdb;
			$prefix         = $wpdb->base_prefix;
			$prefix_escaped = preg_quote( $prefix );

			if ( 1 == $blog_id ) {
				// Exclude tables from non-primary subsites.
				if ( preg_match( '/^' . $prefix_escaped . '([0-9]+)_/', $table_name, $matches ) ) {
					$exclude = true;
				}
			} else {
				$prefix .= $blog_id . '_';
				if ( 0 !== stripos( $table_name, $prefix ) ) {
					$exclude = true;
				}
			}
		}

		return $exclude;
	}

	/**
	 * Filter the given tables if doing a subsite migration.
	 *
	 * @param array $tables
	 * @param string $scope
	 *
	 * @return array
	 */
	function filter_tables_for_subsite( $tables, $scope = 'regular' ) {
		if ( empty( $tables ) ) {
			return $tables;
		}

		// We will not alter backup or temp tables list.
		if ( in_array( $scope, array( 'backup', 'temp' ) ) ) {
			return $tables;
		}

		$filtered_tables = array();
		$blog_id         = $this->subsite_for_export();

		if ( 0 < $blog_id ) {
			foreach ( $tables as $key => $value ) {
				if ( false === $this->filter_table_for_subsite( false, $value ) ) {
					$filtered_tables[ $key ] = $value;
				}
			}
		} else {
			$filtered_tables = $tables;
		}

		return $filtered_tables;
	}

	/**
	 * Filter the given tables with sizes if doing a subsite migration.
	 *
	 * @param array $table_sizes
	 * @param string $scope
	 *
	 * @return array
	 */
	function filter_table_sizes_for_subsite( $table_sizes, $scope = 'regular' ) {
		if ( empty( $table_sizes ) ) {
			return $table_sizes;
		}

		$tables = $this->filter_tables_for_subsite( array_keys( $table_sizes ), $scope );

		return array_intersect_key( $table_sizes, array_flip( $tables ) );
	}

	/**
	 * Change the name of the given table if subsite export and migration profile has new prefix.
	 *
	 * @param string $table_name
	 * @param string $action
	 * @param string $stage
	 *
	 * @return string
	 */
	function filter_target_table_name( $table_name, $action, $stage ) {
		$blog_id = $this->subsite_for_export();

		if ( 1 > $blog_id || 'backup' == $stage ) {
			return $table_name;
		}

		$new_prefix = $this->wpmdbpro->profile_value( 'new_prefix' );

		if ( empty( $new_prefix ) ) {
			return $table_name;
		}

		global $wpdb;
		$prefix = $wpdb->base_prefix;
		if ( 1 < $blog_id && ! $this->wpmdbpro->table_is( '', $table_name, 'global', '', $blog_id ) ) {
			$prefix .= $blog_id . '_';
		}

		if ( 0 === stripos( $table_name, $prefix ) ) {
			$table_name = substr_replace( $table_name, $new_prefix, 0, strlen( $prefix ) );
		}

		return $table_name;
	}

	/**
	 * Handler for the wpmdb_options_row filter.
	 * The given $row can be modified, but if we return false the row will not be used.
	 *
	 * @param stdClass $row
	 * @param string $table_name
	 * @param string $action
	 * @param string $stage
	 *
	 * @return bool
	 */
	function filter_table_row( $row, $table_name, $action, $stage ) {
		$use     = true;
		$blog_id = $this->subsite_for_export();

		if ( 1 > $blog_id || 'backup' == $stage ) {
			return $use;
		}

		$new_prefix = $this->wpmdbpro->profile_value( 'new_prefix' );

		if ( empty( $new_prefix ) ) {
			return $row;
		}

		global $wpdb;

		$prefix = $wpdb->base_prefix;
		if ( 1 < $blog_id ) {
			$prefix .= $blog_id . '_';
		}

		if ( $this->wpmdbpro->table_is( 'options', $table_name ) ) {
			// Rename options records like wp_X_user_roles to wp_user_roles otherwise no users can do anything in the single site install.
			if ( 0 === stripos( $row->option_name, $prefix ) ) {
				$row->option_name = substr_replace( $row->option_name, $new_prefix, 0, strlen( $prefix ) );
			}
		}

		if ( $this->wpmdbpro->table_is( 'usermeta', $table_name ) ) {
			if ( 1 == $blog_id ) {
				$prefix_escaped = preg_quote( $wpdb->base_prefix );
				if ( 1 === preg_match( '/^' . $prefix_escaped . '([0-9]+)_/', $row->meta_key, $matches ) ) {
					// Remove non-primary subsite records from usermeta when exporting primary subsite.
					$use = false;
				} elseif ( 0 === stripos( $row->meta_key, $prefix ) ) {
					// Rename prefixed keys.
					$row->meta_key = substr_replace( $row->meta_key, $new_prefix, 0, strlen( $prefix ) );
				}
			} else {
				if ( 0 === stripos( $row->meta_key, $prefix ) ) {
					// Rename prefixed keys.
					$row->meta_key = substr_replace( $row->meta_key, $new_prefix, 0, strlen( $prefix ) );
				} elseif ( 0 === stripos( $row->meta_key, $wpdb->base_prefix ) ) {
					// Remove wp_* records from usermeta not for extracted subsite.
					$use = false;
				}
			}
		}

		return $use;
	}

	/**
	 * Handler for the wpmdb_find_and_replace filter.
	 *
	 * @param array $tmp_find_replace_pairs
	 * @param string $intent
	 * @param string $site_url
	 *
	 * @return array
	 */
	function filter_find_and_replace( $tmp_find_replace_pairs, $intent, $site_url ) {
		$blog_id = $this->subsite_for_export();

		if ( 1 > $blog_id || 'savefile' != $intent ) {
			return $tmp_find_replace_pairs;
		}

		$subsite_path = '//' . untrailingslashit( $this->scheme_less_url( get_blogaddress_by_id( $blog_id ) ) );

		$primary_path = '//' . untrailingslashit( $this->scheme_less_url( $site_url ) );

		if ( 1 < $blog_id && ! get_site_option( 'ms_files_rewriting' ) ) {
			$upload_dir = wp_upload_dir();

			if ( defined( 'MULTISITE' ) ) {
				$ms_dir = '/sites/' . $blog_id;
			} else {
				$ms_dir = '/' . $blog_id;
			}
			$uploads_path = '//' . $this->scheme_less_url( $upload_dir['baseurl'] ) . $ms_dir;
		}

		foreach ( $tmp_find_replace_pairs as $find => $replace ) {
			if ( $find == $subsite_path ) {
				$tmp_find_replace_pairs[ $primary_path ] = $replace;

				if ( isset( $uploads_path ) && isset( $ms_dir ) ) {
					$uploads_path                            = str_replace( $primary_path, $replace, $uploads_path );
					$uploads_path_new                        = substr( $uploads_path, 0, - strlen( $ms_dir ) );
					$tmp_find_replace_pairs[ $uploads_path ] = $uploads_path_new;
				}
				break;
			}
		}

		return $tmp_find_replace_pairs;
	}
}
