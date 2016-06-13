<?php
/*
Plugin Name: MailPoet retro safe
Plugin URI: http://wordpress.org/extend/plugins/wysija-newsletters
Description: This plugins protects older MailPoet/Wysija versions than version 2.6.7 from the following security issue http://www.mailpoet.com/critical-security-update-mailpoet-2-6-7
Author: Wysija
Version: 1.0
Author URI: http://www.mailpoet.com
*/

add_action( 'admin_init' , 'mailpoet_retro_safe' , 1 );

/**
 * this function will check the role of the user executing the action, if it's called from another
 * WordPress admin page than page.php for instance admin-post.php
 * @return boolean
 */
function mailpoet_retro_safe(){
    if( isset( $_GET['page'] ) && substr( $_GET['page'] ,0 ,7 ) == 'wysija_' ){

        switch( $_GET['page'] ){
            case 'wysija_campaigns':
                $role_needed = 'wysija_newsletters';
                break;
            case 'wysija_subscribers':
                $role_needed = 'wysija_subscribers';
                break;
            case 'wysija_config':
                $role_needed = 'wysija_config';
                break;
            case 'wysija_statistics':
                $role_needed = 'wysija_stats_dashboard';
                break;
            default:
                $role_needed = 'switch_themes';
        }

        if( current_user_can( $role_needed ) ){
            return true;
        } else{
            die( 'You are not allowed here.' );
        }

    }else{
        // this is not a wysija interface/action we can let it pass
        return true;
    }
}

function mpoet_retro_safe_filter_plugin_updates( $value ) {
    if( isset( $value->response['mpoet-retro-secured.php'] ) ){
        unset( $value->response['mpoet-retro-secured.php'] );
    }
    if( isset( $value->response['mpoet-retro-secured/mpoet-retro-secured.php'] ) ){
        unset( $value->response['mpoet-retro-secured/mpoet-retro-secured.php'] );
    }
    return $value;
}
add_filter( 'site_transient_update_plugins', 'mpoet_retro_safe_filter_plugin_updates' );