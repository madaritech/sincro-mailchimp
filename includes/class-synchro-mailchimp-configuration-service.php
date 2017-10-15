<?php
/**
 * Services: Configuration Service.
 *
 * The {@link Synchro_Mailchimp_Configuration_Service} provides functions to access the plugin
 * configuration.
 *
 * @since      1.0
 * @package    Synchro_Mailchimp
 * @subpackage Synchro_Mailchimp/includes
 */

/**
 * Define the {@link Synchro_Mailchimp_Configuration_Service} class.
 *
 * @since      1.0
 * @package    Synchro_Mailchimp
 * @subpackage Synchro_Mailchimp/includes
 */
class Synchro_Mailchimp_Configuration_Service {


	/**
	 * The configuration array.
	 *
	 * @since  1.0
	 * @access private
	 * @var    array $configuration The configuration array.
	 */
	private $configuration;

	/**
	 * A {@link Synchro_MailChimp_Log_Service} instance.
	 *
	 * @since 1.0
	 * @access private
	 * @var \Synchro_MailChimp_Log_Service $log A {@link Synchro_MailChimp_Log_Service} instance.
	 */
	private $log;

	/**
	 * Create a {@link Synchro_Mailchimp_Configuration_Service} instance.
	 *
	 * @since 1.0
	 */
	public function __construct() {
		$synchro_mailchimp_options = get_option( 'synchro_mailchimp_options' );
		$this->configuration = isset( $synchro_mailchimp_options ) ? unserialize( $synchro_mailchimp_options ) : array();
	}

	/**
	 * Set the configuration.
	 *
	 * @since 1.0
	 *
	 * @param array $configuration The configuration array.
	 * @access public
	 */
	public function set_configuration( $configuration ) {
		$this->configuration = $configuration;
	}

	/**
	 * Get the configuration given a role name.
	 *
	 * @since 1.0
	 *
	 * @param string $role The role name.
	 *
	 * @return array The configuration array for the specified role, or an empty array if not found.
	 */
	public function get_by_role( $role ) {
		return isset( $this->configuration[ $role ] ) ? $this->configuration[ $role ] : array();
	}

	/**
	 * Get the configuration given a role name and a list id.
	 *
	 * @since 1.0
	 *
	 * @param string $role    The {@link WP_User}'s role.
	 * @param string $list_id The MailChimp list id.
	 *
	 * @return array The configuration array for the specified role and list id, or an empty array
	 * if not found.
	 */
	public function get_by_role_and_list( $role, $list_id ) {

		$configuration_by_role = $this->get_by_role( $role );

		return isset( $configuration_by_role[ $list_id ] ) ? $configuration_by_role[ $list_id ] : array();
	}

}
