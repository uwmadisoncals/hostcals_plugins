<?php
function connectionsLinkManage( $action, $id = NULL ) {
	global $connections;

	$form = new cnFormObjects();

	$connections->displayMessages();

	echo '<div class="wrap">';
	screen_icon( 'connections' );

	if ( empty( $id ) ) {

		// Ensure the action is 'add'.
		$action = 'add';

		echo '<h2>Connections : ' , __( 'Add My Directory Entry', 'connections_link' ) , '</h2>';

	} else {
		echo '<h2>Connections : ' , __( 'Edit My Directory Entry', 'connections_link' ) , '</h2>';
	}

	// Limit the entry types to just the individual and organization entry types.
	add_filter( 'cn_admin_metabox_publish_atts', array( 'Connections_Link' , 'alterEntryTypes' ) );

	switch ( $action ) {

		case 'add':

			/*
			 * Check whether current user can add an entry.
			 */
			if ( current_user_can( 'connections_add_entry' ) || current_user_can( 'connections_add_entry_moderated' ) ) {

				add_meta_box( 'metabox-name', 'Name', array( $form, 'metaboxName' ), $connections->pageHook->link, 'normal', 'high' );

				$entry = new cnEntry();

				echo '<div id="poststuff" class="metabox-holder has-right-sidebar">';

				$attr = array(
					'action'  => 'admin.php?connections_process=true&process=manage&action=add',
					'method'  => 'post',
					'enctype' => 'multipart/form-data',
				);

				$form->open( $attr );

				wp_nonce_field( 'cn-add-user-metaboxes' );
				wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', FALSE );
				wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', FALSE );
				echo '<input type="hidden" name="action" value="save_cn_add_metaboxes" />';

				$form->tokenField( 'add_entry', FALSE, '_cn_wpnonce', FALSE );

				$form->tokenField( 'add_user', $connections->currentUser->getID(), '_cn_add_user_nonce', FALSE );
				echo '<input type="hidden" name="add_user_id" value="' . $connections->currentUser->getID() . '" />';

				echo '<input type="hidden" name="redirect" value="' . add_query_arg( array( 'page' => 'connections_link' ), 'users.php' ) . '" />';

				echo '<div id="side-info-column" class="inner-sidebar">';
				do_meta_boxes( $connections->pageHook->link, 'side', $entry );
				echo '</div>';


				echo '<div id="post-body" class="has-sidebar">';
				echo '<div id="post-body-content" class="has-sidebar-content">';
				do_meta_boxes( $connections->pageHook->link, 'normal', $entry );
				echo '</div>';
				echo '</div>';

				$form->close();

				echo '</div>';

				unset( $entry );
			} else {
				echo '<div id="message" class="error"><p>' , __( '<strong>ERROR:</strong> You are not authorized to add entries. Please contact the admin if you received this message in error.', 'connections_link' ) , '</p></div>';
			}

			break;

		case 'edit':

			/*
			 * Check whether the current user can edit entries.
			 */
			if ( current_user_can( 'connections_edit_entry' ) || current_user_can( 'connections_edit_entry_moderated' ) ) {

				// Set the Publish metabox action to 'update'.
				add_filter( 'cn_admin_metabox_publish_atts', array( 'Connections_Link', 'publishActionEdit' ) );

				add_meta_box( 'metabox-name', 'Name', array( $form, 'metaboxName' ), $connections->pageHook->link, 'normal', 'high' );

				$form = new cnFormObjects();
				$entry = new cnEntry( $connections->retrieve->entry( $id ) );

				if ( $entry->getStatus() == 'pending' ) echo '<div id="message" class="updated"><p>' , __( '<strong>NOTICE:</strong> Your entry submission is currently under review, however, you can continue to make edits to your entry submission while your submission is under review.', 'connections_link' ) , '</p></div>';

				echo '<div id="poststuff" class="metabox-holder has-right-sidebar">';

				$attr = array(
					'action'  => 'admin.php?connections_process=true&process=manage&action=update&id=' . $id,
					'method'  => 'post',
					'enctype' => 'multipart/form-data',
				);

				$form->open( $attr );

				wp_nonce_field( 'cn-add-user-metaboxes' );
				wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', FALSE );
				wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', FALSE );
				echo '<input type="hidden" name="action" value="save_cn_add_metaboxes" />';

				$form->tokenField( 'update_entry', FALSE, '_cn_wpnonce', FALSE );

				$form->tokenField( 'update_user', $connections->currentUser->getID(), '_cn_update_user_nonce', FALSE );
				echo '<input type="hidden" name="update_user_id" value="' . $connections->currentUser->getID() . '" />';

				echo '<input type="hidden" name="redirect" value="' . add_query_arg( array( 'page' => 'connections_link' ), 'users.php' ) . '" />';

				echo '<div id="side-info-column" class="inner-sidebar">';
				do_meta_boxes( $connections->pageHook->link, 'side', $entry );
				echo '</div>';


				echo '<div id="post-body" class="has-sidebar">';
				echo '<div id="post-body-content" class="has-sidebar-content">';
				do_meta_boxes( $connections->pageHook->link, 'normal', $entry );
				echo '</div>';
				echo '</div>';

				$form->close();

				echo '</div>';

				unset( $entry );
			}
			else {
				echo '<div id="message" class="error"><p>' , __( '<strong>ERROR:</strong> You are not authorized to edit entries. Please contact the admin if you received this message in error.', 'connections_link' ) , '</p></div>';
			}
			break;
	}
?>
	<script type="text/javascript">
		//<![CDATA[
		jQuery(document).ready( function($) {
			// close postboxes that should be closed
			$('.if-js-closed').removeClass('if-js-closed').addClass('closed');
			// postboxes setup
			postboxes.add_postbox_toggles('<?php echo $connections->pageHook->link ?>');
		});
		//]]>
	</script>

	</div>
<?php }