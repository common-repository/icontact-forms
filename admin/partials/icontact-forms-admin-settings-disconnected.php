<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://icontact.com
 * @since      1.0.0
 *
 * @package    Icontact_Forms
 * @subpackage Icontact_Forms/admin/partials
 */

namespace Icontact\Icontact_Forms;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
* Check if user has access to this page
*/
if ( ! current_user_can( 'manage_options' ) ) {
	wp_die( esc_html( 'You don\'t have access to this page' ) );
}

?>
<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">	
	<form  method="POST">
		<input type="hidden" name="action" value="save_credentials">
		<input type="hidden" name="_wpnonce" value="<?php echo esc_attr( $args['wpnonce'] ); ?>" />
		<h2 class="title">Allow WordPress to access your iContact Account?</h2>
		<div>
			<p>Enter the information below to connect your iContact account. This is not your iContact login information. You can find this information by clicking <a href="<?php echo esc_url( ICONTACT_APIURL ); ?>/core/fusion/settings/integrations/wordpress" target="_blank">here</a>.</p>
		</div>
		<table id="api-credentials" class="form-table" role="presentation">
			<tbody>
				<tr valign="top">
					<th scope="row">
						<label for="<?php echo esc_attr( $args['option_prefix'] ); ?>_appid">Application ID (AppId)</label>
					</th>
					<td>
						<input required type="text" class="regultar-text ltr" name="<?php echo esc_attr( $args['option_prefix'] ); ?>appid" id="<?php echo esc_attr( $args['option_prefix'] ); ?>appid" value="<?php echo esc_attr( $args['appid'] ); ?>"></input>
						<br/>
						<small>Go to Settings & Billings > iContact Integrations > Click the View Details button on the WordPress Plugin integration to find this information.</small>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="<?php echo esc_attr( $args['option_prefix'] ); ?>username">Username / Email Address</label></th>
					<td>
						<input required type="text" class="regultar-text ltr" name="<?php echo esc_attr( $args['option_prefix'] ); ?>username" id="<?php echo esc_attr( $args['option_prefix'] ); ?>username" value="<?php echo esc_attr( $args['username'] ); ?>"></input>
						<br/>
						<small>This is the email address or username associated with your iContact account.</small>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="<?php echo esc_attr( $args['option_prefix'] ); ?>password">iContact API Password</label></th>
					<td>
					<input required type="password" class="regultar-text ltr" name="<?php echo esc_attr( $args['option_prefix'] ); ?>password" id="<?php echo esc_attr( $args['option_prefix'] ); ?>password" value="<?php echo esc_attr( $args['password'] ); ?>"></input>
						<br/>
						<small>This is NOT your iContact account password. Your password can be generated on the iContact Integrations WordPress Plugin details page.</small>
					</td>
				</tr>
			</tbody>
		</table> 
		<?php submit_button(); ?>
	</form>
</div>
