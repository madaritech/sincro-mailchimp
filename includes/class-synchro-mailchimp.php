<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link
 * @since 1.0
 *
 * @package    Synchro_Mailchimp
 * @subpackage Synchro_Mailchimp/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0
 * @package    Synchro_Mailchimp
 * @subpackage Synchro_Mailchimp/includes
 * @author     Madaritech <freelance@madaritech.com>
 */
class Synchro_Mailchimp {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    Synchro_Mailchimp_Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    string $synchro_mailchimp The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    string $version The current version of the plugin.
	 */
	protected $version;

	/**
	 * The {@link Synchro_Mailchimp_Configuration_Service} instance.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    \Synchro_Mailchimp_Configuration_Service $configuration_service The {@link Synchro_Mailchimp_Configuration_Service} instance.
	 */
	protected $configuration_service;

	/**
	 * The {@link Synchro_Mailchimp_User_Service} instance.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    \Synchro_Mailchimp_User_Service $user_service The {@link Synchro_Mailchimp_User_Service} instance.
	 */
	protected $user_service;

	/**
	 * The {@link Synchro_Mailchimp_User_Service_Adapter} instance.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    \Synchro_Mailchimp_User_Service_Adapter $user_service The {@link Synchro_Mailchimp_User_Service_Adapter} instance.
	 */
	protected $user_service_adapter;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since 1.0
	 */
	public function __construct() {

		$this->plugin_name = 'synchro_mailchimp';
		$this->version     = '1.2';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Synchro_Mailchimp_Loader. Orchestrates the hooks of the plugin.
	 * - Synchro_Mailchimp_i18n. Defines internationalization functionality.
	 * - Synchro_Mailchimp_Admin. Defines all hooks for the admin area.
	 * - Synchro_Mailchimp_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since  1.0
	 * @access private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-synchro-mailchimp-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-synchro-mailchimp-i18n.php';

		/**
		 * Services.
		 */
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-synchro-mailchimp-log-service.php';
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-synchro-mailchimp-configuration-service.php';
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-synchro-mailchimp-user-service.php';
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-synchro-mailchimp-api-service.php';
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-synchro-mailchimp-subscription-service.php';
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-synchro-mailchimp-requirements-service.php';

		/**
		 * Adapters.
		 */
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-synchro-mailchimp-user-service-adapter.php';

		/**
		 * The classes responsible for defining all actions that occur in the admin area.
		 */
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-synchro-mailchimp-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-synchro-mailchimp-public.php';

		$this->loader = new Synchro_Mailchimp_Loader();

		/**
		 * Services.
		*/
		$this->configuration_service = new Synchro_Mailchimp_Configuration_Service();
		$this->user_service          = new Synchro_Mailchimp_User_Service( $this->configuration_service );

		/**
		 * Adapters.
		*/
		$this->user_service_adapter = new Synchro_Mailchimp_User_Service_Adapter( $this->user_service );

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Synchro_Mailchimp_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since  1.0
	 * @access private
	 */
	private function set_locale() {

		$plugin_i18n = new Synchro_Mailchimp_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since  1.0
	 * @access private
	 */
	private function define_admin_hooks() {

		$plugin_admin           = new Synchro_Mailchimp_Admin( $this->get_plugin_name(), $this->get_version() );
		$plugin_requirements    = new Synchro_Mailchimp_Requirements_Service();

		$this->loader->add_action( 'admin_notices', $plugin_requirements, 'mfw_missing_admin_notice' );

		$this->loader->add_action( 'admin_menu', $plugin_admin, 'synchro_mailchimp_admin_menu' );
		$this->loader->add_action( 'show_user_profile', $plugin_admin, 'form_field_iscrizione_mailing_list' );
		$this->loader->add_action( 'edit_user_profile', $plugin_admin, 'form_field_iscrizione_mailing_list' );
		$this->loader->add_action( 'wp_ajax_esegui_iscrizione', $plugin_admin, 'esegui_iscrizione' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		// Hook to `sm_user_list`.
		$this->loader->add_filter( 'sm_user_list', $this->user_service_adapter, 'user_lists', 10, 3 );

		// Hook to `sm_user_list_interests`.
		$this->loader->add_filter( 'sm_user_list_interests', $this->user_service_adapter, 'user_list_interests', 10, 3 );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since  1.0
	 * @access private
	 */
	private function define_public_hooks() {

		$plugin_public = new Synchro_Mailchimp_Public( $this->get_plugin_name(), $this->get_version() );

		// $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		// $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		// Hook to `sm_user_list_interests`.
		$this->loader->add_filter( 'sm_user_list_interests', $this->user_service_adapter, 'user_list_interests', 10, 3 );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since 1.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since  1.0
	 * @return string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since  1.0
	 * @return Plugin_Name_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since  1.0
	 * @return string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
