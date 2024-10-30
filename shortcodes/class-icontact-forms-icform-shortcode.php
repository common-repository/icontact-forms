<?php
/**
 * The shortcode-specific functionality of the plugin.
 *
 * @link       https://icontact.com
 * @since      1.0.0
 *
 * @package    Icontact_Forms
 * @subpackage Icontact_Forms/shortcodes
 */

namespace Icontact\Icontact_Forms;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The shortcode-specific functionality of the plugin.
 *
 * Init shortcode on WordPress
 *
 * @package    Icontact_Forms
 * @subpackage Icontact_Forms/shortcodes
 * @author     iContact <wordpress@icontact.com>
 */
class Icontact_Forms_Icform_Shortcode {
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
	}

	/**
	 * Shortcode hook init function
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function shortcode_init() {
		$this->register_shortcode();
	}

	/**
	 * Register [icform] shortcode on WordPress
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function register_shortcode() {
		add_shortcode( 'icform', array( $this, 'shortcode_output' ) );
	}

	/**
	 * Shortcode wrapper for the outputting a form.
	 *
	 * @since 1.0.0
	 * @param array  $atts Shortcode attributes provided by a user.
	 * @param string $content The HTML content inside shortcode [icform][/icform].
	 * @return string
	 */
	public function shortcode_output( $atts, $content = '' ) {
		$shortcode_output = apply_filters( ICONTACT_FORMS_OPTION_PREFIX . 'get_form_output', $atts );

		if ( is_null( $shortcode_output ) ) {
			if ( is_preview() ) {
				return '[Form not found]';
			} else {
				return '<div style="display:none">[Form not found]</div>';
			}
		}
		$shortcode_output = stripslashes( html_entity_decode( $shortcode_output ) );
		if ( ! empty( $content ) ) {
			$shortcode_output = '<p>' . $content . '</p>' . $shortcode_output;
		}
		return $shortcode_output;
	}
}
