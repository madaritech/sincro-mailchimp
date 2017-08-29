<?php

/**
 *
 * @link    http://www.madaritech.com
 * @since   1.0.0
 * @package Sincro_Mailchimp
 *
 * @wordpress-plugin
 * Plugin Name:       Sincro Mailchimp
 * Plugin URI:        http://www.madaritech.com
 * Description:       The plugin permits administrators to subscribe/unsubscribe WordPress users to MailChimp by a checkbox on the users	*                    page. Every user, depending on their roles and the template defined in wp-config file, can be associated to MailChimp *                    lists and interests. The plugin requires MailChimp for WordPress plugin.
 * Version:           1.0.0
 * Author:            Madaritech
 * Author URI:        http://www.madaritech.com
 * License:           GPLv2 or later.
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       sincro-mailchimp
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (! defined('WPINC') ) {
    die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-sincro-mailchimp-activator.php
 */
function activate_sincro_mailchimp() 
{
    include_once plugin_dir_path(__FILE__) . 'includes/class-sincro-mailchimp-activator.php';
    Sincro_Mailchimp_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-sincro-mailchimp-deactivator.php
 */
function deactivate_sincro_mailchimp() 
{
    include_once plugin_dir_path(__FILE__) . 'includes/class-sincro-mailchimp-deactivator.php';
    Sincro_Mailchimp_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_sincro_mailchimp');
register_deactivation_hook(__FILE__, 'deactivate_sincro_mailchimp');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-sincro-mailchimp.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since 1.0.0
 */
function run_sincro_mailchimp() 
{

    $plugin = new Sincro_Mailchimp();
    $plugin->run();

}
run_sincro_mailchimp();
