<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link
 * @since 1.0
 *
 * @package    Synchro_Mailchimp
 * @subpackage Synchro_Mailchimp/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * @package    Synchro_Mailchimp
 * @subpackage Synchro_Mailchimp/admin
 * @author     Madaritech <freelance@madaritech.com>
 */
class Synchro_Mailchimp_Admin {


	/**
	 * The ID of this plugin.
	 *
	 * @since  1.0
	 * @access private
	 * @var    string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since  1.0
	 * @access private
	 * @var    string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * A {@link Synchro_MailChimp_Log_Service} instance.
	 *
	 * @since 1.0
	 * @access private
	 * @var \Synchro_MailChimp_Log_Service $log A {@link Synchro_MailChimp_Log_Service} instance.
	 */
	private $log;

	/**
	 * Api Mailchimp.
	 *
	 * @since  1.0
	 * @access public
	 * @var object $api Handler for the MailChimp api, a {@link Synchro_MailChimp_Api_Service} instance.
	 */
	public $api;

	/**
	 * Subscription Service.
	 *
	 * @since  1.0
	 * @access private
	 * @var object $subscription_service Services used to executes all the operations to complete the subscription process.
	 */
	private $subscription_service;

	/**
	 * Requirements Service.
	 *
	 * @since  1.0
	 * @access private
	 * @var object $requirements_service Services used to check the requirements needed to let the plugin working properly.
	 */
	private $requirements_service;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 1.0
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->log = Synchro_MailChimp_Log_Service::create( 'Synchro_Mailchimp_Admin' );
		$this->subscription_service = new Synchro_MailChimp_Subscription_Service();
		$this->requirements_service = new Synchro_MailChimp_Requirements_Service();
		$this->api = new Synchro_Mailchimp_Api_Service();

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since 1.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/synchro-mailchimp-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since 1.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/synchro-mailchimp-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Extract all the lists, categories and interests schema related to the MailChimp Key registered.
	 *
	 * @param array $mailchimp_lists Mailchimp lists: ['list_id' => ['name' => 'list_name', 'checked' => false] ].
	 * @param array $mailchimp_interest_categories Mailchimp categories and interests: ['list_id' => ['category_id' => 'category_name'] ].
	 * @param array $mailchimp_interests Mailchimp categories and interests: ['category_id' => ['interest_id' => ['name' => 'interest_name', 'checked' => false]]].
	 *
	 * @since    1.0
	 * @access public
	 */
	public function read_mailchimp_schema( &$mailchimp_lists, &$mailchimp_interest_categories, &$mailchimp_interests ) {

		// Extract lists from MailChimp.
		$lists_obj = $this->api->get_lists( array() );
		$list_arr = array();

		if ( ! is_null( $lists_obj ) && ! empty( $lists_obj ) && isset( $lists_obj ) ) {
			$list_arr = json_decode( json_encode( $lists_obj ), true );
		}

		// For each list extracted...
		foreach ( $list_arr as $list ) {

			// ...save list name..
			$mailchimp_lists[ $list['id'] ] = [
				'name' => $list['name'],
				'checked' => false,
			];

			// ...extract the list interest categories from MailChimp...
			$interest_categories_obj = $this->api->get_list_interest_categories( $list['id'] );
			$interest_categories_arr = json_decode( json_encode( $interest_categories_obj ), true );

			// ... and foreach interest category extracted...
			foreach ( $interest_categories_arr as $interest_category ) {

				// ...save the interest category and...
				$mailchimp_interest_categories[ $list['id'] ][ $interest_category['id'] ] = $interest_category['title'];

				// ...extract from MailChimp the category interests...
				$interests_obj = $this->api->get_list_interest_category_interests( $list['id'], $interest_category['id'] );
				$interests_arr = json_decode( json_encode( $interests_obj ), true );

				// ...finally for each interest...
				foreach ( $interests_arr as $interest ) {

					// ...save the interest informations.
					$mailchimp_interests[ $interest_category['id'] ][ $interest['id'] ] = [
						'name' => $interest['name'],
						'checked' => false,
					];
				}
			}
		}
	}

