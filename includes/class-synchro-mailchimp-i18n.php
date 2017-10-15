<?php
/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link
 * @since 1.0
 *
 * @package    Synchro_Mailchimp
 * @subpackage Synchro_Mailchimp/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0
 * @package    Synchro_Mailchimp
 * @subpackage Synchro_Mailchimp/includes
 * @author     Madaritech <freelance@madaritech.com>
 */
class Synchro_Mailchimp_i18n {
	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since 1.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'synchro_mailchimp',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
