<?php
/**
 * Plugin Name: Secure XML-RPC
 * Plugin URI:  http://wordpress.org/plugins/secure-xmlrpc
 * Description: More secure wrapper for the WordPress XML-RPC interface.
 * Version:     1.0.0
 * Author:      Eric Mann
 * Author URI:  http://eamann.com
 * License:     GPLv2+
 * Text Domain: xmlrpcs
 * Domain Path: /languages
 */

/**
 * Copyright (c) 2013-4 Eric Mann (email : eric@eamann.com)
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
define( 'XMLRPCS_VERSION', '1.0.0' );
define( 'XMLRPCS_URL',     plugin_dir_url( __FILE__ ) );
define( 'XMLRPCS_PATH',    dirname( __FILE__ ) . '/' );

// Require includes
require_once( 'includes/XMLRPCS_Profile.php' );
require_once( 'includes/class-secure-xmlrpc-server.php' );

// Wireup actions
add_action( 'init',                  array( 'XMLRPCS_Profile', 'init' ),               10, 0 );
add_action( 'show_user_profile',     array( 'XMLRPCS_Profile', 'append_secure_keys' ), 10, 1 );
add_action( 'admin_enqueue_scripts', array( 'XMLRPCS_Profile', 'admin_enqueues' )            );
add_action( 'profile_update',        array( 'XMLRPCS_Profile', 'profile_update' ),     10, 1 );

// Wireup filters
add_filter( 'wp_xmlrpc_server_class', array( 'XMLRPCS_Profile', 'server' ),       10, 1 );
add_filter( 'authenticate',           array( 'XMLRPCS_Profile', 'authenticate' ), 10, 3 );

// Wireup ajax
add_action( 'wp_ajax_xmlrpcs_new_app', array( 'XMLRPCS_Profile', 'new_app' ) );
