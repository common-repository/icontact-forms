<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://icontact.com
 * @since      1.0.0
 *
 * @package    Icontact_Forms
 * @subpackage Icontact_Forms/includes
 */

namespace Icontact\Icontact_Forms;

use stdClass;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * @since      1.0.0
 * @package    Icontact_Forms
 * @subpackage Icontact_Forms/includes
 * @author     iContact <wordpress@icontact.com>
 */
class Icontact_Api_Wrapper extends iContactApi {

	/**
	 * Store the iContatApi singleton
	 *
	 * @since 1.0.0
	 * @access private
	 * @var iContactApi
	 */
	private $api;

	/**
	 * Store the API connection state
	 *
	 * @var boolean
	 */
	private bool $connected = false;

	/**
	 * Save iContact API client folder id.
	 *
	 * @var string
	 */
	private $client_folder_id;

	/**
	 * Save iContact API account id.
	 *
	 * @var string
	 */
	private $client_account_id;

	/**
	 * The array of available client folders.
	 *
	 * @var array
	 */
	private $client_folders;

	/**
	 * Know if the user was successfully "connected" to iContact API but now something went wrong and is not connecting.
	 *
	 * @var boolean
	 */
	private $connection_is_broken = false;

	/**
	 * Start settings the state as disconnected.
	 */
	public function __construct() {
		// Check if last sync to API was successful to consider us connected.
		$account_info = get_transient( ICONTACT_FORMS_OPTION_PREFIX . 'connected_folder' );
		if ( false !== $account_info ) {
			$this->connected = true;
			if ( isset( $account_info['cid'] ) ) {
				$this->client_account_id = $account_info['cid'];
			}
			if ( isset( $account_info['cfid'] ) ) {
				$this->client_folder_id = $account_info['cfid'];
			}
			if ( ! empty( $account_info['error'] ) ) {
				$this->connection_is_broken = true;
			}
		}
	}

	/**
	 * Connect to the iContact API
	 *
	 * @param string $appid    String of the appid.
	 * @param string $username String of the username or email.
	 * @param string $password String o the password.
	 * @return bool  Return True if connection was successful or False if connection if failed.
	 */
	public function connect( string $appid = '', string $username = '', string $password = '' ) {
		$appid    = empty( $appid ) ? get_option( ICONTACT_FORMS_OPTION_PREFIX . 'appid' ) : $appid;
		$username = empty( $username ) ? get_option( ICONTACT_FORMS_OPTION_PREFIX . 'username' ) : $username;
		$password = empty( $password ) ? get_option( ICONTACT_FORMS_OPTION_PREFIX . 'password' ) : $password;

		/**
		 * Build the api credentials array with the parameters.
		 */
		$api_credentials = array(
			'appId'       => $appid,
			'apiUsername' => $username,
			'apiPassword' => $password,
		);

		/**
		 * Check that there is no empty API credentials
		 */
		if ( array_search( '', $api_credentials, true ) !== false ) {
			return false;
		}
		if ( $this->api ) {
			$this->api->setConfig( $api_credentials );
		} else {
			// Give the API your information.
			iContactApi::getInstance()->setConfig( $api_credentials );

			// Store the singleton.
			$this->api = iContactApi::getInstance();

		}

		/**
		* If there is credentials info then we try to call the API and pull the user list to validate it works properly
		*/
		try {
			// Set default client id.
			$default_account_id = $this->api->setAccountId();

			// Get enabled client folders and set the first as default one.
			$this->client_folders = $this->get_client_folders();
			$default_folder_id    = $this->client_folders[0]->clientFolderId;

			$this->connected = true;

			$this->client_folder_id  = ( ! empty( $this->client_folder_id ) ? $this->api->setClientFolderId( $this->client_folder_id ) : $default_folder_id );
			$this->client_account_id = ( ! empty( $this->client_account_id ) ? $this->client_account_id : $default_account_id );
			set_transient(
				ICONTACT_FORMS_OPTION_PREFIX . 'connected_folder',
				array(
					'cid'  => $this->client_account_id,
					'cfid' => $this->client_folder_id,
				)
			);

			return true;
		} catch ( \Exception $o_exception ) {
			if ( $this->connected ) {
				$this->set_connection_broken();
			}
			return false;
		}
	}

	/**
	 * Set the API on broken state to show the user a warning about api credentials.
	 *
	 * @return void
	 */
	public function set_connection_broken() {
		// Save the error state on the connection transient.
		$account_info          = get_transient( ICONTACT_FORMS_OPTION_PREFIX . 'connected_folder' );
		$account_info['error'] = 1;
		set_transient( ICONTACT_FORMS_OPTION_PREFIX . 'connected_folder', $account_info );
		$this->connection_is_broken = true;
	}

