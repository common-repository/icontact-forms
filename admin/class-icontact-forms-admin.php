<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://icontact.com
 * @since      1.0.0
 *
 * @package    Icontact_Forms
 * @subpackage Icontact_Forms/admin
 */

namespace Icontact\Icontact_Forms;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The admin-specific functionality of the plugin
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Icontact_Forms
 * @subpackage Icontact_Forms/admin
 * @author     iContact <wordpress@icontact.com>
 */
class Icontact_Forms_Admin {
	/**
	 * The iContactApi instance.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var object iContact_Api_Wrapper class instance
	 */
	private $api;

	/**
	 * Array of the listed view partials to render the admin pages
	 *
	 * @since 1.0.0
	 * @access private
	 * @var array
	 */
	private $views = array();

	/**
	 * Array of the available submenus for admin pages.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var array
	 */
	private $submenus = array();

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		$this->api = new Icontact_Api_Wrapper();
	}

	/**
	 * Add view name to the list of available views to render
	 *
	 * @since 1.0.0
	 * @param string $view_name The name of the view that will be rendered.
	 * @param string $alias A short name to identify the view.
	 * @return string Name of the view added.
	 */
	public function add_view( string $view_name, string $alias = '' ) {
		if ( ! in_array( $view_name, $this->views, true ) ) {
			$this->views[ $view_name ] = $alias;
			return $view_name;
		}
		return '';
	}

	/**
	 * Add submenu names to the list of available submenus and build submenu id for WordPress.
	 * also register the view name of the submenu added
	 *
	 * @since 1.0.0
	 * @param string $submenu_name The name of the submenu.
	 * @return void
	 */
	public function add_submenu( string $submenu_name ) {
		$submenu_id = ICONTACT_FORMS_MENU_SLUG . '-' . strtolower( $submenu_name );

		if ( ! in_array( $submenu_id, $this->submenus, true ) ) {
			$this->submenus[ $submenu_id ] = array(
				'name'      => $submenu_name,
				'view_name' => $this->add_view(
					add_submenu_page(
						ICONTACT_FORMS_MENU_SLUG,
						$submenu_name,
						$submenu_name,
						'manage_options',
						$submenu_id,
						array(
							$this,
							'render_admin_view',
						)
					),
					strtolower( $submenu_name ),
				),
			);
		}
	}

	/**
	 * Create the sidebar menu and add general WordPress settings option.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return void
	 */
	public function menus() {
		// phpcs:disable
		// File_get_contents used to read a local file.
		$svg = file_get_contents( ICONTACT_FORMS__PLUGIN_DIR . '/admin/images/icontact-icon-blue.svg' );
		// phpcs:enable

		/**
		 *  Add sidebar top main "iContact Forms" menu.
		 *  also add this menu page to views list.
		 */
		$this->add_view(
			add_menu_page(
				ICONTACT_FORMS_MENU_PAGE_TITLE,
				ICONTACT_FORMS_MENU_TITLE,
				'manage_options',
				ICONTACT_FORMS_MENU_SLUG,
				array(
					$this,
					'render_admin_view',
				),
				// phpcs:disable
				// Base64_encode used as requested by WordPress Docs https://developer.wordpress.org/reference/functions/add_menu_page/.
				'data:image/svg+xml;base64,' . base64_encode( $svg ), 
				// phpcs:enable
			),
			'main-menu',
		);

		/**
		 *  Add Forms submenu to main menu.
		 */
		$this->add_submenu( 'Forms' );

		/**
		 *  Add Settings submenu to main menu.
		 */
		$this->add_submenu( 'Settings' );

		/**
		 *  Add About submenu to main menu.
		 */
		$this->add_submenu( 'About' );

		/**
		 * WordPress duplicates parent menu as a submenu when adding submenus, so the not pretty solution it's to remove parent menu from the submenu.
		 */
		remove_submenu_page( ICONTACT_FORMS_MENU_SLUG, ICONTACT_FORMS_MENU_SLUG );

		/**
		 * Add submenu iContact settings to WordPress general settings page.
		 */
		add_options_page(
			'iConctact Forms Settings Options',
			'iContact Forms',
			'manage_options',
			ICONTACT_FORMS_MENU_SLUG,
			array(
				$this,
				'render_admin_view',
			),
		);
	}

	/**
	 * Register the credential iContact API options for the plugin and turn on the activation banner.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return void
	 */
	public function register_admin_settings() {
		register_setting( 'icontact-forms-group', ICONTACT_FORMS_OPTION_PREFIX . 'appid' );
		register_setting( 'icontact-forms-group', ICONTACT_FORMS_OPTION_PREFIX . 'username' );
		register_setting( 'icontact-forms-group', ICONTACT_FORMS_OPTION_PREFIX . 'password' );
	}

	/**
	 * Return the active page view name based on our on views list names.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string The active view name based on our own views list.
	 */
	public function get_active_view_name() {
		$active_page_id = get_current_screen()->id;
		$view_name      = ! empty( $this->views[ $active_page_id ] ) ? $this->views[ $active_page_id ] : $active_page_id;
		return $view_name;
	}

	/**
	 * Call the activation_banner view if API is not connected.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return void
	 */
	public function admin_activation_notice() {
		if ( $this->api->is_connected() === false ) {
			// Don't show activation banner in settings page.
			if ( ! in_array( $this->get_active_view_name(), array( 'settings', 'main-menu' ), true ) ) {
				load_template( ICONTACT_FORMS__PLUGIN_DIR . 'admin/partials/icontact-forms-admin-activation-banner.php' );
			}
		} else {
			if ( $this->api->connection_is_broken() ) {
				$this->show_notice( 'It looks like your connection has failed. You need to re-authenticate with iContact on the Settings page', 'error' );
			}
		}
	}

	/**
	 * Show WordPress notices messages on admin interface
	 *
	 * @param string   $message Message to be shown on the notice.
	 * @param string   $type Could be: error, warning, success or info.
	 * @param boolean  $is_dismissible Allow to dismiss the notice or not.
	 * @param callable $callback Wanted callback function after show the notice.
	 * @return callable In case $callback exists, return the callback function.
	 */
	public function show_notice( string $message, string $type = 'info', bool $is_dismissible = true, callable $callback = null ) {
		$class = 'notice notice-' . $type;
		if ( $is_dismissible ) {
			$class .= ' is-dismissible';
		}
		printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
		if ( is_callable( $callback ) ) {
			return call_user_func( $callback );
		}
	}

	/**
	 * Add settings link to plugin actions
	 *
	 * @param  array  $plugin_actions WordPress plugin actions array.
	 * @param  string $plugin_file WordPress plugin file path string.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Return $plugin_actions merged array with new cl_settings $new_actions array.
	 */
	public function add_plugin_link( $plugin_actions, $plugin_file ) {
		$new_actions = array();
		if ( basename( ICONTACT_FORMS__PLUGIN_DIR ) . '/icontact-forms.php' === $plugin_file ) {
			$new_actions['cl_settings'] = sprintf( '<a href="%s">Settings</a>', esc_url( admin_url( 'options-general.php?page=' . ICONTACT_FORMS_MENU_SLUG ) ) );
		}
		return array_merge( $new_actions, $plugin_actions );
	}

	/**
	 * Render the admin top menu for all the plugin admin pages
	 * need to be here because the top menu is on a special place on wp html to be on top
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function render_admin_top_menu() {
		$this->render_admin_view( 'top-menu' );
	}

	/**
	 * Receive the iContact API credentials and saved on WordPress options to be stored on DB, then test the connection with those credentials
	 *
	 * @param string $appid The iContact Api App ID.
	 * @param string $username The iContact Api username or email.
	 * @param string $password The iContact Api password.
	 * @return void
	 */
	public function save_settings( $appid, $username, $password ) {
		$appid    = sanitize_text_field( $appid );
		$username = sanitize_text_field( $username );
		$password = sanitize_text_field( $password );

		update_option( ICONTACT_FORMS_OPTION_PREFIX . 'appid', $appid );
		update_option( ICONTACT_FORMS_OPTION_PREFIX . 'username', $username );
		update_option( ICONTACT_FORMS_OPTION_PREFIX . 'password', $password );
	}

	/**
	 * Process the admin POST request from settings and top menu forms to handle API connection and credentials
	 *
	 * @return void
	 */
	public function disconnect_button_handler() {
		if ( isset( $_POST[ ICONTACT_FORMS_OPTION_PREFIX . 'disconnect_button_nonce' ] ) ) {
			$disconnect_button_nonce = sanitize_text_field( wp_unslash( $_POST[ ICONTACT_FORMS_OPTION_PREFIX . 'disconnect_button_nonce' ] ) );
		}

		if ( wp_verify_nonce( $disconnect_button_nonce, ICONTACT_FORMS_OPTION_PREFIX . 'disconnect_button_nonce' ) ) {

			// We reset the api credentials to blank so the user get "disconnected" from the iContact Api.
			$this->save_settings( '', '', '' );

			delete_option( ICONTACT_FORMS_OPTION_PREFIX . 'client_folders_info' );

			$this->api->disconnect();

			wp_send_json_success( 'Disconnected', 200 );

			wp_die();

		} else {

			wp_die(
				'Invalid nonce specified',
				'Error',
				array(
					'response'  => 403,
					'back_link' => 'admin.php?page=' . esc_attr( ICONTACT_FORMS_PLUGIN_NAME ),
				)
			);
		}
	}

	/**
	 * Callback function for the menu to decide what view to how to user Forms | Settings | About and add the Top Menu part
	 *
	 * @since 1.0.0
	 * @param string $view_name The name of the view to render inside admin settings.
	 * @return void
	 */
	public function render_admin_view( string $view_name ) {
		// We get the active WP page id of current page.
		$active_page_id = get_current_screen()->id;
		if ( isset( $this->views[ $active_page_id ] ) ) {
			if ( empty( $view_name ) ) {
				$view_name = $this->get_active_view_name();
			}
		} else {
			// If it's not part of the plugin return nothing.
			return;
		}

		if ( isset( $_POST['_wpnonce'] ) ) {
			$wpnonce = sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) );
		}
		/**
		 * We process all the $_POST data only on top-menu to not process twice the same $_POST action
		 * since top-menu always is the first view called since is a special one.
		 */
		if ( ! empty( $wpnonce ) && wp_verify_nonce( $wpnonce ) && 'top-menu' === $view_name ) {
			if ( isset( $_POST['action'] ) ) {
				$action = sanitize_text_field( wp_unslash( $_POST['action'] ) );
			}
			switch ( $action ) {
				case 'save_credentials':
					$appid    = ( isset( $_POST[ ICONTACT_FORMS_OPTION_PREFIX . 'appid' ] ) ) ? sanitize_text_field( wp_unslash( $_POST[ ICONTACT_FORMS_OPTION_PREFIX . 'appid' ] ) ) : '';
					$username = ( isset( $_POST[ ICONTACT_FORMS_OPTION_PREFIX . 'username' ] ) ) ? sanitize_text_field( wp_unslash( $_POST[ ICONTACT_FORMS_OPTION_PREFIX . 'username' ] ) ) : '';
					$password = ( isset( $_POST[ ICONTACT_FORMS_OPTION_PREFIX . 'password' ] ) ) ? sanitize_text_field( wp_unslash( $_POST[ ICONTACT_FORMS_OPTION_PREFIX . 'password' ] ) ) : '';
					$this->save_settings( $appid, $username, $password );
					$this->api->connect( $appid, $username, $password );
					if ( $this->api->is_connected() ) {
						$client_folders = $this->api->get_client_folders();
						if ( count( $client_folders ) > 1 ) {
							update_option( ICONTACT_FORMS_OPTION_PREFIX . 'client_folders_info', wp_json_encode( $client_folders ) );
						} else {
							do_action( ICONTACT_FORMS_OPTION_PREFIX . 'update_form_index', $this->api );
							set_transient( ICONTACT_FORMS_OPTION_PREFIX . 'succesfully_connected', 3600 );
						}
					}
					break;
				case 'save_folder':
					$folder_id = ( isset( $_POST['folder'] ) ) ? sanitize_text_field( wp_unslash( $_POST['folder'] ) ) : '';
					$this->api->set_client_folder_id( $folder_id );

					$folder_info = get_option( ICONTACT_FORMS_OPTION_PREFIX . 'client_folders_info' );
					$folder_info = json_decode( $folder_info );

					$selected_folder = array();
					foreach ( $folder_info as $folder ) {
						$folder = (array) $folder;
						if ( (string) $folder['clientFolderId'] === $folder_id ) {
							array_push( $selected_folder, $folder );
						}
					}

					update_option( ICONTACT_FORMS_OPTION_PREFIX . 'client_folders_info', wp_json_encode( $selected_folder ) );
					do_action( ICONTACT_FORMS_OPTION_PREFIX . 'update_form_index', $this->api );
					set_transient( ICONTACT_FORMS_OPTION_PREFIX . 'succesfully_connected', 3600 );
					break;
			}
		}

		/**
		 * Select which view are we going to show based on user admin screen.
		 */
		switch ( $view_name ) {
			case 'top-menu':
				$template_variables['submenus']      = $this->submenus;
				$template_variables['active_page']   = $active_page_id;
				$template_variables['connected']     = $this->api->is_connected();
				$template_variables['wpnonce']       = wp_create_nonce( ICONTACT_FORMS_OPTION_PREFIX . 'disconnect_button_nonce' );
				$template_variables['option_prefix'] = ICONTACT_FORMS_OPTION_PREFIX;

				// Include top menu on all views for admin pages.
				load_template( ICONTACT_FORMS__PLUGIN_DIR . 'admin/partials/icontact-forms-admin-top-menu.php', true, $template_variables );
				break;
			case 'forms':
				/**
				 * Check if user has access to this page
				 */
				if ( ! current_user_can( 'manage_options' ) ) {
					wp_die( 'You don\'t have access to this page' );
				}

				$template_variables['appid']    = get_option( ICONTACT_FORMS_OPTION_PREFIX . 'appid' );
				$template_variables['username'] = get_option( ICONTACT_FORMS_OPTION_PREFIX . 'username' );
				$template_variables['password'] = get_option( ICONTACT_FORMS_OPTION_PREFIX . 'password' );

				if ( $this->api->is_connected() === false ) {
					$this->show_notice( 'Connect your iContact account to see your forms and use them on your WordPress site.', 'error' );
				} else {
					if ( empty( $_GET['paged'] ) && empty( $_POST['s'] ) ) {
						do_action( ICONTACT_FORMS_OPTION_PREFIX . 'update_form_index', $this->api );
					}
				}
				echo '<div class="wrap icontact-forms-forms-table">';

				$forms_list_table = new Icontact_Forms_Forms_Table( $this->api->is_connected() );

				$forms_list_table->prepare_items();
				add_thickbox();
				$search_nonce = wp_create_nonce( 'search_nonce' );
				?>
				<form id="forms-table" method="post" class="icontact-forms">
					<input type="hidden" name='search_nonce' value='<?php print esc_attr( $search_nonce ); ?>'>
					<div id="icform-table-toolbox">
						<div class="inline" title="Create new Form" onClick="window.open( '<?php echo esc_url( ICONTACT_APIURL ); ?>/core/mycontacts/signup/designer', '_blank')">
							<p class="submit">
								<span class="button">
									<span class="dashicons dashicons-external"></span>
									Create New
								</span>
							</p>
						</div>
						<div class="inline" title="Refresh iContact forms lists" onClick="window.location.href=window.location.href">
							<p class="submit">
								<span class="button">
								<span class="dashicons dashicons-image-rotate"></span>
								Sync
								</span>
							</p>
						</div>
						<div class="inline searchbox" title="Search box" >
						<?php
						$forms_list_table->search_box( 'search', 'search_id' );
						?>
						</div>
				</div>
				<?php
				$forms_list_table->display();
				?>
				</form></div>
				<?php if ( ! empty( $_POST['s'] ) ) { ?>
				<script>
					var icform_forms_ajax_action = '<?php print esc_attr( ICONTACT_FORMS_OPTION_PREFIX . 'table_ajax' ); ?>';
					var icform_search_nonce = '<?php print esc_attr( $search_nonce ); ?>'
				</script>
				<?php } ?>
				<?php
				break;
			case 'about':
				load_template( ICONTACT_FORMS__PLUGIN_DIR . 'admin/partials/icontact-forms-admin-about.php' );
				break;
			default:
			case 'settings':
			case 'main-menu':
				/**
				 * Check if user has access to this page
				 */
				if ( ! current_user_can( 'manage_options' ) ) {
					wp_die( 'You don\'t have access to this page' );
				}

				$template_variables['appid']         = get_option( ICONTACT_FORMS_OPTION_PREFIX . 'appid' );
				$template_variables['username']      = get_option( ICONTACT_FORMS_OPTION_PREFIX . 'username' );
				$template_variables['password']      = get_option( ICONTACT_FORMS_OPTION_PREFIX . 'password' );
				$template_variables['wpnonce']       = wp_create_nonce();
				$template_variables['option_prefix'] = ICONTACT_FORMS_OPTION_PREFIX;

				if ( $this->api->is_connected() ) {
					$client_folder_info = get_option( ICONTACT_FORMS_OPTION_PREFIX . 'client_folders_info' );
					if ( $client_folder_info ) {
						$client_folder_info = json_decode( $client_folder_info );
						if ( count( $client_folder_info ) > 1 ) {
							add_thickbox();
							$template_variables['client_folders'] = (array) $client_folder_info;
							load_template( ICONTACT_FORMS__PLUGIN_DIR . 'admin/partials/icontact-forms-client-folders-modal.php', true, $template_variables );
						} else {
							$client_folder_info                       = $client_folder_info[0];
							$template_variables['client_folder_name'] = $client_folder_info->name;
						}
					}

					if ( get_transient( ICONTACT_FORMS_OPTION_PREFIX . 'succesfully_connected' ) ) {
						$this->show_notice( 'Successfully connected', 'success' );
						delete_transient( ICONTACT_FORMS_OPTION_PREFIX . 'succesfully_connected' );
					}
					load_template( ICONTACT_FORMS__PLUGIN_DIR . 'admin/partials/icontact-forms-admin-settings-connected.php', true, $template_variables );

				} else {
					$api_error_message = $this->api->lastError();

					if ( ! empty( $api_error_message ) ) {
						$this->show_notice( $api_error_message, 'error' );
					}

					load_template( ICONTACT_FORMS__PLUGIN_DIR . 'admin/partials/icontact-forms-admin-settings-disconnected.php', true, $template_variables );
				}
				break;
		}
	}

	/**
	 * Ajax request forms table handler to show the full table list when search is cleared.
	 *
	 * @return void
	 */
	public function forms_table_handler() {
		$forms_list_table = new Icontact_Forms_Forms_Table( $this->api->is_connected() );

		if ( isset( $_POST['search_nonce'] ) ) {
			$wpnonce = sanitize_text_field( wp_unslash( $_POST['search_nonce'] ) );
		}
		if ( isset( $_SERVER['HTTP_HOST'] ) ) {
			$server_host = sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) );
		}
		if ( isset( $_SERVER['REQUEST_URI'] ) ) {
			$request_uri = sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) );
		}

		// In this case we want the rows from first page.
		if ( ! empty( $_POST['pagination'] ) && wp_verify_nonce( $wpnonce, 'search_nonce' ) ) {
			// Second call send the pagination html for the table.
			$forms_list_table->prepare_items();

			ob_start();
			$pagination = $forms_list_table->pagination( 'top' );
			$pagination = ob_get_clean();

			$current_url = set_url_scheme( 'http://' . $server_host . $request_uri . '?' );

			$pagination = str_replace( $current_url, '?page=' . ICONTACT_FORMS_MENU_SLUG . '-forms&', $pagination );
			$response   = array( 'pagination' => $pagination );

			die( wp_json_encode( $response ) );
		} else {
			$forms_list_table->ajax_response();
		}
	}

	/**
	 * Add custom class to page body
	 *
	 * @since 1.0.0
	 *
	 * @param string $classes Add custom class to page body to have a global class for plugin CSS.
	 * @return string Classes to be added to the body of the page.
	 */
	public function css_body_class( string $classes ) {
		$classes .= ' ' . ICONTACT_FORMS_PLUGIN_NAME . '';
		return $classes;
	}

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( ICONTACT_FORMS_PLUGIN_NAME, ICONTACT_FORMS__PLUGIN_URL . 'admin/css/icontact-forms-admin.css', array(), ICONTACT_FORMS_VERSION, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
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

		wp_enqueue_script( ICONTACT_FORMS_PLUGIN_NAME, ICONTACT_FORMS__PLUGIN_URL . 'admin/js/icontact-forms-admin.js', array( 'jquery' ), ICONTACT_FORMS_VERSION, false );

	}
}
