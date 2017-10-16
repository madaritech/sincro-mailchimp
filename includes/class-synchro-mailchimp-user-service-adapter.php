<?php
/**
 * Adapters: User Service Adapter.
 *
 * Define an adapter that hooks WP's actions/filters to the {@link Synchro_Mailchimp_User_Service}.
 *
 * @since      1.0
 * @package    Synchro_Mailchimp
 * @subpackage Synchro_Mailchimp/includes
 */

/**
 * Define the {@link Synchro_Mailchimp_User_Service_Adapter} class.
 *
 * @since      1.0
 * @package    Synchro_Mailchimp
 * @subpackage Synchro_Mailchimp/includes
 */
class Synchro_Mailchimp_User_Service_Adapter {
	/**
	 * A {@link Synchro_MailChimp_Log_Service} instance.
	 *
	 * @since  1.0
	 * @access private
	 * @var    \Synchro_MailChimp_Log_Service $log A {@link Synchro_MailChimp_Log_Service} instance.
	 */
	private $log;

	/**
	 * The {@link Synchro_Mailchimp_User_Service} instance.
	 *
	 * @since  1.0
	 * @access private
	 * @var    \Synchro_Mailchimp_User_Service $user_service The {@link Synchro_Mailchimp_User_Service} instance.
	 */
	private $user_service;

	/**
	 * Create a {@link Synchro_Mailchimp_User_Service_Adapter} instance.
	 *
	 * @since 1.0
	 *
	 * @param \Synchro_Mailchimp_User_Service $user_service The {@link Synchro_Mailchimp_User_Service} instance.
	 */
	public function __construct( $user_service ) {

		$this->log = Synchro_MailChimp_Log_Service::create( 'Synchro_Mailchimp_User_Service_Adapter' );

		$this->user_service = $user_service;

	}

	/**
	 * Called by the `sm_user_lists`, processes the lists with the relative interests for a {@link WP_User}
	 *
	 * @since 1.0
	 *
	 * @uses Synchro_Mailchimp_User_Service::get_lists() to get the interests.
	 *
	 * @param array $lists The precomputed array of interests. The interest id as key and a boolean value that is True if the interest needs to be bound to the user, otherwise false.
	 *
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
	public function user_lists( $lists, $user_id ) {

		$this->log->debug( "Getting the lists and interests for user $user_id" );
		return $this->user_service->get_lists( $lists, $user_id );
	}

	/**
	 * Called by the `sm_user_list_interests`, processes the interests for a {@link WP_User}/list
	 * combination.
	 *
	 * @since 1.0
	 *
	 * @uses Synchro_Mailchimp_User_Service::get_interests() to get the interests.
	 *
	 * @param array  $interests {
	 * The precomputed array of interests.
	 *
	 * @type string  $key The interest id.
	 * @type boolean $value True if the interest needs to be bound to the user, otherwise false.
	 * }
	 *
	 * @param int    $user_id   The {@link WP_User}'s id.
	 * @param string $list_id   MailChimp's list id.
	 *
	 * @return array {
	 * An array of interests.
	 *
	 * @type string  $key The interest id.
	 * @type boolean $value True if the interest needs to be bound to the user, otherwise false.
	 * }
	 */
	public function user_list_interests( $interests, $user_id, $list_id ) {
		$this->log->debug( "Getting the interests for user $user_id and list $list_id..." );
		return $this->user_service->get_interests( $user_id, $list_id, $interests );
	}

}
