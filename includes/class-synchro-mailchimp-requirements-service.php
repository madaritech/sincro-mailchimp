<?php
/**
 * The MailChimp subscription service.
 *
 * @link
 * @since 1.0
 *
 * @package    Synchro_Mailchimp
 * @subpackage Synchro_Mailchimp/admin
 */

/**
 * The core subscription functionality of the plugin.
 *
 * @package    Synchro_Mailchimp
 * @subpackage Synchro_Mailchimp/admin
 * @author     Madaritech <freelance@madaritech.com>
 */
class Synchro_Mailchimp_Requirements_Service {
	/**
	 * A {@link Synchro_MailChimp_Log_Service} instance.
	 *
	 * @since 1.0
	 * @access private
	 * @var \Synchro_MailChimp_Log_Service $log A {@link Synchro_MailChimp_Log_Service} instance.
	 */
	private $log;

	/**
	 * Api Mailchimp.
	 *
	 * @since  1.0
	 * @access public
	 * @var \Synchro_MailChimp_Api_Service $api A {@link Synchro_MailChimp_Api_Service} instance.
	 */
	private $api;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 1.0
	 */
	public function __construct() {
		$this->log = Synchro_MailChimp_Log_Service::create( 'Synchro_Mailchimp_Admin_Requirements_Service' );
		$this->api = new Synchro_Mailchimp_Api_Service();
	}

	/**
	 * Check if MailChimp for WordPress is active.
	 *
	 * @since  1.0
	 * @access public
	 */
	public function mfw_is_missing() {
		// is_plugin_active is only available from within the admin pages. If you want to use this function you will need to manually require plugin.php.
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		return ( ! is_plugin_active( 'mailchimp-for-wp/mailchimp-for-wp.php' ) || ! $this->api->is_connected());
	}

	/**
	 * Missing MailChimp for WordPress notice.
	 *
	 * @since  1.0
	 * @access public
	 */
	public function mfw_missing_admin_notice() {
		if ( $this->mfw_is_missing() ) :
	?>
	<div class="notice error is-dismissible" >
		<p>
			<?php

				$allowed_html = array(
					'em' => array(),
					'strong' => array(),
				);
				$html_text = wp_kses( '<strong>Synchro MailChimp</strong> needs <strong>MailChimp for WordPress</strong> installed, active and configured. Download it now! ', $allowed_html );
				echo __( $html_text, 'synchro_mailchimp' );
			?>
			<a href="https://it.wordpress.org/plugins/mailchimp-for-wp/" target="_blank">MailChimp for WordPress</a></p>
	</div>
	<?php endif;
	}
}
?>
