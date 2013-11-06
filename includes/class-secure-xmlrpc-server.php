<?php
include_once(ABSPATH . 'wp-admin/includes/admin.php');
include_once(ABSPATH . WPINC . '/class-IXR.php');
include_once(ABSPATH . WPINC . '/class-wp-xmlrpc-server.php');

/**
 * Secure XML-RPC Server Implementation
 *
 * Extends OAuth-style security for remote procedure calls so they don't need to pass username/password credentials
 * in plaintext with the request.
 *
 * @subpackage Publishing
 */
class secure_xmlrpc_server extends wp_xmlrpc_server {
	public function __construct() {
		add_filter( 'xmlrpc_methods', array( $this, 'add_methods' ), 10, 1 );

		parent::__construct();
	}

	/**
	 * Filter default methods and overload with our secure implementation.
	 *
	 * @param array $methods
	 *
	 * @return array
	 */
	public function add_methods( $methods ) {
		$methods['wp.getPost'] = array( $this, 'wp_getPost' );

		return $methods;
	}

	/**
	 * Add an X-Deprecated header.
	 *
	 * @param string $message
	 */
	protected function deprecated( $message ) {
		header( 'X-Deprecated: ' . $message );
	}

	/**
	 * Overload the existing wp.getPost method.
	 *
	 * @param array $args
	 *
	 * @return array|void
	 */
	public function wp_getPost( $args ) {
		if ( ! empty( $args[1] ) || ! empty( $args[2] ) ) {
			$this->deprecated( __( 'Username/Password authentication is deprecated for XML-RPC requests.', 'xmlrpcs' ) );
		}

		return parent::wp_getPost( $args );
	}
}