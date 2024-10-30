<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://icontact.com
 * @since      1.0.0
 *
 * @package    Icontact_Forms
 * @subpackage Icontact_Forms/public
 */

namespace Icontact\Icontact_Forms;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The public-facing functionality of the plugin.
 *
 * Enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Icontact_Forms
 * @subpackage Icontact_Forms/public
 * @author     iContact <wordpress@icontact.com>
 */
class Icontact_Forms_Public {
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Icontact_Forms_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Icontact_Forms_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( ICONTACT_FORMS_PLUGIN_NAME, ICONTACT_FORMS__PLUGIN_URL . 'public/css/icontact-forms-public.css', array(), ICONTACT_FORMS_VERSION, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Icontact_Forms_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Icontact_Forms_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( ICONTACT_FORMS_PLUGIN_NAME, ICONTACT_FORMS__PLUGIN_URL . 'js/icontact-forms-public.js', array( 'jquery' ), ICONTACT_FORMS_VERSION, false );

	}

}
