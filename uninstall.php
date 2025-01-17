<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 *
 * For more information, see the following discussion:
 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/pull/123#issuecomment-28541913
 *
 * @link       https://icontact.com
 * @since      1.0.0
 *
 * @package    Icontact_Forms
 */

namespace Icontact\Icontact_Forms;

require_once 'icontact-forms.php';

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Delete all icontact information on database when uninstalled.

delete_option( ICONTACT_FORMS_OPTION_PREFIX . 'appid' );
delete_option( ICONTACT_FORMS_OPTION_PREFIX . 'username' );
delete_option( ICONTACT_FORMS_OPTION_PREFIX . 'password' );
delete_option( ICONTACT_FORMS_OPTION_PREFIX . 'forms_index' );
delete_transient( ICONTACT_FORMS_OPTION_PREFIX . 'connected_folder' );

foreach ( wp_load_alloptions() as $option => $value ) {
	if ( strpos( $option, ICONTACT_FORMS_OPTION_PREFIX ) === 0 ) {
		delete_option( $option );
	}
}
