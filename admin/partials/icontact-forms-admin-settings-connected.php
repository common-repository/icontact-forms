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
	wp_die( 'You don\'t have access to this page' );
}

?>
<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">		
	<div>
		<p> You are connected to your iContact account. </p>
		<p> <strong> Username /Email Address: </strong> <?php echo esc_html( $args['username'] ); ?> </p>
		<?php
		if ( isset( $args['client_folder_name'] ) ) {
			echo sprintf( '<strong>Client folder:</strong> ' . esc_attr( $args['client_folder_name'] ) );
		}
		?>
		<p> To disconnect your account, click the Disconnect button in the upper right corner.</p>
	</div>
</div>
