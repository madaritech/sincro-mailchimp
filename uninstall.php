<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * @link  http://example.com
 * @since 1.0
 *
 * @package Synchro_Mailchimp
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$option_name = 'synchro_mailchimp_options';
delete_option( $option_name );
