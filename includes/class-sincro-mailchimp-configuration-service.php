<?php
/**
 * Services: Configuration Service.
 *
 * The {@link Sincro_Mailchimp_Configuration_Service} provides functions to access the plugin
 * configuration.
 *
 * @since      1.0.0
 * @package    Sincro_Mailchimp
 * @subpackage Sincro_Mailchimp/includes
 */

/**
 * Define the {@link Sincro_Mailchimp_Configuration_Service} class.
 *
 * @since      1.0.0
 * @package    Sincro_Mailchimp
 * @subpackage Sincro_Mailchimp/includes
 */
class Sincro_Mailchimp_Configuration_Service
{

    /**
     * The configuration array.
     *
     * @since  1.0.0
     * @access private
     * @var    array $configuration The configuration array.
     */
    private $configuration;

    /**
     * Create a {@link Sincro_Mailchimp_Configuration_Service} instance.
     *
     * @since 1.0.0
     *
     * @param array $configuration The configuration array.
     */
    public function __construct( $configuration ) 
    {

        $this->configuration = $configuration;

    }

    /**
     * Get the configuration given a role name.
     *
     * @since 1.0.0
     *
     * @param string $role The role name.
     *
     * @return array The configuration array for the specified role, or an empty array if not found.
     */
    public function get_by_role( $role ) 
    {
        return isset($this->configuration[ $role ]) ? $this->configuration[ $role ] : array();
    }

    /**
     * Get the configuration given a role name and a list id.
     *
     * @since 1.0.0
     *
     * @param string $role    The {@link WP_User}'s role.
     * @param string $list_id The MailChimp list id.
     *
     * @return array The configuration array for the specified role and list id, or an empty array
     * if not found.
     */
    public function get_by_role_and_list( $role, $list_id ) 
    {

        $configuration_by_role = $this->get_by_role($role);

        return isset($configuration_by_role[ $list_id ]) ? $configuration_by_role[ $list_id ] : array();
    }

}
