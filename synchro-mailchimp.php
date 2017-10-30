<?php
/**
 * Subscribe/unsubscribe WordPress users to MailChimp
 *
 * @link    http://www.madaritech.com
 * @since   1.0
 * @package Synchro_Mailchimp
 *
 * @wordpress-plugin
 * Plugin Name:       Synchro MailChimp
 * Description:       The plugin permits administrators to subscribe/unsubscribe WordPress users to MailChimp by a checkbox on the WordPress user settings page. Every user, depending on their roles and the template defined in plugin settings page, can be associated to MailChimp lists and interests. The plugin requires MailChimp for WordPress plugin.
 * Version:           1.6
 * Author:            Madaritech
 * Author URI:        http://www.madaritech.com
 * License:           GPL2
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       synchro-mailchimp
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-synchro-mailchimp-activator.php
 */
function activate_synchro_mailchimp() {
	include_once plugin_dir_path( __FILE__ ) . 'includes/class-synchro-mailchimp-activator.php';
	Synchro_Mailchimp_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-synchro-mailchimp-deactivator.php
 */
function deactivate_synchro_mailchimp() {
	include_once plugin_dir_path( __FILE__ ) . 'includes/class-synchro-mailchimp-deactivator.php';
	Synchro_Mailchimp_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_synchro_mailchimp' );
register_deactivation_hook( __FILE__, 'deactivate_synchro_mailchimp' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-synchro-mailchimp.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since 1.0
 */
function run_synchro_mailchimp() {

	$plugin = new Synchro_Mailchimp();
	$plugin->run();

}
run_synchro_mailchimp();
