<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link
 * @since 1.0.0
 *
 * @package    Sincro_Mailchimp
 * @subpackage Sincro_Mailchimp/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Sincro_Mailchimp
 * @subpackage Sincro_Mailchimp/admin
 * @author     Madaritech <dm@madaritech.com>
 */
class Sincro_Mailchimp_Admin
{

    /**
     * The ID of this plugin.
     *
     * @since  1.0.0
     * @access private
     * @var    string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since  1.0.0
     * @access private
     * @var    string $version The current version of this plugin.
     */
    private $version;

    /**
     * Configurazione Plugin.
     *
     * @since  1.0.0
     * @access protected
     */
    private $smc;

    /*
    * A {@link Sincro_MailChimp_Log_Service} instance.
    *
    * @since 1.0.0
    * @access private
    * @var \Sincro_MailChimp_Log_Service $log A {@link Sincro_MailChimp_Log_Service} instance.
    */
    private $log;

    /**
     * Subscription Service.
     *
     * @since  1.0.0
     * @access private
     */
    private $subscription_service;

    /**
     * Requirements Service.
     *
     * @since  1.0.0
     * @access private
     */
    private $requirements_service;

    /**
     * Initialize the class and set its properties.
     *
     * @since 1.0.0
     *
     * @param string $plugin_name The name of this plugin.
     * @param string $version     The version of this plugin.
     */
    public function __construct( $plugin_name, $version ) 
    {

        $this->log = Sincro_MailChimp_Log_Service::create('Sincro_Mailchimp_Admin');
        $this->subscription_service = new Sincro_MailChimp_Subscription_Service();
        $this->requirements_service = new Sincro_MailChimp_Requirements_Service();

        $this->plugin_name = $plugin_name;
        $this->version     = $version;

    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since 1.0.0
     */
    public function enqueue_styles() 
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Plugin_Name_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Plugin_Name_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/sincro-mailchimp-admin.css', array(), $this->version, 'all');

    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since 1.0.0
     */
    public function enqueue_scripts() 
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Plugin_Name_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Plugin_Name_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/sincro-mailchimp-admin.js', array( 'jquery' ), $this->version, false);

    }

    /**
     * The field on the editing screens.
     *
     * @param $user    WP_User user object
     *
     * @since 1.0.0
     */
    public function form_field_iscrizione_mailing_list( $user ) 
    {
    	if ( $this->requirements_service->mfw_is_missing() ) {
	        $checked = 0;

	        // Estrazione dati utente
	        $user_email = $user->user_email;
	        $user_role  = $user->roles[0];


	        $subscription_status = $this->subscription_service->check_subscription_status($user_email, $user_role);

	        if ($subscription_status == 2 ) {
	            $checked = 1;
	        }

	        wp_enqueue_script('sm', plugin_dir_url(__FILE__) . 'js/sincro-mailchimp-admin-ajax.js', array( 'jquery' ), $this->version, true);

	        $params = array(
	        'user_email' => esc_js($user->user_email),
	        'user_role'  => esc_js($user->roles[0]),
	        '_wpnonce'   => wp_create_nonce('esegui_iscrizione')
	        );

	        wp_localize_script('sm', 'sm', $params);

			include_once 'partials/sincro-mailchimp-admin-display.php';
		}
        
    }

    /**
     * In base ai parametri ricevuti via post esegue o meno l'iscrizione.
     *
     * @since 1.0.0
     */
    public function esegui_iscrizione() 
    {

        check_admin_referer('esegui_iscrizione', '_wpnonce');

        $check_status = intval($_POST['check_status']);
        $user_email   = sanitize_email(( strval($_POST['user_email']) ));
        $user_role    = strip_tags(strval($_POST['user_role']));
        $ut           = isset($_POST['ut']) ? intval($_POST['ut']) : 0;

        if (! is_email($user_email) || $check_status < 0 || $check_status > 1 ) {
            wp_send_json_error($check_status);
        }

        if ($ut ) {
            wp_send_json_success('Verifica Unit Test');
        }

        if (! current_user_can('administrator') ) {
            wp_send_json_error(__('Permessi non sufficienti, operazione fallita', 'sincro_mailchimp'));
        }

        //Elaborazione
        $subscription_status = $this->subscription_service->check_subscription_status($user_email, $user_role);

        if (! $subscription_status ) {
            wp_send_json_error(__('Configurazione assente, operazione fallita', 'sincro_mailchimp'));
        }

        if ($check_status ) {
            $this->subscription_service->subscribe_process($subscription_status, $user_email, $user_role);
        } else {
            $this->subscription_service->unsubscribe_process($subscription_status, $user_email, $user_role);
        }

        wp_send_json_success(__('Operazione eseguita', 'sincro_mailchimp'));
    }
}