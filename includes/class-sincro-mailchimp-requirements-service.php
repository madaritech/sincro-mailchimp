<?php

/**
 * The MailChimp subscription service.
 *
 * @link
 * @since 1.0.0
 *
 * @package    Sincro_Mailchimp
 * @subpackage Sincro_Mailchimp/admin
 */

/**
 * The core subscription functionality of the plugin.
 *
 * @package    Sincro_Mailchimp
 * @subpackage Sincro_Mailchimp/admin
 * @author     Madaritech <freelance@madaritech.com>
 */
class Sincro_Mailchimp_Requirements_Service
{

    /*
    * A {@link Sincro_MailChimp_Log_Service} instance.
    *
    * @since 1.0.0
    * @access private
    * @var \Sincro_MailChimp_Log_Service $log A {@link Sincro_MailChimp_Log_Service} instance.
    */
    private $log;

    /**
     * Initialize the class and set its properties.
     *
     * @since 1.0.0
     */
    public function __construct() 
    {
        $this->log = Sincro_MailChimp_Log_Service::create('Sincro_Mailchimp_Admin_Requirements_Service');
    }

    /**
     * Check if MailChimp for WordPress is active.
     *
     * @since  1.0.0
     * @access public
     */
    public function mfw_is_missing() {
        // is_plugin_active is only available from within the admin pages. If you want to use this function you will need to manually require plugin.php
        include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        return (is_plugin_active( 'mailchimp-for-wp/mailchimp-for-wp.php' ) );
    }

    /**
     * Missing MailChimp for WordPress notice.
     *
     * @since  1.0.0
     * @access public
     */
    public function mfw_missing_admin_notice() {
        if ( !$this->mfw_is_missing() ) :
    ?>
    <div class="notice error is-dismissible" >
        <p><?php _e( 'Sincro MailChimp per funzionare richiede che il plugin MailChimp per WordPress sia installato ed attivo. Installalo ora!', 'sincro_mailchimp' ); ?></p>
    </div>
    <?php
        endif;
    }
}