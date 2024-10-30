<?php
/**
 * Fired during plugin deactivation
 *
 * @link       https://icontact.com
 * @since      1.0.0
 *
 * @package    Icontact_Forms
 * @subpackage Icontact_Forms/includes
 */

namespace Icontact\Icontact_Forms;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Icontact_Forms
 * @subpackage Icontact_Forms/includes
 * @author     iContact <wordpress@icontact.com>
 */
class Icontact_Forms_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		$deactivate_url = isset( $_SERVER['REQUEST_URI'] ) ? wp_sanitize_redirect( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
		if ( false === strstr( $deactivate_url, 'continue=1' ) ) {
			wp_die(
				'Please remove your iContact forms when you deactivate the plugin.</br>iContact forms will no longer render properly once deactivated.',
				'Warning',
				array(
					'response'  => 200,
					'link_url'  => esc_attr( $deactivate_url ) . '&continue=1',
					'link_text' => 'Continue plugin deactivation',
					'back_link' => true,
					'exit'      => true,
				)
			);
		}
	}

}