	/**
	 * Delete connected transient info and set the api connected to false.
	 *
	 * @return void
	 */
	public function disconnect() {
		delete_transient( ICONTACT_FORMS_OPTION_PREFIX . 'connected_folder' );
		$this->connected = false;
	}

	/**
	 * Process the last error from the iContact API handle the case when no API credentials exists to avoid throwing a fatal error
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Raw error returned by the iContact API or null if there is no API connection.
	 */
	public function lastError() {
		if ( isset( $this->api ) ) {
			$errors = $this->api->getErrors();
			if ( false === $errors ) {
				return null;
			} else {
				$errors = $this->api->getErrors();
				return ( end( $errors ) );
			}
		} else {
			return null;
		}
	}

	/**
	 * Return true/false if connection is broken or not.
	 *
	 * @return bool
	 */
	public function connection_is_broken() {
		return $this->connection_is_broken;
	}

	/**
	 * Return JSON response of forms info from iContact API.
	 *
	 * @param integer $offset API offset to start for the forms array.
	 * @param integer $limit API limit per page of the forms array.
	 * @return json Returns JSON object from iContact API response.
	 */
	public function get_forms_info( int $offset = 0, int $limit = 20 ) {
		$api_response = array();

		if ( empty( $this->api ) ) {
			if ( false === $this->connect() ) {
				$this->set_connection_broken();
				$api_error_message    = $this->lastError();
				$api_response         = new stdClass();
				$api_response->errors = array( $api_error_message );

				$this->set_connection_broken();

				return $api_response;
			}
		}

		if ( $this->is_connected() ) {
			// Set the resource.
			$s_resource = (string) "/a/{$this->client_account_id}/c/{$this->client_folder_id}/signupforms";

			$this->api->setOffset( $offset );
			$this->api->setLimit( $limit );

			// Find the Signup forms.
			try {
				$a_forms = $this->api->makeCall( $s_resource, 'get' );
				if ( $a_forms ) {
					if ( empty( $a_forms ) ) {
						// Add an error, for there.
						// are no signup forms.
						return array();
					} else {
						return $a_forms;
					}
				}
			} catch ( \Exception $o_exception ) {
				$api_error_message    = 'Forbidden';
				$api_response         = new stdClass();
				$api_response->errors = array( $api_error_message );

				$this->set_connection_broken();

				return $api_response;
			}
		}
		return $api_response;
	}

	/**
	 * Return if API is connected or not.
	 *
	 * @return boolean True/False if is connected or not.
	 */
	public function is_connected() {
		return $this->connected;
	}

	/**
	 * Get the client folders array from the API.
	 *
	 * @return array Client folders array.
	 */
	public function get_client_folders() {
		if ( empty( $this->api ) ) {
			$this->connect();
		}

		if ( isset( $this->client_folders ) ) {
			return $this->client_folders;
		}

		// Check for an Account ID.
		if ( empty( $this->api->iAccountId ) ) {
			// Set the Account ID.
			$this->api->setAccountId();
		}
		// Set the resource.
		$s_resource = (string) "/a/{$this->api->iAccountId}/c/";
		// Find the Client Folders.
		$api_response = $this->api->makeCall( $s_resource, 'get' );
		if ( $api_response ) {
			if ( empty( $api_response ) ) {
				// Add an error, for there.
				// are no client folders.
				return array();
			} else {
				$all_folders = $api_response->clientfolders;
				$limit       = 20;
				$offset      = 0;
				while ( $api_response->total > ( $limit + $offset ) ) {
					$offset += $limit;
					$this->api->setOffset( $offset );
					$api_response = $this->api->makeCall( $s_resource, 'get' );
					$all_folders  = array( ...$all_folders, ...$api_response->clientfolders );
				}

				$enabled_folders = array();
				foreach ( $all_folders as $folder ) {
					if ( ! isset( $folder->enabled ) || (bool) $folder->enabled ) {
						array_push( $enabled_folders, $folder );
					}
				}

				usort(
					$enabled_folders,
					function( $a, $b ) {
						return strnatcasecmp( $a->name, $b->name );
					}
				);

				return $enabled_folders;
			}
		}
	}

	/**
	 * Set the on modal selected client folder id on the transient.
	 *
	 * @param string $client_folder_id The client folder id selected.
	 * @return void
	 */
	public function set_client_folder_id( string $client_folder_id ) {
		$account_info            = get_transient( ICONTACT_FORMS_OPTION_PREFIX . 'connected_folder' );
		$this->client_account_id = $account_info['cid'];
		$this->client_folder_id  = $client_folder_id;

		set_transient(
			ICONTACT_FORMS_OPTION_PREFIX . 'connected_folder',
			array(
				'cid'  => $this->client_account_id,
				'cfid' => $this->client_folder_id,
			)
		);
	}

}
