<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://icontact.com
 * @since             1.0.0
 * @package           Icontact_Forms
 *
 * @wordpress-plugin
 * Plugin Name:       iContact Lead Forms
 * Plugin URI:        https://www.icontact.com/wordpress
 * Description:       The official iContact plugin to add iContact forms to your WordPress site and grow your mailing lists.

 * Version:           1.1.1
 * Author:            iContact
 * Author URI:        https://www.icontact.com/
 * License:           GPL-2.0+
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       icontact-forms
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Autoload plugin file to manage all classes and namespace.
require 'autoload.php';

// Create a new instance of the autoloader.
$loader = new Icontact_Forms_Loader_Class();

// Register this instance.
$loader->register();

// Plugin Folder Path.
if ( ! defined( 'ICONTACT_FORMS__PLUGIN_DIR' ) ) {
	define( 'ICONTACT_FORMS__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

/*
*  Add our namespace and the folder it maps to
*  All this folders will be included on the namespace class loader
*/
$class_folders = array( 'includes', 'admin', 'public', 'shortcodes', 'blocks' );
foreach ( $class_folders as $folder ) {
	$loader->addNamespace( 'Icontact\Icontact_Forms', ICONTACT_FORMS__PLUGIN_DIR . $folder );
}


/**
* Currently plugin version.
* Plugin constants for menu, slug, page title and options prefix
*/
define( 'ICONTACT_FORMS_VERSION', '1.0.0' );
define( 'ICONTACT_FORMS_PLUGIN_NAME', 'icontact-forms' );
define( 'ICONTACT_FORMS_MENU_SLUG', 'icontact-forms' );
define( 'ICONTACT_FORMS_MENU_PAGE_TITLE', 'iContact Forms Settings' );
define( 'ICONTACT_FORMS_MENU_TITLE', 'iContact Forms' );
define( 'ICONTACT_FORMS_OPTION_PREFIX', 'icontact_forms_' );
define( 'ICONTACT_APIURL', get_option( ICONTACT_FORMS_OPTION_PREFIX . 'apiurl', 'https://app.icontact.com/icp' ) );

// Plugin Folder URL.
if ( ! defined( 'ICONTACT_FORMS__PLUGIN_URL' ) ) {
	define( 'ICONTACT_FORMS__PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

// Plugin Root File.
if ( ! defined( 'ICONTACT_FORMS__PLUGIN_FILE' ) ) {
	define( 'ICONTACT_FORMS__PLUGIN_FILE', __FILE__ );
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-icontact-forms-activator.php
 */
function activate_icontact_forms() {
	\Icontact\Icontact_Forms\Icontact_Forms_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-icontact-forms-deactivator.php
 */
function deactivate_icontact_forms() {
	\Icontact\Icontact_Forms\Icontact_Forms_Deactivator::deactivate();
}

register_activation_hook( __FILE__, __NAMESPACE__ . '\activate_icontact_forms' );
register_deactivation_hook( __FILE__, __NAMESPACE__ . '\deactivate_icontact_forms' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_icontact_forms() {

	$plugin = new \Icontact\Icontact_Forms\Icontact_Forms();
	$plugin->run();

}
run_icontact_forms();
