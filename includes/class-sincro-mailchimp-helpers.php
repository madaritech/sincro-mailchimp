<?php

/**
 * Define helpers functionality and override function also for testing mock purpose.
 *
 * @link       
 * @since      1.0.0
 *
 * @package    Sincro_Mailchimp
 * @subpackage Sincro_Mailchimp/includes
 */

/**
 * Define helpers.
 *
 * @since      1.0.0
 * @package    Sincro_Mailchimp
 * @subpackage Sincro_Mailchimp/includes
 * @author     Dario <dm@madaritech.com>
 */
class Sincro_Mailchimp_Helpers {

	/**
	 * Given mail extract the user id.
	 *
	 * @since    1.0.0
	 * @access   public
	 *
	 * @param    string $user_email email dell'utente.
	 */
	public function get_id_by_email( $user_email ) {

		$user = get_user_by( 'email', $user_email );
		return ( $user->ID );
	
	}

}
