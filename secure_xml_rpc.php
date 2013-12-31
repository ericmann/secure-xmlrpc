<?php
/**
 * Plugin Name: Secure XML-RPC
 * Plugin URI:  http://wordpress.org/plugins/secure-xmlrpc
 * Description: More secure wrapper for the WordPress XML-RPC interface.
 * Version:     0.1.0
 * Author:      Eric Mann
 * Author URI:  http://eamann.com
 * License:     GPLv2+
 * Text Domain: xmlrpcs
 * Domain Path: /languages
 */

/**
 * Copyright (c) 2013 Eric Mann (email : eric@eamann.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

/**
 * Built using grunt-wp-plugin
 * Copyright (c) 2013 10up, LLC
 * https://github.com/10up/grunt-wp-plugin
 */

// Useful global constants
define( 'XMLRPCS_VERSION', '0.1.0' );
define( 'XMLRPCS_URL',     plugin_dir_url( __FILE__ ) );
define( 'XMLRPCS_PATH',    dirname( __FILE__ ) . '/' );

// Require includes
require_once( 'includes/XMLRPCS_Profile.php' );
require_once( 'includes/class-secure-xmlrpc-server.php' );

// Set up our pseudo-namespace
XMLRPCS_Profile::setup();

/**
 * Default initialization for the plugin:
 * - Registers the default textdomain.
 */
function xmlrpcs_init() {
	$locale = apply_filters( 'plugin_locale', get_locale(), 'xmlrpcs' );
	load_textdomain( 'xmlrpcs', WP_LANG_DIR . '/xmlrpcs/xmlrpcs-' . $locale . '.mo' );
	load_plugin_textdomain( 'xmlrpcs', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

/**
 * Activate the plugin
 */
function xmlrpcs_activate() {
	// First load the init scripts in case any rewrite functionality is being loaded
	xmlrpcs_init();

	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'xmlrpcs_activate' );

/**
 * Deactivate the plugin
 * Uninstall routines should be in uninstall.php
 */
function xmlrpcs_deactivate() {

}
register_deactivation_hook( __FILE__, 'xmlrpcs_deactivate' );

/**
 * Replace default server implementation with custom subclass.
 *
 * @param string $server_class
 *
 * @return string
 */
function xmlrpcs_server( $server_class ) {
	return 'secure_xmlrpc_server';
}

// Wireup actions
add_action( 'init', 'xmlrpcs_init' );
add_action( 'show_user_profile', array( 'XMLRPCS_Profile', 'append_secure_keys' ), 10, 1 );

// Wireup filters
add_filter( 'wp_xmlrpc_server_class', 'xmlrpcs_server' );

// Wireup shortcodes
