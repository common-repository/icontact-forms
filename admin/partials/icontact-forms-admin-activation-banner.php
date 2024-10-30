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
?>
<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="notice notice-info is-dismissible icform-activation-banner">
	<div>
		<p>Get the most out of the <strong>iContact Forms</strong> plugin -- use it with an active iContact account.</p>
	</div>
	<div id="activation_banners_links">
		<a class="inline" href="<?php print esc_url_raw( get_admin_url() . 'admin.php?page=' . ICONTACT_FORMS_MENU_SLUG ); ?>-settings">
			<p class="submit">
				<button type="button" name="connect" id="connect" class="button button-primary button-large">Connect your account</button>
			</p>
		</a>
		<a class="inline" href="https://www.icontact.com/wordpress" target="_blank" >
			<p class="submit">
				<button type="button" name="connect" id="connect" class="button">Try Free</button>
			</p>			
		</a>
		<p class="inline"><a href="<?php print esc_url_raw( get_admin_url() . 'admin.php?page=' . ICONTACT_FORMS_MENU_SLUG ); ?>-about">Learn more</a> about the power of email marketing.</p>
	</div>
</div>
