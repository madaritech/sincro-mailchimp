<?php
/**
 * Fired during plugin activation
 *
 * @link
 * @since 1.0
 *
 * @package    Synchro_Mailchimp
 * @subpackage Plugin_Name/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0
 * @package    Synchro_Mailchimp
 * @subpackage Synchro_Mailchimp/includes
 * @author     Madaritech <freelance@madaritech.com>
 */
class Synchro_Mailchimp_Activator {
	/**
	 * Check previous settings on activation.
	 *
	 * If old settings are found on activation, the settings are not deleted, but reused.
	 *
	 * @since 1.0
	 */
	public static function activate() {
		if ( ! get_option( 'synchro_mailchimp_options' ) ) {
			global $wp_roles;
			$all_roles = $wp_roles->roles;

			$options = array();

			foreach ( $all_roles as $role => $name ) {
				$options[ $role ] = array();
			}

			update_option( 'synchro_mailchimp_options', serialize( $options ) );
		}
	}
}
