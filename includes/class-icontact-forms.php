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
class Icontact_Forms {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Icontact_Forms_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		$this->load_dependencies();
		$this->set_locale();
		$this->define_forms_index_hooks();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->define_shortcode_hooks();
		$this->define_block_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Icontact_Forms_Loader. Orchestrates the hooks of the plugin.
	 * - Icontact_Forms_i18n. Defines internationalization functionality.
	 * - Icontact_Forms_Admin. Defines all hooks for the admin area.
	 * - Icontact_Forms_Public. Defines all hooks for the public side of the site.
	 * - iContactApi. This class is a wrapper for the iContact API.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		$this->loader = new Icontact_Forms_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Icontact_Forms_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Icontact_Forms_I18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the forms index
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_forms_index_hooks() {
		$plugin_forms_index = new Icontact_Forms_Forms_Index();

		// Update form index.
		$this->loader->add_action( ICONTACT_FORMS_OPTION_PREFIX . 'update_form_index', $plugin_forms_index, 'update_forms_index' );

		// Get form index.
		$this->loader->add_filter( ICONTACT_FORMS_OPTION_PREFIX . 'get_form_index', $plugin_forms_index, 'get_forms_index' );

		// Get form output.
		$this->loader->add_filter( ICONTACT_FORMS_OPTION_PREFIX . 'get_form_output', $plugin_forms_index, 'get_form_output' );

		// Get form output by ajax.
		$this->loader->add_action( 'wp_ajax_' . ICONTACT_FORMS_OPTION_PREFIX . 'form_output', $plugin_forms_index, 'form_output_handler' );

		// Get form output by ajax.
		$this->loader->add_action( 'wp_ajax_' . ICONTACT_FORMS_OPTION_PREFIX . 'form_index', $plugin_forms_index, 'form_index_handler' );
	}

	/**
	 * Register all of the hooks related to the shortcode functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_shortcode_hooks() {
		$plugin_shortcode = new Icontact_Forms_Icform_Shortcode();

		// Register shortcode.
		$this->loader->add_action( 'init', $plugin_shortcode, 'shortcode_init' );

	}

	/**
	 * Register all of the hooks related to the block functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_block_hooks() {
		$plugin_block = new Icontact_Forms_Block();

		$this->loader->add_action( 'init', $plugin_block, 'block_init' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {
		$plugin_admin = new Icontact_Forms_Admin();

		// Add CSS styles.
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );

		// Add admin scripts.
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		// Create menus.
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'menus' );

		// Register settings.
		$this->loader->add_action( 'admin_init', $plugin_admin, 'register_admin_settings' );

		// Add initial activation banner.
		$this->loader->add_action( 'admin_notices', $plugin_admin, 'admin_activation_notice' );

		// Add admin top menu at the beginning of the content section.
		$this->loader->add_action( 'in_admin_header', $plugin_admin, 'render_admin_top_menu' );

		// Process the post of the API credetianles form submission.
		$this->loader->add_action( 'wp_ajax_' . ICONTACT_FORMS_OPTION_PREFIX . 'disconnect_button', $plugin_admin, 'disconnect_button_handler' );

		// Create "Settings" link in plugin listing.
		$this->loader->add_filter( 'plugin_action_links', $plugin_admin, 'add_plugin_link', 10, 2 );

		// Add custom Body class for the plugin.
		$this->loader->add_filter( 'admin_body_class', $plugin_admin, 'css_body_class', 1, 1 );

		// Forms table ajax response.
		$this->loader->add_action( 'wp_ajax_' . ICONTACT_FORMS_OPTION_PREFIX . 'table_ajax', $plugin_admin, 'forms_table_handler' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {
		$plugin_public = new Icontact_Forms_Public();

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Icontact_Forms_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}
}