	/**
	 * Using the MailChimp Schema, with the actual configuration configured by the user sent via form, updates the configuration option with the actual role, lists and interests that have to be associated on subscription process to the particular user.
	 *
	 * @param array $configuration_options Array for the configuration of the lists and interests values actually associated to role for every subscribed user.
	 * @param array $mailchimp_lists Mailchimp lists: ['list_id' => ['name' => 'list_name', 'checked' => false] ].
	 * @param array $mailchimp_interest_categories Mailchimp categories and interests: ['list_id' => ['category_id' => 'category_name'] ].
	 * @param array $mailchimp_interests Mailchimp categories and interests: ['category_id' => ['interest_id' => ['name' => 'interest_name', 'checked' => false]]].
	 *
	 * @return array $configuration_options Array for the configuration of the lists and interests values actually associated to role for every subscribed user
	 *
	 * @since    1.0
	 * @access public
	 */
	public function build_configuration_option( $configuration_options, $mailchimp_lists, $mailchimp_interest_categories, $mailchimp_interests ) {

		global $wp_roles;
		$all_roles = $wp_roles->roles;

		foreach ( $all_roles as $role => $role_name ) {

			$configuration_options[ $role ] = array();

			foreach ( $mailchimp_lists as $list_id => $list_array ) {

				if ( isset( $_POST[ $role . '-list-' . $list_id ] ) && absint( $_POST[ $role . '-list-' . $list_id ] ) == $list_id ) {

					$configuration_options[ $role ][ $list_id ] = array();

					if ( isset( $mailchimp_interest_categories[ $list_id ] ) ) {
						foreach ( $mailchimp_interest_categories[ $list_id ] as $category_id => $category_name ) {

							foreach ( $mailchimp_interests[ $category_id ] as $interest_id => $interest_array ) {

								if ( isset( $_POST[ $role . '-list-' . $list_id . '-interest-' . $interest_id ] ) && absint( $_POST[ $role . '-list-' . $list_id . '-interest-' . $interest_id ] ) == $interest_id ) {

									$configuration_options[ $role ][ $list_id ][ $interest_id ] = true;

								} else {
									$configuration_options[ $role ][ $list_id ][ $interest_id ] = false;
								}
							}
						}
					}
				}
			}
		}

		return($configuration_options);
	}

	/**
	 * Using the actual configuration option, updates the MailChimp schemas of every roles indicating the checked lists and checked interests.
	 *
	 * @param array $all_roles List of all the user roles, standard and custom.
	 * @param array $configuration Array for the configuration of the lists and interests values actually associated to role for every subscribed user.
	 * @param array $settings_lists Mailchimp lists to update with checked tags: ['list_id' => ['name' => 'list_name', 'checked' => false] ].
	 * @param array $settings_interest_categories Mailchimp categories and interests to update with checked tags: ['list_id' => ['category_id' => 'category_name'] ].
	 * @param array $settings_interests Mailchimp categories and interests to update with checked tags: ['category_id' => ['interest_id' => ['name' => 'interest_name', 'checked' => false]]].
	 *
	 * @since    1.0
	 * @access public
	 */
	public function build_setting_form( $all_roles, $configuration, &$settings_lists, &$settings_interest_categories, &$settings_interests ) {

		// Update 'checked' property using configuration, and assignment to the proper wp role.
		// Scan every role.
		foreach ( $all_roles as $role => $role_name ) {

			// The $configuration was extracted by MailChimp site, and it contains only the selected lists and interests.
			foreach ( $configuration[ $role ] as $configuration_list_id => $configuration_interest_array ) {

				// The $setting_* contains all the values, without know if checked or not: in this function we find out and set properly thei values. Here we cycle on all the lists for the $role.
				foreach ( $settings_lists[ $role ] as $mailchimp_list_id => $mailchimp_list_array ) {

					// If the list exists in $configuration, it means is checked on MailChimp, so we have to check it too on the form.
					if ( $mailchimp_list_id == $configuration_list_id ) {

						// Check the list!
						$settings_lists[ $role ][ $mailchimp_list_id ]['checked'] = true;

						// Now examine the related interests.
						// If there are interest categories for the list...
						if ( isset( $settings_interest_categories[ $role ][ $mailchimp_list_id ] ) ) {

							// ...let's cycle over these categories...
							foreach ( $settings_interest_categories[ $role ][ $mailchimp_list_id ] as $mailchimp_category_id => $mailchimp_category_name ) {

								// ...and so from the category let's cycle over all the relative interests.
								foreach ( $settings_interests[ $role ][ $mailchimp_category_id ] as $mailchimp_interest_id => $mailchimp_interest_bool ) {

									// If the particular interest exists in the configuration array, it means have to be checked. We get that value from the $configuration_interest_array that contain all the set checked interests.
									if ( array_key_exists( $mailchimp_interest_id, $configuration_interest_array ) ) {

										$settings_interests[ $role ][ $mailchimp_category_id ][ $mailchimp_interest_id ]['checked'] = $configuration_interest_array[ $mailchimp_interest_id ];

									}
								}
							}
						}
					}
				}
			}
		}
	}

