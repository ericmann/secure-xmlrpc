<?php
class XMLRPCS_Profile {
	/**
	 * Append the new UI to the user profile.
	 *
	 * @param WP_User $profileuser
	 */
	public static function append_secure_keys( $profileuser ) {
		?>
<h3><?php _e( 'Remote Publishing Permissions', 'xmlrpcs' ); ?></h3>
<table class="form-table">
	<tr>
		<th scope="row"><?php _e( 'Allowed applications', 'xmlrpcs' ); ?></th>
		<td></td>
	</tr>
	<tr>
		<th scope="row"><?php _e( 'Add a new application', 'xmlrpcs' ); ?></th>
		<td><a id="xmlrpcs-generate" href=""><?php _e( 'Generate application keys', 'xmlrpcs' ); ?></a></td>
	</tr>
</table>
<?php
	}
}