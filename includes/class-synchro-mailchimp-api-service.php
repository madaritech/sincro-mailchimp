<?php
/**
 * The api functionality.
 *
 * @link
 * @since 1.0
 *
 * @package    Synchro_Mailchimp
 * @subpackage Synchro_Mailchimp/includes
 */

/**
 * The api functionality of the plugin from the MailChimp for WP plugin.
 *
 * @package    Synchro_Mailchimp
 * @subpackage Synchro_Mailchimp/includes
 * @author     Madaritech <freelance@madaritech.com>
 */
class Synchro_Mailchimp_Api_Service {


	/**
	 * Calls is_connected API from MailChimp for WP Plugin.
	 *
	 * @since  1.0
	 * @access public
	 */
	public function is_connected() {
		global $mc4wp;

		return ( $mc4wp['api']->is_connected() );
	}

	/**
	 * Calls get_lists API from MailChimp for WP Plugin.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @param array $args Parametri per l'API MailChimp.
	 */
	public function get_lists( $args ) {
		global $mc4wp;

		return ( $mc4wp['api']->get_lists( $args ) );
	}

	/**
	 * Calls get_list_interest_categories API from MailChimp for WP Plugin.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @param int   $list_id the list id.
	 * @param array $args MailChimp API parameters.
	 */
	public function get_list_interest_categories( $list_id, array $args = array() ) {
		global $mc4wp;

		return ( $mc4wp['api']->get_list_interest_categories( $list_id, $args ) );
	}

	/**
	 * Calls get_list_interest_category_interests API from MailChimp for WP Plugin.
	 *
	 * @param int   $list_id The list id.
	 * @param int   $interest_category_id The category interest id.
	 * @param array $args MailChimp API parameters.
	 *
	 * @since  1.0
	 * @access public
	 */
	public function get_list_interest_category_interests( $list_id, $interest_category_id, array $args = array() ) {
		global $mc4wp;

		return ( $mc4wp['api']->get_list_interest_category_interests( $list_id, $interest_category_id, $args ) );
	}

	/**
	 * Calls get_list_member API from MailChimp for WP Plugin.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @param string $list_id    Id della Mailing List.
	 * @param string $user_email Email dell'utente.
	 */
	public function get_list_member( $list_id, $user_email ) {
		global $mc4wp;

		return ( $mc4wp['api']->get_list_member( $list_id, $user_email ) );
	}

	/**
	 * Calls add_list_member API from MailChimp for WP Plugin.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @param string $list_id Id della Mailing List.
	 * @param array  $args    Parametri per l'API MailChimp.
	 */
	public function add_list_member( $list_id, $args ) {
		global $mc4wp;

		return ( $mc4wp['api']->add_list_member( $list_id, $args ) );
	}

	/**
	 * Calls delete_list_member API from MailChimp for WP Plugin.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @param string $list_id    Id della Mailing List.
	 * @param string $user_email Email dell'utente.
	 */
	public function delete_list_member( $list_id, $user_email ) {
		global $mc4wp;

		return ( $mc4wp['api']->delete_list_member( $list_id, $user_email ) );
	}
}
