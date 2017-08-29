<?php
/**
 * Services: User Service.
 *
 * The {@link Sincro_Mailchimp_User_Service} provides methods related to {@link WP_User}s such as
 * getting the lists/interests a {@link WP_User} should be subscribed to.
 *
 * @since      1.0.0
 * @package    Sincro_Mailchimp
 * @subpackage Sincro_Mailchimp/includes
 */

/**
 * Define the {@link Sincro_Mailchimp_User_Service} class.
 *
 * @since      1.0.0
 * @package    Sincro_Mailchimp
 * @subpackage Sincro_Mailchimp/includes
 */
class Sincro_Mailchimp_User_Service
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
     * The {@link Sincro_Mailchimp_Configuration_Service} instance.
     *
     * @since  1.0.0
     * @access private
     * @var    \Sincro_Mailchimp_Configuration_Service $configuration_service The {@link Sincro_Mailchimp_Configuration_Service} instance.
     */
    private $configuration_service;

    /**
     * Create a {@link Sincro_Mailchimp_User_Service} instance.
     *
     * @since 1.0.0
     *
     * @param \Sincro_Mailchimp_Configuration_Service $configuration_service The {@link Sincro_Mailchimp_Configuration_Service} instance.
     */
    public function __construct( $configuration_service ) 
    {

        $this->log = Sincro_MailChimp_Log_Service::create('Sincro_Mailchimp_User_Service');

        $this->configuration_service = $configuration_service;

    }

    /**
     * Get the MailChimp lists and relative interests for a {@link WP_User}.
     *
     * @since 1.0.0
     *
     * @param int    $user_id The {@link WP_User}'s id.
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
    public function get_lists( $lists, $user_id ) 
    {

        if (Sincro_MailChimp_Log_Service::is_enabled() ) {
            $this->log->debug("Getting the lists for user $user_id");
        }

        // Get the user.
        $user = get_user_by('id', $user_id);

        // Return an empty array if the user doesn't exist.
        if (false === $user ) {

            if (Sincro_MailChimp_Log_Service::is_enabled() ) {
                $this->log->warn("User $user_id not found.");
            }

            return $lists;
        }

        // Cycle in the user's role.
        foreach ( $user->roles as $role ) {

            // Get the lists for the specified role.
            $role_lists = $this->configuration_service->get_by_role($role);

            if (Sincro_MailChimp_Log_Service::is_enabled() ) {
                $this->log->trace('Got ' . count($role_lists) . " list(s) for user $user_id, role $role.");
            }

            // Add the list to the return array and combine it with the existing value if any
            foreach ( $role_lists as $list_id => $interests ) {
                $lists[ $list_id ] = ( isset($lists[ $list_id ]) ? $lists[ $list_id ] : $interests );
            }

        }

        if (Sincro_MailChimp_Log_Service::is_enabled() ) {
            $this->log->info('Found ' . count($lists) . " list(s) for user $user_id: " . var_export($lists, true));
        }

        return $lists;
    }


    /**
     * Get the list of interests for a {@link WP_User} and MailChimp list.
     *
     * @since 1.0.0
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
    public function get_interests( $user_id, $list_id, $seed = array() ) 
    {

        if (Sincro_MailChimp_Log_Service::is_enabled() ) {
            $this->log->debug("Getting the interests for user $user_id and list $list_id...");
        }

        // Get the user.
        $user = get_user_by('id', $user_id);

        // Return an empty array if the user doesn't exist.
        if (false === $user ) {

            if (Sincro_MailChimp_Log_Service::is_enabled() ) {
                $this->log->warn("User $user_id not found.");
            }

            return $seed;
        }

        // Initialize the return array.
        $interests = $seed;

        // Cycle in the user's role.
        foreach ( $user->roles as $role ) {

            // Get the interests for the specified role.
            $role_interests = $this->configuration_service->get_by_role_and_list($role, $list_id);

            if (Sincro_MailChimp_Log_Service::is_enabled() ) {
                $this->log->trace('Got ' . count($role_interests) . " interest(s) for user $user_id, role $role, list $list_id.");
            }

            // Add the interest to the return array and combine it with the existing value if any: the seed is not changed, only new keys are added with their values
            foreach ( $role_interests as $key => $value ) {
                $interests[ $key ] = ( isset($interests[ $key ]) ? $interests[ $key ] : $value );
            }

        }

        if (Sincro_MailChimp_Log_Service::is_enabled() ) {
            $this->log->info('Found ' . count($interests) . " interest(s) for user $user_id and list $list_id: " . var_export($interests, true));
        }

        return $interests;
    }

}
