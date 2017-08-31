<?php

/**
 * Fired during plugin activation
 *
 * @link       
 * @since 1.0.0
 *
 * @package    Sincro_Mailchimp
 * @subpackage Plugin_Name/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Sincro_Mailchimp
 * @subpackage Sincro_Mailchimp/includes
 * @author     Madaritech <freelance@madaritech.com>
 */
class Sincro_Mailchimp_Activator
{

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since 1.0.0
     */
    public static function activate() 
    {
        global $wp_roles;
        $all_roles = $wp_roles->roles;

        $options = array();
        
        foreach ($all_roles as $role => $name) {
            $options[$role] = array();
        }
        
        update_option( 'sincro_mailchimp_options', serialize($options) );
    }

}
