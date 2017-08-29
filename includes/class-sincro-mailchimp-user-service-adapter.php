<?php
/**
 * Adapters: User Service Adapter.
 *
 * Define an adapter that hooks WP's actions/filters to the {@link Sincro_Mailchimp_User_Service}.
 *
 * @since      1.0.0
 * @package    Sincro_Mailchimp
 * @subpackage Sincro_Mailchimp/includes
 */

/**
 * Define the {@link Sincro_Mailchimp_User_Service_Adapter} class.
 *
 * @since      1.0.0
 * @package    Sincro_Mailchimp
 * @subpackage Sincro_Mailchimp/includes
 */
class Sincro_Mailchimp_User_Service_Adapter
{

    /**
     * A {@link Sincro_MailChimp_Log_Service} instance.
     *
     * @since  1.0.0
     * @access private
     * @var    \Sincro_MailChimp_Log_Service $log A {@link Sincro_MailChimp_Log_Service} instance.
     */
    private $log;

    /**
     * The {@link Sincro_Mailchimp_User_Service} instance.
     *
     * @since  1.0.0
     * @access private
     * @var    \Sincro_Mailchimp_User_Service $user_service The {@link Sincro_Mailchimp_User_Service} instance.
     */
    private $user_service;

    /**
     * Create a {@link Sincro_Mailchimp_User_Service_Adapter} instance.
     *
     * @since 1.0.0
     *
     * @param \Sincro_Mailchimp_User_Service $user_service The {@link Sincro_Mailchimp_User_Service} instance.
     */
    public function __construct( $user_service ) 
    {

        $this->log = Sincro_MailChimp_Log_Service::create('Sincro_Mailchimp_User_Service_Adapter');

        $this->user_service = $user_service;

    }

    /**
     * Called by the `sm_user_lists`, processes the lists with the relative interests for a {@link WP_User}
     *
     * @since 1.0.0
     *
     * @uses Sincro_Mailchimp_User_Service::get_lists() to get the interests.
     *
     * @param array  $interests {
     * The precomputed array of interests.
     *
     * @type string  $key The interest id.
     * @type boolean $value True if the interest needs to be bound to the user, otherwise false.
     * }
     *
     * @param int    $user_id   The {@link WP_User}'s id.
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
    public function user_lists($lists, $user_id) 
    {

        if (Sincro_MailChimp_Log_Service::is_enabled() ) {
            $this->log->debug("Getting the lists and interests for user $user_id");
        }

        return $this->user_service->get_lists($lists, $user_id);
    }

    /**
     * Called by the `sm_user_list_interests`, processes the interests for a {@link WP_User}/list
     * combination.
     *
     * @since 1.0.0
     *
     * @uses Sincro_Mailchimp_User_Service::get_interests() to get the interests.
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
    public function user_list_interests( $interests, $user_id, $list_id ) 
    {

        if (Sincro_MailChimp_Log_Service::is_enabled() ) {
            $this->log->debug("Getting the interests for user $user_id and list $list_id...");
        }

        return $this->user_service->get_interests($user_id, $list_id, $interests);
    }

}
