<?php
class XMLRPCS_Profile {
	/**
	 * Enqueue our admin-side scripts, styles, and localizations
	 */
	public static function admin_enqueues() {
		$screen = get_current_screen();
		if ( 'profile' !== $screen->base ) {
			return;
		}

		$ext = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '.js' : '.min.js';

		wp_enqueue_script( 'xmlrpcs', XMLRPCS_URL . "/assets/js/secure_xml_rpc{$ext}", array( 'jquery' ), XMLRPCS_VERSION, true );
		wp_localize_script(
			'xmlrpcs',
			'xmlrpcs',
			array(
			     'ajaxurl'   => admin_url( 'admin-ajax.php' ),
			     'confirm_delete' => __( 'Are you sure you wish to remove this application? This action cannot be undone.', 'xmlrpcs' ),
			     'new_nonce' => wp_create_nonce( 'xmlrpcs_new_app' ),
			)
		);

		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
			wp_enqueue_style( 'xmlrpcs', XMLRPCS_URL . "/assets/css/src/secure_xml_rpc.css", array(), XMLRPCS_VERSION );
		} else {
			wp_enqueue_style( 'xmlrpcs', XMLRPCS_URL . "/assets/css/secure_xml_rpc.min.css", array(), XMLRPCS_VERSION );
		}
	}

	/**
	 * Append the new UI to the user profile.
	 *
	 * @param WP_User $profileuser
	 */
	public static function append_secure_keys( $profileuser ) {
		?>
<h3><?php esc_html_e( 'Remote Publishing Permissions', 'xmlrpcs' ); ?></h3>
<table class="form-table xmlrpcs_permissions">
<tbody>
	<tr>
		<th scope="row"><?php esc_html_e( 'Allowed applications', 'xmlrpcs' ); ?></th>
		<td><?php echo XMLRPCS_Profile::secure_keys_list( $profileuser ); ?></td>
	</tr>
	<tr>
		<th scope="row"><?php esc_html_e( 'Add a new application', 'xmlrpcs' ); ?></th>
		<td><a id="xmlrpcs-generate" href=""><?php esc_html_e( 'Generate application keys', 'xmlrpcs' ); ?></a></td>
	</tr>
</tbody>
</table>
<?php
	}

	/**
	 * Generate a table of the secure keys for the given user.
	 *
	 * @param WP_User $profileuser
	 *
	 * @return string
	 */
	public static function secure_keys_list( $profileuser ) {
		$keys = get_user_meta( $profileuser->ID, '_xmlrpcs' );

		$output = '<table id="xmlrpcs_app_body">';
		$output .= '<thead>';
		$output .= '<tr><th>' . esc_html__( 'Application', 'xmlrpcs' ) . '</th><th>' . esc_html__( 'Public Key', 'xmlrpcs' ) . '</th><th>' . esc_html__( 'Secret Key', 'xmlrcps' ) . '</th></tr>';
		$output .= '</thead>';
		$output .= '<tbody>';

		if ( count( $keys ) > 0 ) {
			foreach( $keys as $key ) {
				$app = get_user_meta( $profileuser->ID, '_xmlrpcs_app_' . $key, true );
				$secret = get_user_meta( $profileuser->ID, '_xmlrpcs_secret_' . $key, true );

				$output .= '<tr>';
				$output .= '<td><input name="xmlrpcs_app[]" class="app_name" type="text" value="' . esc_attr( $app ) . '" /></td>';
				$output .= '<td><input name="xmlrpcs_key[]" class="app_key" type="text" value="' . esc_attr( $key ) . '" readonly /></td>';
				$output .= '<td><input class="app_key" type="text" value="' . esc_attr( $secret ) . '" readonly /></td>';
				$output .= '<td><span class="dashicons dashicons-no xmlrpcs-delete"></span></td>';
				$output .= '</tr>';
			}
		} else {
			$output .= '<tr id="xmlrpcs-no-apps"><td colspan="4">' . esc_html__( 'No applications currently authorized' ) . '</td></tr>';
		}

		$output .= '</tbody></table>';

		return $output;
	}

	/**
	 * Create a new app for the current user.
	 */
	public static function new_app() {
		if ( ! wp_verify_nonce( $_POST['_nonce'] , 'xmlrpcs_new_app' ) ) {
			wp_send_json_error();
		}

		// Get the current user
		$user = wp_get_current_user();

		// Generate a set of unique keys
		$key = apply_filters( 'xmlrpcs_public_key', wp_hash( time() . rand(), 'auth' ) );;
		$secret = apply_filters( 'xmlprcs_secret_key', wp_hash( time() . rand() . $key, 'auth' ) );

		add_user_meta( $user->ID, '_xmlrpcs', $key, false );
		add_user_meta( $user->ID, "_xmlrpcs_secret_{$key}", $secret, true );
		add_user_meta( $user->ID, "_xmlrpcs_app_{$key}", __( 'New Application', 'xmlrpcs' ), true );

		// Generate the output
		echo '<tr>';
		echo '<td><input name="xmlrpcs_app[]" class="app_name" type="text" value="' . esc_attr__( 'New Application', 'xmlrpcs' ) . '" /></td>';
		echo '<td><input name="xmlrpcs_key[]" class="app_key" type="text" value="' . esc_attr( $key ) . '" readonly /></td>';
		echo '<td><input class="app_key" type="text" value="' . esc_attr( $secret ) . '" readonly /></td>';
		echo '<td><span class="dashicons dashicons-no xmlrpcs-delete"></span></td>';
		echo '</tr>';
		die();
	}

	/**
	 * Update the user's secure keys.
	 *
	 * @param $user_id
	 */
	public static function profile_update( $user_id ) {
		// Get the current user
		$user = wp_get_current_user();

		// Can only edit your own profile!!!
		if ( $user_id !== $user->ID ) {
			return;
		}

		// Get the POSTed data
		$apps = $_POST['xmlrpcs_app'];
		$keys = $_POST['xmlrpcs_key'];
		$apps = array_map( 'sanitize_text_field', $apps );
		$keys = array_map( 'sanitize_text_field', $keys );

		// Get the user's existing keys so we can remove any that have been deleted
		$existing = get_user_meta( $user_id, '_xmlrpcs' );
		$to_remove = array_diff( $existing, $keys );

		foreach( $to_remove as $remove ) {
			delete_user_meta( $user_id, "_xmlrpcs_secret_{$remove}" );
			delete_user_meta( $user_id, "_xmlrpcs_app_{$remove}" );
		}

		// Remove existing keys so we can update just the ones we want to keep
		delete_user_meta( $user_id, '_xmlrpcs' );

		// Update the application names
		foreach( $keys as $index => $key ) {
			add_user_meta( $user_id, '_xmlrpcs', $key );
			update_user_meta( $user_id, "_xmlrpcs_app_{$key}", $apps[ $index ] );
		}
	}

	/**
	 * Overload the authentication system to authenticate using headers instead of by username/password.
	 *
	 * @param null|WP_User $user
	 * @param string       $username
	 * @param string       $password
	 *
	 * @return null|WP_Error|WP_User
	 */
	public static function authenticate( $user, $username, $password ) {
		// Bail if this isn't an XML-RPC request.
		if ( ! defined( 'XMLRPC_REQUEST' ) || ! XMLRPC_REQUEST ) {
			return $user;
		}

		// If the user is already logged in, do nothing.
		if ( is_a( $user, 'WP_User' ) ) {
			return $user;
		}

		// Get the authentication information from the POST headers
		if ( ! isset( $_SERVER['HTTP_AUTHORIZATION'] ) ) {
			return $user;
		}

		$tokens = explode( '||', $_SERVER['HTTP_AUTHORIZATION'] );
		$key = $tokens[0];
		$hash = $tokens[1];

		// Lookup the user based on the key passed in.
		$user_query = new WP_User_Query(
			array(
			     'meta_query' => array(
				     array(
					     'key' => '_xmlrpcs',
					     'value' => $key
				     )
			     )
			)
		);

		// If we don't find anyone, bail.
		if ( count( $user_query->results ) === 0 ) {
			return $user;
		}

		// OK, we've found someone. Now, verify the hashes match.
		$found = $user_query->results[0];
		$secret = get_user_meta( $found->ID, "_xmlrpcs_secret_{$key}", true );

		if ( ! $secret ) {
			return $user;
		}

		// Calculate the hash independently
		$body = @file_get_contents('php://input');
		$calculated = hash( 'sha256', $secret . $body );

		if ( $calculated === $hash ) {
			return $found;
		} else {
			return $user;
		}
	}
}