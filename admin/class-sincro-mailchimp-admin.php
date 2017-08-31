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
 * @author     Madaritech <freelance@madaritech.com>
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

    /*
    * A {@link Sincro_MailChimp_Log_Service} instance.
    *
    * @since 1.0.0
    * @access private
    * @var \Sincro_MailChimp_Log_Service $log A {@link Sincro_MailChimp_Log_Service} instance.
    */
    private $log;

    /**
     * Api Mailchimp.
     *
     * @since  1.0.0
     * @access public
     */
    public $api;

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
        $this->api = new Sincro_Mailchimp_Api_Service();

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
     * Extract all the lists, categories and interests schema related to the MailChimp Key registered.
     *
     * @param array $mailchimp_lists Mailchimp lists: ['list_id' => ['name' => 'list_name', 'checked' => false] ]
     * @param array  $mailchimp_interest_categories Mailchimp categories and interests: ['list_id' => ['category_id' => 'category_name'] ]
     * @param array  $mailchimp_interests Mailchimp categories and interests: ['category_id' => ['interest_id' => ['name' => 'interest_name', 'checked' => false]]]
     *
     * @since    1.0.0
     * @access public
     */
    public function read_mailchimp_schema(&$mailchimp_lists, &$mailchimp_interest_categories, &$mailchimp_interests) {

        $lists_obj = $this->api->get_lists(array());
        $list_arr = json_decode(json_encode($lists_obj), true);

        foreach ($list_arr as $list) {

            $mailchimp_lists[$list['id']] = [ 'name' => $list['name'], 'checked' => false ];
            $interest_categories_obj = $this->api->get_list_interest_categories( $list['id'] );
            $interest_categories_arr = json_decode(json_encode($interest_categories_obj), true);

            foreach ($interest_categories_arr as $interest_category) {
                
                $mailchimp_interest_categories[$list['id']][$interest_category['id']] = $interest_category['title'];
                $interests_obj = $this->api->get_list_interest_category_interests( $list['id'], $interest_category['id'] );
                $interests_arr = json_decode(json_encode($interests_obj), true);

                foreach ($interests_arr as $interest) {
                    $mailchimp_interests[$interest_category['id']][$interest['id']] = [ 'name' => $interest['name'], 'checked' => false ];
                }
            }    
        }
    }

    /**
     * Using the MailChimp Schema, with the actual configuration configured by the user sent via form, updates the configuration option with the actual role, lists and interests that have to be associated on subscription process to the particular user.
     *
     * @param array $configuration_options Array for the configuration of the lists and interests values actually associated to role for every subscribed user 
     * @param array $mailchimp_lists Mailchimp lists: ['list_id' => ['name' => 'list_name', 'checked' => false] ]
     * @param array  $mailchimp_interest_categories Mailchimp categories and interests: ['list_id' => ['category_id' => 'category_name'] ]
     * @param array  $mailchimp_interests Mailchimp categories and interests: ['category_id' => ['interest_id' => ['name' => 'interest_name', 'checked' => false]]]
     *
     * @return array $configuration_options Array for the configuration of the lists and interests values actually associated to role for every subscribed user 
     *
     * @since    1.0.0
     * @access public
     */
    public function build_configuration_option($configuration_options, $mailchimp_lists, $mailchimp_interest_categories, $mailchimp_interests) {

        global $wp_roles;
        $all_roles = $wp_roles->roles;

        foreach ($all_roles as $role => $role_name) {

                    $configuration_options[$role] = array();

                    foreach ($mailchimp_lists as $list_id => $list_array) {

                        if ( isset($_POST[$role.'-list-'.$list_id]) && esc_html($_POST[$role.'-list-'.$list_id]) == $list_id )  {
                            
                            $configuration_options[$role][$list_id] = array();

                            foreach ( $mailchimp_interest_categories[$list_id] as $category_id => $category_name) {
                                
                                foreach ( $mailchimp_interests[$category_id] as $interest_id => $interest_array) {

                                    if ( isset($_POST[$role.'-list-'.$list_id.'-interest-'.$interest_id]) && esc_html($_POST[$role.'-list-'.$list_id.'-interest-'.$interest_id]) == $interest_id ) {

                                        $configuration_options[$role][$list_id][$interest_id] = true;

                                    }
                                    else {
                                        $configuration_options[$role][$list_id][$interest_id] = false;
                                    }

                                }
                            }
                        }
                    }

                }

        return($configuration_options);
    }

    /**
     * Using the actual configuration option, updates the MailChimp schemas of every roles indicating the checked lists and checked interests.
     *
     * @param array $configuration Array for the configuration of the lists and interests values actually associated to role for every subscribed user 
     * @param array $settings_lists Mailchimp lists to update with checked tags: ['list_id' => ['name' => 'list_name', 'checked' => false] ]
     * @param array  $settings_interest_categories Mailchimp categories and interests to update with checked tags: ['list_id' => ['category_id' => 'category_name'] ]
     * @param array  $settings_interests Mailchimp categories and interests to update with checked tags: ['category_id' => ['interest_id' => ['name' => 'interest_name', 'checked' => false]]] 
     *
     * @since    1.0.0
     * @access public
     */
    public function build_setting_form($all_roles, $configuration, &$settings_lists, &$settings_interest_categories, &$settings_interests) {

        //Update 'checked' property using configuration, and assignment to the proper wp role
        foreach ($all_roles as $role => $role_name) {
            
            foreach ($configuration[$role] as $configuration_list_id => $configuration_interest_array) {

                foreach ($settings_lists[$role] as $mailchimp_list_id => $mailchimp_list_array) {
                        
                    if ( $mailchimp_list_id == $configuration_list_id ) {

                        //Checked sulla lista
                        $settings_lists[$role][$mailchimp_list_id]['checked'] = true;
                    
                    }

                    if ($settings_lists[$role][$mailchimp_list_id]['checked']) {
                        foreach ($settings_interest_categories[$role][$mailchimp_list_id] as $mailchimp_category_id => $mailchimp_category_name) {
                            
                            foreach ($settings_interests[$role][$mailchimp_category_id] as $mailchimp_interest_id => $mailchimp_interest_bool) {
                            
                                if (array_key_exists($mailchimp_interest_id, $configuration_interest_array)) {

                                    $settings_interests[$role][$mailchimp_category_id][$mailchimp_interest_id]['checked'] = $configuration_interest_array[$mailchimp_interest_id];
                            
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * The Admin Menu for the plugin.
     *
     * @since 1.0.0
     */
    public function sincro_mailchimp_admin_menu( ) 
    {
        add_menu_page(
            'Sincro MailChimp Plugin',
            'Sincro MailChimp',
            'manage_options',
            'sincro-mailchimp',
            array(&$this, 'sincro_mailchimp_settings_page')
        );   
    }

    /**
     * Create the Settings Page for the admin area.
     *
     * @since    1.0.0
     */
    public function sincro_mailchimp_settings_page() {

        if( !current_user_can( 'manage_options' ) ) {
            wp_die( __('You do not have sufficient permissions to access this page.', 'sincro_mailchimp') );
        }

        $configuration_options = array();
        $mailchimp_lists = array();                //['list_id' => ['name' => 'list_name', 'checked' => false] ]
        $mailchimp_interest_categories = array();  //['list_id' => ['category_id' => 'category_name'] ]
        $mailchimp_interests = array();            //['category_id' => ['interest_id' => ['name' => 'interest_name', 'checked' => false]]]

        $this->read_mailchimp_schema($mailchimp_lists, $mailchimp_interest_categories, $mailchimp_interests);

        if (isset($_POST['form_submitted'])) {

            $hidden_field = esc_html( $_POST['form_submitted'] );

            if ($hidden_field == 'Y') {

                $configuration_options = $this->build_configuration_option($configuration_options, $mailchimp_lists, $mailchimp_interest_categories, $mailchimp_interests);

                update_option('sincro_mailchimp_options', serialize($configuration_options));
            }
        }

        $sincro_mailchimp_options = get_option('sincro_mailchimp_options');
        $configuration = unserialize($sincro_mailchimp_options);        

        $settings_lists;
        $settings_interest_categories;
        $settings_interests;
        
        global $wp_roles;
        $all_roles = $wp_roles->roles;

        foreach ($all_roles as $role => $role_name) {
            //Initializing mailchimp lists and interests for the role for the settings page
            $settings_lists[$role] = $mailchimp_lists;
            $settings_interest_categories[$role] = $mailchimp_interest_categories;
            $settings_interests[$role] = $mailchimp_interests;
        }

        $this->build_setting_form($all_roles, $configuration, $settings_lists, $settings_interest_categories, $settings_interests);

        require_once('partials/sincro-mailchimp-admin-display.php');
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

			include_once 'partials/sincro-mailchimp-users-admin-display.php';
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
        try {
            $subscription_status = $this->subscription_service->check_subscription_status($user_email, $user_role);
            if (Sincro_MailChimp_Log_Service::is_enabled() ) {
            $this->log->debug("Checking subscrition status [ subscription status :: $subscription_status ][ user e-mail :: $user_email ][ user role :: $user_role ]");
            }
        } catch (Exception $e) {
            $error_message = __("Verifica stato sottoscrizione fallita. ", 'sincro_mailchimp');
            wp_send_json_error($error_message.$e->getMessage());
        }

        if (! $subscription_status ) {
            wp_send_json_error(__('Configurazione assente, operazione fallita', 'sincro_mailchimp'));
        }

        if (Sincro_MailChimp_Log_Service::is_enabled() ) {
            $this->log->debug("Checkbox status received [ check status :: $check_status ]");
        }

        if ($check_status ) {
            try {
                $this->subscription_service->subscribe_process($subscription_status, $user_email, $user_role);
            }
            catch (Exception $e) {
                $error_message = __("Processo di sottoscrizione fallito. ", 'sincro_mailchimp');
                wp_send_json_error($error_message.$e->getMessage());
            }
        } else {
            try {            
                $this->subscription_service->unsubscribe_process($subscription_status, $user_email, $user_role);
            }
            catch (Exception $e) {
                $error_message = __("Processo di cancellazione della sottoscrizione fallito. ", 'sincro_mailchimp');
                wp_send_json_error($error_message.$e->getMessage());
            }
        }

        wp_send_json_success(__('Operazione eseguita', 'sincro_mailchimp'));
    }

}