<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link
 * @since 1.0
 * @package    Synchro_Mailchimp
 * @subpackage Synchro_Mailchimp/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Synchro_Mailchimp
 * @subpackage Synchro_Mailchimp/public
 * @author     Madaritech <freelance@madaritech.com>
 */
class Synchro_Mailchimp_Public {


	/**
	 * The ID of this plugin.
	 *
	 * @since  1.0
	 * @access private
	 * @var    string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since  1.0
	 * @access private
	 * @var    string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 1.0
	 * @param string $plugin_name The name of the plugin.
	 * @param string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

}
