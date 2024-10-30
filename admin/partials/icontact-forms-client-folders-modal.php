<?php
/**
 * Modal to show if user have multiple iContact client folders
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
	wp_die( 'You don\'t have access to this page' );
}

?>
<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div id="icform-folders-modal" style="display:none;">
	<div class="icform-folders-modal-content">
		<h1>Congrats your account is connected.</h1>
		<p>Your account has multiple client folders. Please select which client folder you would like to use.</p>
		<form  method="POST">
		<input type="hidden" name="action" value="save_folder">
		<input type="hidden" name="_wpnonce" value="<?php echo esc_attr( $args['wpnonce'] ); ?>" />
		<select name="folder" id="icform-folders">
			<?php
			foreach ( $args['client_folders'] as $folder ) {
				$folder = (array) $folder;
				echo sprintf( '<option value="%s">%s</option>', esc_attr( $folder['clientFolderId'] ), esc_attr( $folder['name'] ) );
			}
			?>
		</select>
		<?php submit_button( 'Connect' ); ?>
		</form>
	</div>
</div>
<script>
	imgLoader = new Image(); 
</script>
