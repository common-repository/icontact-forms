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
<div class="icontact-forms-top-menu">
	<div class="icontact-forms">
		<div class="icontact-forms column">
			<div class="icontact-forms top-menu logo">
				<img width="176" height="43" src="<?php echo esc_url( ICONTACT_FORMS__PLUGIN_URL . '/admin/images/icontact-pc-logo-top-menu.svg' ); ?>" class="icontact-forms top-menu logo" alt="iContact Logo - Homepage">
			</div>            
		</div>
		<div class="icontact-forms column">
			<div class="icontact-forms top-menu submenu">
				<ul>
					<?php
					foreach ( $args['submenus'] as $top_submenu_id => $top_submenu ) {
						?>
					<li>
						<div class="icontact-forms <?php echo ( $top_submenu['view_name'] === $args['active_page'] ) ? 'active' : ''; ?>" ><a href="<?php echo esc_url( admin_url( 'admin.php?page=' . $top_submenu_id ) ); ?>"><span><?php echo esc_html( $top_submenu['name'] ); ?></span></a></div>
					</li>
						<?php
					}
					?>
				</ul>
			</div>            
		</div>
		<div class="icontact-forms column connected">
			<div>
					<?php
					if ( $args['connected'] ) {
						?>
							<div class="inline status">
								<span class="logged-in">●</span>
								<span>Connected</span>
								</div>
							<div class="inline">
								<input type="hidden" name="topmenu-settings-options" id="action" value="<?php echo esc_attr( $args['option_prefix'] ); ?>disconnect_button"></input>
								<input type="hidden" name="topmenu-settings-options" id="<?php echo esc_html( $args['option_prefix'] ); ?>disconnect_button_nonce" value="<?php echo esc_html( $args['wpnonce'] ); ?>" /></input>
								<button type="button" id="icontact-forms-disconnect-button"  class="button button-primary">Disconnect</button>
							</div>
						<?php
					} else {
						?>
						<div class="inline">
							<span class="logged-out">●</span>
							<span>Disconnected</span>
						</div>
						<?php
					}
					?>
			</div>
		</div>
	</div>
</div>
