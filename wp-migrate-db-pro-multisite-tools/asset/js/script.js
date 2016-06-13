var wpmdb = wpmdb || {};
wpmdb.mediaFiles = {
	remote_media_files_unavailable: false
};

(function( $, wpmdb ) {
	var $mst_options = $( '.multisite-tools-options' );
	var $mst_subsite_export = $( '#multisite-subsite-export' );
	var $mst_options_content = $( '.multisite-tools-options .expandable-content' );
	var $mst_select_subsite = $( '#select-subsite' );
	var $mst_new_prefix_field = $( '.new-prefix-field' );
	var $mst_new_prefix = $( '#new-prefix' );

	var finished_loading = false;
	var original_local_url = null;
	var replace_right = false;
	var table_prefix = $( '.table-select-wrap .table-prefix' ).text();

	function is_multisite_subsite_export() {
		return '1' === $mst_subsite_export.attr( 'data-available' ) && $mst_subsite_export.is( ':checked' ) ? true : false;
	}

	function disable_multisite_tools_options() {
		$.wpmdb.do_action( 'wpmdb_enable_table_migration_options' );
		$mst_options_content.hide();
	}

	function enable_multisite_tools_options() {
		$mst_options_content.show();
	}

	function hide_show_options() {
		if ( 'false' === wpmdb_data.is_multisite || 'savefile' !== wpmdb_migration_type() ) {
			disable_multisite_tools_options();
			$mst_options.hide();
			return;
		}

		if ( is_multisite_subsite_export() ) {
			enable_multisite_tools_options();
			selected_subsite_changed();
		} else {
			disable_multisite_tools_options();
		}
		$mst_options.show();
	}

	function hide_show_new_prefix_field() {
		if ( '' === $mst_select_subsite.val() ) {
			$mst_new_prefix_field.hide();
		} else {
			$mst_new_prefix_field.show();
		}
	}

	function subsite_export_changed() {
		var args = is_multisite_subsite_export();

		$.wpmdb.do_action( 'wpmdbmst_subsite_export_changed', args );
	}

	function get_selected_subsite() {
		var details = {};
		if ( is_multisite_subsite_export() ) {
			var blog_id = $mst_select_subsite.find( 'option:selected' ).val();

			if ( '' === blog_id ) {
				details = false;
			} else {
				details.blog_id = blog_id;
				details.domain_and_path = $mst_select_subsite.find( 'option:selected' ).text();
			}
		}

		return details;
	}

	function selected_subsite_changed() {
		var selected_subsite = get_selected_subsite();
		$.wpmdb.do_action( 'wpmdbmst_selected_subsite_changed', selected_subsite );
	}

	function update_table_selects() {
		$.wpmdb.do_action( 'wpmdb_refresh_table_selects' );

		// At the moment we should only update the visible table select list if doing an Export (this may change later).
		if ( 'savefile' === wpmdb_migration_type() ) {
			$.wpmdb.do_action( 'wpmdb_update_push_table_select' );
		}

		// We may need to enable or disable the ability to select the "Migrate all tables with prefix ..." option.
		if ( is_multisite_subsite_export() ) {
			$.wpmdb.do_action( 'wpmdb_disable_table_migration_options' );

			// When switching subsites select all the tables unless still loading saved profile.
			if ( finished_loading ) {
				$.wpmdb.do_action( 'wpmdb_select_all_tables' );
			}
		} else {
			$.wpmdb.do_action( 'wpmdb_enable_table_migration_options' );
		}
	}

	function maybe_update_local_url_for_subsite( selected_subsite ) {
		var new_local_url = original_local_url;

		if ( undefined === selected_subsite ) {
			return;
		} else if ( undefined !== selected_subsite.domain_and_path ) {
			new_local_url = '//' + selected_subsite.domain_and_path;
		}

		if ( true === replace_right ) {
			$( '.replace-row.pin .replace-right-col input[type="text"]' ).val( new_local_url );
		} else {
			$( '.replace-row.pin .old-replace-col input[type="text"]' ).val( new_local_url );
		}
	}

	function is_subsite_table( table_prefix, table_name ) {
		var selected_subsite = get_selected_subsite();

		if ( undefined !== selected_subsite.blog_id ) {
			if ( 1 < selected_subsite.blog_id ) {
				table_prefix = table_prefix + selected_subsite.blog_id + '_';
				var pos = table_name.toLowerCase().indexOf( table_prefix.toLowerCase() );

				if ( 0 === pos ) {
					return true;
				}
			} else {
				var escaped_table_name = wpmdb.preg_quote( table_name );
				var regex = new RegExp( table_prefix + '([0-9]+)_', 'i' );
				var results = regex.exec( escaped_table_name );
				return null == results; // If null is returned, there was no match which is good in this case.
			}
		}

		return false;
	}

	function filter_table_for_subsite( exclude, table_name ) {
		if ( 'false' === wpmdb_data.is_multisite || 'savefile' !== wpmdb_migration_type() ) {
			return exclude;
		}

		if ( is_multisite_subsite_export() ) {

			// If there is no subsite selected then we should exclude all tables.
			if ( false === get_selected_subsite() ) {
				return true;
			}

			// If table does not have correct base table prefix for site then exclude from subsite export.
			if ( table_name.substr( 0, table_prefix.length ) !== table_prefix ) {
				return true;
			}

			// wp_users and wp_usermeta are relevant to all sites, shortcut out.
			if ( wpmdb.table_is( table_prefix, 'users', table_name ) || wpmdb.table_is( table_prefix, 'usermeta', table_name ) ) {
				return exclude;
			}

			// Following tables are Multisite setup tables and can be excluded from migration.
			// We'll handle getting any data we need from these tables elsewhere.
			var ms_tables = [ 'blog_versions', 'blogs', 'registration_log', 'signups', 'site', 'sitemeta' ];

			$.each( ms_tables, function( index, ms_table ) {
				if ( wpmdb.table_is( table_prefix, ms_table, table_name ) ) {
					exclude = true;
				}
			} );

			if ( false === is_subsite_table( table_prefix, table_name ) ) {
				exclude = true;
			}
		}

		return exclude;
	}

	function validate_new_prefix( new_prefix ) {
		var escaped_new_prefix = wpmdb.preg_quote( new_prefix );

		var regex = new RegExp( '[^a-z0-9_]', 'i' );
		var results = regex.exec( escaped_new_prefix );
		return null == results; // If null is returned, there was no match which is good in this case.
	}

	function filter_migration_profile_ready( value, args ) {
		if ( 'false' === wpmdb_data.is_multisite || 'savefile' !== wpmdb_migration_type() || false === is_multisite_subsite_export() ) {
			return value;
		}

		var new_prefix = $mst_new_prefix.val();

		if ( false === get_selected_subsite() ) {
			alert( wpmdbmst_strings.please_select_a_subsite );
			value = false;
		} else if ( 0 === new_prefix.trim().length ) {
			alert( wpmdbmst_strings.please_enter_a_prefix );
			value = false;
		} else if ( false === validate_new_prefix( new_prefix ) ) {
			alert( wpmdbmst_strings.new_prefix_contents );
			value = false;
		}

		return value;
	}

	// IMPORTANT: This action fires before find/replace columns are swapped for pull/push.
	$.wpmdb.add_action( 'move_connection_info_box', function( args ) {
		table_prefix = $( '.table-select-wrap .table-prefix' ).text();
		if ( null === original_local_url ) {
			original_local_url = $.wpmdb.apply_filters( 'wpmdb_base_old_url' );
		}

		if ( 'undefined' !== typeof args.migration_type && 'undefined' !== typeof args.last_migration_type ) {
			if ( 'savefile' === args.migration_type ) {
				if ( 'pull' === args.last_migration_type ) {
					replace_right = true;
				}
			} else if ( 'savefile' === args.last_migration_type ) {
				$( '.replace-row.pin .old-replace-col input[type="text"]' ).val( original_local_url );
			}
		}
		hide_show_options();
		wpmdb_toggle_migration_action_text();
		$.wpmdb.do_action( 'wpmdb_refresh_table_selects' );
		replace_right = false;
	} );

	$.wpmdb.add_action( 'wpmdbmst_subsite_export_changed', selected_subsite_changed );
	$.wpmdb.add_action( 'wpmdbmst_selected_subsite_changed', update_table_selects );
	$.wpmdb.add_action( 'wpmdbmst_selected_subsite_changed', maybe_update_local_url_for_subsite );
	$.wpmdb.add_action( 'wpmdbmst_selected_subsite_changed', hide_show_new_prefix_field );

	$.wpmdb.add_filter( 'wpmdb_exclude_table', filter_table_for_subsite );
	$.wpmdb.add_filter( 'wpmdb_migration_profile_ready', filter_migration_profile_ready );

	$( document ).ready( function() {
		$( 'body' ).on( 'change', '#multisite-subsite-export', function( e ) {
			subsite_export_changed();
		} );

		$( 'body' ).on( 'change', '#select-subsite', function( e ) {
			selected_subsite_changed();
		} );

		hide_show_options();
		finished_loading = true;
	} );

})( jQuery, wpmdb );
