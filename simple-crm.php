<?php
/*
Plugin Name: Simple CRM
Plugin URI: http://wordpress.org/extend/plugins/simple-crm/
Description: Simple CRM lets you define custom fields to extend user profiles and it is also a framework for integration with all kind of CRM API webservices.
Author: Stas Sușcov
Version: 0.1
Author URI: http://stas.nerd.ro/
*/

define( 'SCRM_ROOT', dirname( __FILE__ ) );
define( 'SCRM_WEB_ROOT', WP_PLUGIN_URL . '/' . basename( SCRM_ROOT ) );

require_once SCRM_ROOT . '/includes/crm.class.php';

/**
 * i18n
 */
function scrm_textdomain() {
    load_plugin_textdomain( 'scrm', false, basename( SCRM_ROOT ) . '/languages' );
}
add_action( 'init', 'scrm_textdomain' );

SCRM::init();

?>
