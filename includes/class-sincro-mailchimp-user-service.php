<?php
/**
 * Services: User Service.
 *
 * The {@link Sincro_Mailchimp_User_Service} provides methods related to {@link WP_User}s such as
 * getting the lists/interests a {@link WP_User} should be subscribed to.
 *
 * @since 1.0.0
 * @package Sincro_Mailchimp
 * @subpackage Sincro_Mailchimp/includes
 */

/**
 * Define the {@link Sincro_Mailchimp_User_Service} class.
 *
 * @since 1.0.0
 * @package Sincro_Mailchimp
 * @subpackage Sincro_Mailchimp/includes
 */
class Sincro_Mailchimp_User_Service {

	/**
	 * The {@link Sincro_Mailchimp_Configuration_Service} instance.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var \Sincro_Mailchimp_Configuration_Service $configuration_service The {@link Sincro_Mailchimp_Configuration_Service} instance.
	 */
	private $configuration_service;

	/**
	 * Create a {@link Sincro_Mailchimp_User_Service} instance.
	 *
	 * @since 1.0.0
	 *
	 * @param \Sincro_Mailchimp_Configuration_Service $configuration_service The {@link Sincro_Mailchimp_Configuration_Service} instance.
	 */
	public function __construct( $configuration_service ) {

		$this->configuration_service = $configuration_service;

	}

	/**
	 * Get the list of interests for a {@link WP_User} and MailChimp list.
	 *
	 * @since 1.0.0
	 *
	 * @param int    $user_id The {@link WP_User}'s id.
	 * @param string $list_id MailChimp's list id.
	 *
	 * @return array {
	 * An array of interests.
	 *
	 * @type string  $key The interest id.
	 * @type boolean $value True if the interest needs to be bound to the user, otherwise false.
	 * }
	 */
	public function get_interests( $user_id, $list_id ) {

		// Get the user.
		$user = get_user_by( 'id', $user_id );

		// Return an empty array if the user doesn't exist.
		if ( null === $user ) {
			return array();
		}

		// Initialize the return array.
		$interests = array();

		// Cycle in the user's role.
		foreach ( $user->roles as $role ) {

			// Get the interests for the specified role.
			$role_interests = $this->configuration_service->get_by_role_and_list( $role, $list_id );

			// Add the interest to the return array and combine it with the existing value if any.
			foreach ( $role_interests as $key => $value ) {
				$interests[ $key ] = ( isset( $interests[ $key ] ) ? $interests[ $key ] : false ) || $value;
			}

		}

		return $interests;
	}

}