	/**
	 * The Admin Menu for the plugin.
	 *
	 * @since 1.0
	 */
	public function synchro_mailchimp_admin_menu() {
		add_menu_page(
			'Synchro MailChimp Plugin',
			'Synchro MC',
			'manage_options',
			'synchro-mailchimp',
			array( &$this, 'synchro_mailchimp_settings_page' ),
			plugins_url( '/images/madaritech_logo.png', __FILE__ )
		);
	}

	/**
	 * Create the Settings Page for the admin area.
	 *
	 * @since    1.0
	 */
	public function synchro_mailchimp_settings_page() {

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html( 'You do not have sufficient permissions to access this page.', 'synchro_mailchimp' ) );
		}

		if ( ! $this->requirements_service->mfw_is_missing() ) {

			$save_settings = false;

			$configuration_options = array();

			// mailchimp_lists structure: ['list_id' => ['name' => 'list_name', 'checked' => false] ].
			$mailchimp_lists = array();

			// mailchimp_interest_categories structure: ['list_id' => ['category_id' => 'category_name'] ].
			$mailchimp_interest_categories = array();

			// mailchimp_interests structure: ['category_id' => ['interest_id' => ['name' => 'interest_name', 'checked' => false]]].
			$mailchimp_interests = array();

			$this->read_mailchimp_schema( $mailchimp_lists, $mailchimp_interest_categories, $mailchimp_interests );

			// Settings page posts setting data to itself.
			if ( isset( $_POST['form_submitted'] ) ) {

				// Form submitted, so we check the nonce.
				check_admin_referer( 'mdt_setting_configuration', 'conf_set_nonce' );

				$save_settings = true;

				$configuration_options = $this->build_configuration_option( $configuration_options, $mailchimp_lists, $mailchimp_interest_categories, $mailchimp_interests );

				update_option( 'synchro_mailchimp_options', serialize( $configuration_options ) );
			}

			$synchro_mailchimp_options = get_option( 'synchro_mailchimp_options' );
			$configuration = unserialize( $synchro_mailchimp_options );

			$settings_lists;
			$settings_interest_categories;
			$settings_interests;

			global $wp_roles;
			$all_roles = $wp_roles->roles;

			foreach ( $all_roles as $role => $role_name ) {
				// Initializing mailchimp lists and interests for the role for the settings page.
				$settings_lists[ $role ] = $mailchimp_lists;
				$settings_interest_categories[ $role ] = $mailchimp_interest_categories;
				$settings_interests[ $role ] = $mailchimp_interests;
			}

			$this->build_setting_form( $all_roles, $configuration, $settings_lists, $settings_interest_categories, $settings_interests );
		}

		require_once( 'partials/synchro-mailchimp-admin-display.php' );

	}

	/**
	 * The field on the editing screens.
	 *
	 * @param WP_User $user The user who is proceeding to subscribe to MailChimp.
	 *
	 * @since 1.0
	 */
	public function form_field_iscrizione_mailing_list( $user ) {
		if ( ! $this->requirements_service->mfw_is_missing() ) {
			$checked = 0;

			// Estrazione dati utente.
			$user_email = $user->user_email;
			$user_role  = $user->roles[0];

			$subscription_status = $this->subscription_service->check_subscription_status( $user_email, $user_role );

			if ( 2 == $subscription_status ) {
				$checked = 1;
			}

			wp_enqueue_script( 'sm', plugin_dir_url( __FILE__ ) . 'js/synchro-mailchimp-admin-ajax.js', array( 'jquery' ), $this->version, true );

			$params = array(
				'user_email' => esc_js( $user->user_email ),
				'user_role'  => esc_js( $user->roles[0] ),
				'_wpnonce'   => wp_create_nonce( 'esegui_iscrizione' ),
			);

			wp_localize_script( 'sm', 'sm', $params );

			include_once 'partials/synchro-mailchimp-users-admin-display.php';
		}
	}

	/**
	 * In base ai parametri ricevuti via post esegue o meno l'iscrizione.
	 *
	 * @since 1.0
	 */
	public function esegui_iscrizione() {
		check_admin_referer( 'esegui_iscrizione', '_wpnonce' );

		$check_status = isset( $_POST['check_status'] ) ? intval( $_POST['check_status'] ) : -1;
		$user_email   = isset( $_POST['user_email'] ) ? sanitize_email( wp_unslash( $_POST['user_email'] ) ) : '';
		$user_role    = isset( $_POST['user_role'] ) ? sanitize_text_field( wp_unslash( $_POST['user_role'] ) ) : '';
		$ut           = isset( $_POST['ut'] ) ? intval( $_POST['ut'] ) : 0;

		if ( ! is_email( $user_email ) || $check_status < 0 || $check_status > 1 ) {
			wp_send_json_error( $check_status );
		}

		if ( $ut ) {
			wp_send_json_success( 'Verifica Unit Test' );
		}

		if ( ! current_user_can( 'administrator' ) ) {
			wp_send_json_error( __( 'Insufficient permissions, operation failed', 'synchro_mailchimp' ) );
		}

		// Elaborazione.
		try {
			$subscription_status = $this->subscription_service->check_subscription_status( $user_email, $user_role );
			$this->log->debug( "Checking subscrition status [ subscription status :: $subscription_status ][ user e-mail :: $user_email ][ user role :: $user_role ]" );
		} catch ( Exception $e ) {
			$error_message = __( 'Subscription check status failed. ', 'synchro_mailchimp' );
			wp_send_json_error( $error_message . $e->getMessage() );
		}

		if ( ! $subscription_status ) {
			wp_send_json_error( __( 'No configuration available, operation failed.', 'synchro_mailchimp' ) );
		}

		if ( 4 == $subscription_status ) {
			wp_send_json_error( __( 'The configuration on MailChimp has changed. Before subscribe the user go to Synchro MC settings page, update the configuration and press "Save Settings" button.', 'synchro_mailchimp' ) );
		}

		$this->log->debug( "Checkbox status received [ check status :: $check_status ]" );

		if ( $check_status ) {
			try {
				$this->subscription_service->subscribe_process( $subscription_status, $user_email, $user_role );
			} catch ( Exception $e ) {
				$error_message = __( 'Subscription process failure. ', 'synchro_mailchimp' );
				wp_send_json_error( $error_message . $e->getMessage() );
			}
		} else {
			try {
				$this->subscription_service->unsubscribe_process( $subscription_status, $user_email, $user_role );
			} catch ( Exception $e ) {
				$error_message = __( 'Subscription delete process failure. ', 'synchro_mailchimp' );
				wp_send_json_error( $error_message . $e->getMessage() );
			}
		}

		wp_send_json_success( __( 'Operation performed.', 'synchro_mailchimp' ) );
	}

}
