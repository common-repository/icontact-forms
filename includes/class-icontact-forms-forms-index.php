<?php
/**
 * The forms index functionality of the plugin.
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
 * The forms index functionality of the plugin.
 *
 * Defines the plugin name, version, and update form index and output
 *
 * @package    Icontact_Forms
 * @subpackage Icontact_Forms/admin
 * @author     iContact <wordpress@icontact.com>
 */
class Icontact_Forms_Forms_Index {
	/**
	 * Save iContact API client folder id.
	 *
	 * @var string
	 */
	private $client_folder_id;

	/**
	 * Save iContact API client account id.
	 *
	 * @var string
	 */
	private $client_account_id;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
	}

	/**
	 * Return the array with the last forms information saved from the iContact Api
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_forms_index() {

		$forms_index = get_option( ICONTACT_FORMS_OPTION_PREFIX . 'forms_index' );
		if ( is_array( $forms_index ) === false ) {
			$forms_index = array();
		}
		return $forms_index;
	}

	/**
	 * Function to handle ajax request of the form index.
	 *
	 * @return void
	 */
	public function form_index_handler() {
		if ( isset( $_GET['_wpnonce'] ) ) {
			$wp_nonce = sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) );
		}

		if ( wp_verify_nonce( $wp_nonce ) ) {
			$this->update_forms_index( new Icontact_Api_Wrapper() );

			wp_send_json_success( $this->get_forms_index(), 200 );

			wp_die();
		} else {
			echo 'Wrong WordPress nonce';
			wp_die();
		}
	}

	/**
	 * Updates the forms index with all the forms information
	 *
	 * @param Icontact_Api_Wrapper $api_instance The current api wrapper instance that was used to connect to iContact api.
	 * @return void
	 */
	public function update_forms_index( $api_instance ) {
		if ( $api_instance->is_connected() ) {
			$api_response = $api_instance->get_forms_info();
		}

		if ( empty( $api_response->errors ) ) {
			$forms_info = $api_response->forms;
			while ( $api_response->total > ( $api_response->limit + $api_response->offset ) ) {
				$api_response = $api_instance->get_forms_info( ( $api_response->limit + $api_response->offset ) );
				$forms_info   = array( ...$forms_info, ...$api_response->forms );
			}

			$key_whitelist           = array( 'formId', 'name', 'lists', 'updated' );
			$account_info            = get_transient( ICONTACT_FORMS_OPTION_PREFIX . 'connected_folder' );
			$this->client_account_id = $account_info['cid'];
			$this->client_folder_id  = $account_info['cfid'];

			$forms_index = array();

			$i = 0;
			// If there is only 1 element transform the object to an array so the foreach still works.
			if ( false === is_array( $forms_info ) ) {
				$forms_info = array( $forms_info );
			}
			foreach ( $forms_info as $form ) {
				$form = (array) $form;
				if ( empty( $form ) ) {
					continue;
				}
				array_push( $forms_index, array_change_key_case( array_intersect_key( $form, array_flip( $key_whitelist ) ), CASE_LOWER ) );
				$forms_index[ $i ]['cfid'] = $this->client_folder_id;
				$forms_index[ $i ]['cid']  = $this->client_account_id;
				$i++;
			}

			update_option( ICONTACT_FORMS_OPTION_PREFIX . 'forms_index', $forms_index );

			$this->update_forms_output( $forms_info );
		}
	}

	/**
	 * Update on Wodpress database using options the information of the Form HTML output code for each Form ID
	 *
	 * @since 1.0.0
	 * @param array $forms_info API Response with forms info.
	 * @access public
	 * @return void
	 */
	private function update_forms_output( $forms_info ) {
		$account_info            = get_transient( ICONTACT_FORMS_OPTION_PREFIX . 'connected_folder' );
		$this->client_account_id = $account_info['cid'];
		$this->client_folder_id  = $account_info['cfid'];

		foreach ( $forms_info as $form ) {
			$form           = (array) $form;
			$form           = array_change_key_case( $form, CASE_LOWER );
			$form_option_id = ICONTACT_FORMS_OPTION_PREFIX . 'foutput_fid_' . $form['formid'] . '_cfid_' . $this->client_folder_id . '_cid_' . $this->client_account_id;
			update_option( $form_option_id, str_replace( array( "\r", "\n" ), '', $form['automatichtml'] ) );
		}
	}

	/**
	 * Return the HTML form output for request form id
	 *
	 * @param array $params Array with at least formid and cfid params to get the output.
	 * @return string HTML output of the requested form or null if nothing was found.
	 */
	public function get_form_output( array $params ) {
		$is_in_editor = ( isset( $params['preview'] ) && 1 === $params['preview'] ) ? true : false;
		// We only check for this either on live published post or on a preview, because on the editor index.js react logic take care of this.
		if ( false === $is_in_editor && false === $this->check_form_is_unique() ) {
			// If we are not on a live published post then we are on a preview so we will show this visible.
			if ( is_preview() ) {
				return 'You can only have one signup form per page.';
			} else {
				// If we are doing this on a published post live on prod we would hide the error on none display divs to not cause problems with the layout.
				return '<div style="display:none">You can only have one signup form per page.</div>';
			}
		}

		$form_output = false;
		if ( isset( $params['formid'] ) && isset( $params['cfid'] ) && isset( $params['cid'] ) ) {
			$form_output = get_option( ICONTACT_FORMS_OPTION_PREFIX . 'foutput_fid_' . $params['formid'] . '_cfid_' . $params['cfid'] . '_cid_' . $params['cid'] );
		}
		if ( false === $form_output ) {
			return null;
		} else {
			return "<style>#ic_signupform .elcontainer .formEl.fieldtype-checkbox .option-container input { position: relative !important; }</style><div>$form_output</div>";
		}
	}

	/**
	 * Function to handle ajax request of the form output.
	 *
	 * @return void
	 */
	public function form_output_handler() {
		if ( isset( $_GET['_wpnonce'] ) ) {
			$wp_nonce = sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) );
		}

		if ( wp_verify_nonce( $wp_nonce ) ) {
			$params['formid'] = isset( $_GET['formid'] ) ? sanitize_text_field( wp_unslash( $_GET['formid'] ) ) : '';
			$params['cfid']   = isset( $_GET['cfid'] ) ? sanitize_text_field( wp_unslash( $_GET['cfid'] ) ) : '';
			$params['cid']    = isset( $_GET['cid'] ) ? sanitize_text_field( wp_unslash( $_GET['cid'] ) ) : '';

			$allowed_tags = array(
				'div'    => array(
					'id' => array(),
				),
				'style'  => array(),
				'script' => array(
					'src' => array(),
				),
			);

			wp_die( wp_kses( $this->get_form_output( $params ), $allowed_tags ) );
		} else {
			echo 'Wrong WordPress nonce';
			wp_die();
		}
	}

	/**
	 * Check if the current post have more than one iContact signup form on page.
	 *
	 * @return bool True if form is unique or false if there is more than 1 form on post.
	 */
	private function check_form_is_unique() {
		$post_info = get_post();

		if ( is_null( $post_info ) ) {
			// We only check this if we are inside a post.
			return true;
		} else {
			// If we inside a post we only check this on the preview or in published post.
			$post_blocks = parse_blocks( $post_info->post_content );
		}

		$blocks_found      = 0;
		$shortcodes_blocks = 0;
		foreach ( $post_blocks as $block ) {
			if ( 'icontact-forms/icontact-forms-block' === $block['blockName'] ) {
				$blocks_found++;
			}
			if ( 'core/shortcode' === $block['blockName'] ) {
				if ( strstr( $block['innerHTML'], '[icform ' ) ) {
					$shortcodes_blocks++;
				}
			}
		}

		// The user can write the shortcode instead of copy/paste in that cases is not detected by WordPress blocks on post so we need to count the string on post content.
		$shortcode_strings = substr_count( $post_info->post_content, '[icform ' );

		// To make sure we don't double count the shortcode blocks detected by WordPress and the shortcode strings we take the higher one.
		$blocks_found += ( $shortcode_strings > $shortcodes_blocks ) ? $shortcode_strings : $shortcodes_blocks;

		if ( $blocks_found > 1 ) {
			return false;
		} else {
			return true;
		}
	}
}
