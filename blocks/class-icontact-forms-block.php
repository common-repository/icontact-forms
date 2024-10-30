<?php
/**
 * The block-specific functionality of the plugin.
 *
 * @link       https://icontact.com
 * @since      1.0.0
 *
 * @package    Icontact_Forms
 * @subpackage Icontact_Forms/blocks
 */

namespace Icontact\Icontact_Forms;

use simple_html_dom;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
/**
 * The block-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and init the block scripts
 *
 * @package    Icontact_Forms
 * @subpackage Icontact_Forms/blocks
 * @author     iContact <wordpress@icontact.com>
 */
class Icontact_Forms_Block {
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
	}

	/**
	 * Registers all block assets so that they can be enqueued through Gutenberg in.
	 * the corresponding context.
	 *
	 * @see https://wordpress.org/gutenberg/handbook/designers-developers/developers/tutorials/block-tutorial/applying-styles-with-stylesheets/
	 */
	public function block_init() {
		// Skip block registration if Gutenberg is not enabled/merged.
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}
		$dir = dirname( __FILE__ );

		$index_js = 'icontact-forms-block/index.js';
		wp_register_script(
			ICONTACT_FORMS_PLUGIN_NAME . '-block-block-editor',
			plugins_url( $index_js, __FILE__ ),
			array(
				'wp-blocks',
				'wp-i18n',
				'wp-element',
				'wp-block-editor',
				'wp-components',
			),
			filemtime( "{$dir}/{$index_js}" ),
			false
		);

		$editor_css = 'icontact-forms-block/editor.css';
		wp_register_style(
			ICONTACT_FORMS_PLUGIN_NAME . '-block-block-editor',
			plugins_url( $editor_css, __FILE__ ),
			array(),
			filemtime( "{$dir}/{$editor_css}" )
		);

		$style_css = 'icontact-forms-block/style.css';
		wp_register_style(
			ICONTACT_FORMS_PLUGIN_NAME . '-block-block',
			plugins_url( $style_css, __FILE__ ),
			array(),
			filemtime( "{$dir}/{$style_css}" )
		);

		// Get the array of existings forms from the forms index.
		$existing_forms = apply_filters( ICONTACT_FORMS_OPTION_PREFIX . 'get_form_index', array() );

		wp_localize_script(
			ICONTACT_FORMS_PLUGIN_NAME . '-block-block-editor',
			'icontact_forms_block_block_editor',
			array(
				'logo_url' => ICONTACT_FORMS__PLUGIN_URL . '/admin/images/icontact-pc-logo-top-menu.svg',
				'forms'    => $existing_forms,
				'prefix'   => ICONTACT_FORMS_OPTION_PREFIX,
				'_wpnonce' => wp_create_nonce(),
			)
		);

		/**
		 * Register block on WordPress with the default form list and render callback to the shortcode plugin.
		 */
		register_block_type(
			__DIR__ . '/icontact-forms-block/block.json',
			array(
				'api_version'     => 2,
				'editor_script'   => ICONTACT_FORMS_PLUGIN_NAME . '-block-block-editor',
				'textdomain'      => ICONTACT_FORMS_PLUGIN_NAME,
				'render_callback' => array( $this, 'render_block' ),
				'attributes'      => array(
					'formid'  => array(
						'type'    => 'string',
						'default' => '',
					),
					'cfid'    => array(
						'type'    => 'string',
						'default' => '',
					),
					'cid'     => array(
						'type'    => 'string',
						'default' => '',
					),
					'preview' => array(
						'type'    => 'bool',
						'default' => 0,
					),
				),
			)
		);
	}

	/**
	 * Render the form block
	 *
	 * @param array  $atts WordPress array of attributes from the block or element.
	 * @param string $content Post content.
	 * @return string  Form html output.
	 */
	public function render_block( $atts, $content = '' ) {
		$is_preview = ! empty( $atts['preview'] ) ? true : false;

		$form_output = apply_filters( ICONTACT_FORMS_OPTION_PREFIX . 'get_form_output', $atts );

		if ( is_null( $form_output ) ) {
			if ( $is_preview || is_preview() ) {
				return 'Form not found.';
			} else {
				return '<div style="display:none">Form not found.</div>';
			}
		}
		return stripslashes( html_entity_decode( $form_output ) );
	}
}
