<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link
 * @since      1.0.0
 *
 * @package    Sincro_Mailchimp
 * @subpackage Sincro_Mailchimp/admin
 */

/**
 * The api functionality of the plugin from the MailChimp for WP plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Sincro_Mailchimp
 * @subpackage Sincro_Mailchimp/admin
 * @author     Madaritech <dm@madaritech.com>
 */
class Sincro_Mailchimp_Admin_Api {

	/**
	 * Richiama l'API get_lists dal Plugin MailChimp for WP.
	 *
	 * @since    1.0.0
	 * @access   public
	 *
	 * @param    array $args Parametri per l'API MailChimp.
	 */
	public function get_lists( $args ) {
		global $mc4wp;

		return ( $mc4wp['api']->get_lists( $args ) );
	}

	/**
	 * Richiama l'API get_list_member dal Plugin MailChimp for WP.
	 *
	 * @since    1.0.0
	 * @access   public
	 *
	 * @param    string $list_id Id della Mailing List.
	 * @param    string $user_email Email dell'utente.
	 */
	public function get_list_member( $list_id, $user_email ) {
		global $mc4wp;

		return ( $mc4wp['api']->get_list_member( $list_id, $user_email ) );
	}

	/**
	 * Richiama l'API add_list_member dal Plugin MailChimp for WP.
	 *
	 * @since    1.0.0
	 * @access   public
	 *
	 * @param    string $list_id Id della Mailing List.
	 * @param    array $args Parametri per l'API MailChimp.
	 */
	public function add_list_member( $list_id, $args ) {
		global $mc4wp;

		return ( $mc4wp['api']->add_list_member( $list_id, $args ) );
	}

	/**
	 * Richiama l'API delete_list_member dal Plugin MailChimp for WP.
	 *
	 * @since    1.0.0
	 * @access   public
	 *
	 * @param    string $list_id Id della Mailing List.
	 * @param    string $user_email Email dell'utente.
	 */
	public function delete_list_member( $list_id, $user_email ) {
		global $mc4wp;

		return ( $mc4wp['api']->delete_list_member( $list_id, $user_email ) );
	}
}