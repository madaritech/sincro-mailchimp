<?php
/**
 * Services: User Service.
 *
 * The {@link Synchro_Mailchimp_User_Service} provides methods related to {@link WP_User}s such as
 * getting the lists/interests a {@link WP_User} should be subscribed to.
 *
 * @since      1.0
 * @package    Synchro_Mailchimp
 * @subpackage Synchro_Mailchimp/includes
 */

/**
 * Define the {@link Synchro_Mailchimp_User_Service} class.
 *
 * @since      1.0
 * @package    Synchro_Mailchimp
 * @subpackage Synchro_Mailchimp/includes
 */
class Synchro_Mailchimp_User_Service {


	/**
	 * A {@link Synchro_MailChimp_Log_Service} instance.
	 *
	 * @since  1.0
	 * @access private
	 * @var    \Synchro_MailChimp_Log_Service $log A {@link Synchro_MailChimp_Log_Service} instance.
	 */
	private $log;

	/**
	 * The {@link Synchro_Mailchimp_Configuration_Service} instance.
	 *
	 * @since  1.0
	 * @access private
	 * @var    \Synchro_Mailchimp_Configuration_Service $configuration_service The {@link Synchro_Mailchimp_Configuration_Service} instance.
	 */
	private $configuration_service;

	/**
	 * Create a {@link Synchro_Mailchimp_User_Service} instance.
	 *
	 * @since 1.0
	 *
	 * @param \Synchro_Mailchimp_Configuration_Service $configuration_service The {@link Synchro_Mailchimp_Configuration_Service} instance.
	 */
	public function __construct( $configuration_service ) {

		$this->log = Synchro_MailChimp_Log_Service::create( 'Synchro_Mailchimp_User_Service' );

		$this->configuration_service = $configuration_service;

	}

	/**
	 * Get the MailChimp lists and relative interests for a {@link WP_User}.
	 *
	 * @since 1.0
	 *
	 * @param array $lists The lists associated with the user.
	 * @param int   $user_id The {@link WP_User}'s id.
	 *
	 * @return array {
	 * An array of lists of interests.
	 *
	 * @type string  $key The list id.
	 * @type array {
	 *      @type int $key the interest id.
	 *      @type boolean $value True if the interest needs to be bound to the user, otherwise false.
	 *    }
	 * }
	 */
	public function get_lists( $lists, $user_id ) {

		$this->log->debug( "Getting the lists for user $user_id" );

		// Get the user.
		$user = get_user_by( 'id', $user_id );

		// Return an empty array if the user doesn't exist.
		if ( false === $user ) {
			$this->log->warn( "User $user_id not found." );
			return $lists;
		}

		// Cycle in the user's role.
		foreach ( $user->roles as $role ) {

			// Get the lists for the specified role.
			$role_lists = $this->configuration_service->get_by_role( $role );

			$this->log->trace( 'Got ' . count( $role_lists ) . " list(s) for user $user_id, role $role." );

			// Add the list to the return array and combine it with the existing value if any.
			foreach ( $role_lists as $list_id => $interests ) {
				$lists[ $list_id ] = ( isset( $lists[ $list_id ] ) ? $lists[ $list_id ] : $interests );
			}
		}

		$this->log->info( 'Found ' . count( $lists ) . " list(s) for user $user_id: " . var_export( $lists, true ) );

		return $lists;
	}


	/**
	 * Get the list of interests for a {@link WP_User} and MailChimp list.
	 *
	 * @since 1.0
	 *
	 * @param int    $user_id The {@link WP_User}'s id.
	 * @param string $list_id MailChimp's list id.
	 * @param array  $seed    { An initial array of interests.
	 * An initial array of interests.
	 *
	 * @type string  $key The interest id.
	 * @type boolean $value True if the interest needs to be bound to the user, otherwise false.
	 * }
	 *
	 * @return array {
	 * An array of interests.
	 *
	 * @type string  $key The interest id.
	 * @type boolean $value True if the interest needs to be bound to the user, otherwise false.
	 * }
	 */
	public function get_interests( $user_id, $list_id, $seed = array() ) {

		$this->log->debug( "Getting the interests for user $user_id and list $list_id..." );

		// Get the user.
		$user = get_user_by( 'id', $user_id );

		// Return an empty array if the user doesn't exist.
		if ( false === $user ) {
			$this->log->warn( "User $user_id not found." );
			return $seed;
		}

		// Initialize the return array.
		$interests = $seed;

		// Cycle in the user's role.
		foreach ( $user->roles as $role ) {

			// Get the interests for the specified role.
			$role_interests = $this->configuration_service->get_by_role_and_list( $role, $list_id );

			$this->log->trace( 'Got ' . count( $role_interests ) . " interest(s) for user $user_id, role $role, list $list_id." );

			// Add the interest to the return array and combine it with the existing value if any: the seed is not changed, only new keys are added with their values.
			foreach ( $role_interests as $key => $value ) {
				$interests[ $key ] = ( isset( $interests[ $key ] ) ? $interests[ $key ] : $value );
			}
		}

		$this->log->info( 'Found ' . count( $interests ) . " interest(s) for user $user_id and list $list_id: " . var_export( $interests, true ) );

		return $interests;
	}

}
