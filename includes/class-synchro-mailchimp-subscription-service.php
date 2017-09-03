<?php

/**
 * The MailChimp subscription service.
 *
 * @link
 * @since 1.0.0
 *
 * @package    Synchro_Mailchimp
 * @subpackage Synchro_Mailchimp/includes
 */

/**
 * The core subscription functionality of the plugin.
 *
 * @package    Synchro_Mailchimp
 * @subpackage Synchro_Mailchimp/includes
 * @author     Madaritech <freelance@madaritech.com>
 */
class Synchro_Mailchimp_Subscription_Service
{

    /*
    * A {@link Synchro_MailChimp_Log_Service} instance.
    *
    * @since 1.0.0
    * @access private
    * @var \Synchro_MailChimp_Log_Service $log A {@link Synchro_MailChimp_Log_Service} instance.
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
     * Configurazione Plugin.
     *
     * @since  1.0.0
     * @access private
     */
    private $configuration;

    /**
     * Initialize the class and set its properties.
     *
     * @since 1.0.0
     */
    public function __construct() 
    {

        $this->log = Synchro_MailChimp_Log_Service::create('Synchro_Mailchimp_Subscription_Service');
        $this->api = new Synchro_Mailchimp_Api_Service();
        $this->configuration = new Synchro_Mailchimp_Configuration_Service();

    }

    /**
     * Configuration set method.
     *
     * @since 1.0.0
     */
    public function set_configuration($configuration) 
    {
        $this->configuration = $configuration;
    }

    /**
     * Implementa la logica del processo di sottoscrizione.
     *
     * @param $subscription_status
     * @param $user_email
     * @param $user_role
     *
     * @since 1.0.0
     */
    public function subscribe_process( $subscription_status, $user_email, $user_role ) 
    {

        if (Synchro_MailChimp_Log_Service::is_enabled() ) {
            $this->log->debug("Subscribing [ subscription status :: $subscription_status ][ user e-mail :: $user_email ][ user role :: $user_role ]...");
        }

        $res = false;

        switch ( $subscription_status ) {
            case 0:
                // Configurazione vuota: non eseguo nulla
                break;
            case 1:
                // Procedo con l'iscrizione
                $res = $this->subscribe_user($user_email);

                break;
            case 2:
                // Utente già iscritto correttamente
                break;
            case 3:
                // Utente iscritto parzialmente o in modo diverso rispetto la configurazione
                
                //Reset iscrizione parziale
                if ($this->unsubscribe_user_mailchimp($user_email) ) {
                    // Procedo con iscrizione da configurazione
                    $res = $this->subscribe_user($user_email);
                }

                break;

            default:
                break;
        }

        if (Synchro_MailChimp_Log_Service::is_enabled() ) {
            $this->log->info("Subscribed [ subscription status :: $subscription_status ][ user e-mail :: $user_email ][ user role :: $user_role ].");
        }

        return ( $res );

    }

    /**
     * Implementa la logica del processo di cancellazione della sottoscrizione.
     *
     * @param $subscription_status
     * @param $user_email
     * @param $user_role
     *
     * @since 1.0.0
     */
    public function unsubscribe_process( $subscription_status, $user_email, $user_role ) 
    {

        $res = false;

        switch ( $subscription_status ) {
        case 0:
            // Configurazione vuota: non eseguo nulla
            break;
        case 1:
            // Utente non iscritto
            break;
        case 2:
            // Utente iscritto secondo configurazione
            $res = $this->unsubscribe_user_config($user_email);

            break;
        case 3:
            // Utente iscritto parzialmente o in modo diverso rispetto la configurazione
            $res = $this->unsubscribe_user_mailchimp($user_email);

            break;
        default:
            break;
        }

        return ( $res );

    }

    /**
     * Verifica lo stato dell'iscrizione. Valori ritornati:
     * 0 - la configurazione è vuota
     * 1 - l'utente non è iscritto e la configurazione non è vuota
     * 2 - l'utente è già iscritto e rispetta la configurazione
     * 3 - l'utente è iscritto parzialmente o in modo diverso rispetto la configurazione
     * 4 - la configurazione su mailchimp è cambiata, ma la configurazione registrata su wp non è aggiornata
     *
     * @param $user_email
     * @param $user_role
     *
     * @since 1.0.0
     */
    public function check_subscription_status( $user_email, $user_role ) 
    {
        if (Synchro_MailChimp_Log_Service::is_enabled() ) {
            $this->log->debug("Checking subscription status [ user e-mail :: $user_email ][ user role :: $user_role ]...");
        }
        
        // Estrazione parametri configurazione
        $smc = $this->configuration->get_by_role($user_role);

        if (Synchro_MailChimp_Log_Service::is_enabled() ) {
                $c = count($smc);
                $this->log->debug("Checking configuration [ count smc :: $c ][ user role :: $user_role ]");
        } 

        // Estrazione List associate all'utente e verifica allineamento rispetto la configurazione
        $args['email']  = $user_email;

        try {

            $res_user_lists = $this->api->get_lists($args);

            $num_list_mailchimp = count((array) $res_user_lists);
            $num_list_config    = count($smc);

            if ($num_list_config != 0 && $num_list_mailchimp == 0 ) {
                return ( 1 );
            } //unchecked

            if (Synchro_MailChimp_Log_Service::is_enabled() ) {
                $this->log->debug("Check Subscription Status [ num_list_config :: $num_list_config ]");  
            }

            if ($num_list_config == 0 ) {
                return ( 0 );
            } //unchecked

            if ($num_list_config != 0 && $num_list_mailchimp != 0 ) {

                //Controllo se il numero di liste associate in configurazione e su Mailchimp è uguale
                if ($num_list_config == $num_list_mailchimp ) {

                    foreach ( $res_user_lists as $list ) {

                        //Verifico che gli id lista coincidano con la configurazione
                        if (array_key_exists($list->id, $smc) ) {

                            //Estrazione interests da Mailchimp
                            $res_user_list_interests = $this->api->get_list_member($list->id, $user_email);

                            if (isset($res_user_list_interests->interests)) {
                                $interest_ids = (array) $res_user_list_interests->interests;

                                foreach ( $interest_ids as $key => $value ) {
                                    if (!isset($smc[ $list->id ][ $key ])){
                                        return ( 4 );
                                    }
                                    if ($smc[ $list->id ][ $key ] !== $value) 
                                    {
                                        return ( 3 );
                                    }
                                }
                            }

                        } else {
                            //una lista su mailchimp non è presente nella configurazione locale
                            return ( 3 );
                        }
                    }

                } else {
                    return ( 3 );
                }

                return ( 2 );
            }

        } 
        catch (MC4WP_API_Connection_Exception $e) {

                if (Synchro_MailChimp_Log_Service::is_enabled() ) {
                    
                    $message = $e->getMessage();
                    $code = $e->getCode();
                    $this->log->debug("Check Subscription Status: MC4WP_API_Connection_Exception [ message :: $message ] [ code :: $code]");
                
                }
                throw new Exception(__("Connection failure. $message",'synchro_mailchimp'));
            
        }
        catch (MC4WP_API_Resource_Not_Found_Exception $e) {
        
            if (Synchro_MailChimp_Log_Service::is_enabled() ) {
                
                $message = $e->getMessage();
                $code = $e->getCode();
                $this->log->debug("Check Subscription Status: MC4WP_API_Resource_Not_Found_Exception [ message :: $message ] [ code :: $code]");
            
            }
            throw new Exception(__('Resource not found.','synchro_mailchimp'));
        
        }
        catch (MC4WP_API_Exception $e) {
        
            if (Synchro_MailChimp_Log_Service::is_enabled() ) {

                $message = $e->getMessage();
                $code = $e->getCode();
                $this->log->debug("Check Subscription Status: MC4WP_API_Exception [ message :: $message ] [ code :: $code]");
            
            }
            throw new Exception(__('Connection API error.','synchro_mailchimp'));
        
        }
        catch (Exception $e) {
        
            if (Synchro_MailChimp_Log_Service::is_enabled() ) {

                $message = $e->getMessage();
                $code = $e->getCode();
                $this->log->debug("Check Subscription Status: Exception [ message :: $message ] [ code :: $code]");

            }
            throw new Exception(__('Generic error.','synchro_mailchimp'));
        }
    }

    /**
     * Eseguo l'iscrizione dell'utente.
     *
     * @param $user_email
     *
     * @since 1.0.0
     */
    public function subscribe_user( $user_email ) 
    {
        if (Synchro_MailChimp_Log_Service::is_enabled() ) {
            $this->log->debug("Subscribing user [ user e-mail :: $user_email ]...");
        }

        $args['email_address'] = $user_email;
        $args['status']        = 'subscribed';

        // Get the user id.
        $user = get_user_by('email', $user_email);

        $lists = array();
        $lists = apply_filters('sm_user_list', $lists, $user->ID);

        foreach ( $lists as $list_id => $interests ) {

            $args['interests'] = apply_filters('sm_user_list_interests', $interests, $user->ID, $list_id);

            /**
             * Call the `sm_merge_fields` filter to allow 3rd parties to preprocess the `merge_fields` before the
             * request to MailChimp.
             *
             * @since 1.0.0
             *
             * @api
             *
             * @param array array() An empty array of merge fields.
             * @param string $user_email The user's e-mail address.
             * @param string $list_id The MailChimp list's id.
             * @param array  $interests An array of interests' ids.
             * @param array  $configuration The Synchro_Mailchimp configuration's array.
             */
            $args['merge_fields'] = apply_filters('sm_merge_fields', array(), $user_email, $list_id, $interests);

            try  {

                $add_status = $this->api->add_list_member($list_id, $args);
           
            } catch (MC4WP_API_Connection_Exception $e) {

                if (Synchro_MailChimp_Log_Service::is_enabled() ) {
                    
                    $message = $e->getMessage();
                    $code = $e->getCode();
                    $this->log->debug("Subscribing user: MC4WP_API_Connection_Exception [ message :: $message ] [ code :: $code]");
                
                }
                throw new Exception(__("Connection problem. $message",'synchro_mailchimp'));
            
            }
            catch (MC4WP_API_Resource_Not_Found_Exception $e) {
            
                if (Synchro_MailChimp_Log_Service::is_enabled() ) {
                    
                    $message = $e->getMessage();
                    $code = $e->getCode();
                    $this->log->debug("Subscribing user: MC4WP_API_Resource_Not_Found_Exception [ message :: $message ] [ code :: $code]");
                
                }
                throw new Exception(__('Resource not found in subscring user.','synchro_mailchimp'));
            
            }
            catch (MC4WP_API_Exception $e) {
            
                if (Synchro_MailChimp_Log_Service::is_enabled() ) {

                    $message = $e->getMessage();
                    $code = $e->getCode();
                    $this->log->debug("Subscribing user: MC4WP_API_Exception [ message :: $message ] [ code :: $code]");
                
                }
                throw new Exception(__('Subscribing user: API error. MailChimp message: '.$e->detail,'synchro_mailchimp'));
            
            }
            catch (Exception $e) {
            
                if (Synchro_MailChimp_Log_Service::is_enabled() ) {

                    $message = $e->getMessage();
                    $code = $e->getCode();
                    $this->log->debug("Subscribing user: Exception [ message :: $message ] [ code :: $code]");

                }
                throw new Exception(__('Subscribing user: generic error.','synchro_mailchimp'));
            
            }

            
            if (Synchro_MailChimp_Log_Service::is_enabled() ) {
                $this->log->trace("Call to `add_list_member` returned [ " . var_export($add_status, true) . " ]");
            }

        }

        return ( true );
    }

    /**
     * Elimino l'iscrizione basandomi sullo stato della configurazione locale.
     *
     * @param $user_email
     *
     * @since 1.0.0
     */
    public function unsubscribe_user_config( $user_email ) 
    {

        if (Synchro_MailChimp_Log_Service::is_enabled() ) {
            $this->log->debug("Unsubscribing user config [ user e-mail :: $user_email ]");
        }

        // Get the user id.
        $user = get_user_by('email', $user_email);

        $lists = array();
        $lists = apply_filters('sm_user_list', $lists, $user->ID);

        if (Synchro_MailChimp_Log_Service::is_enabled() ) {
            $c = count($lists);
            $this->log->debug("Unsubscribing user config [ lists after apply filter :: $c ]");
        }

        $reset_args['email'] = $user_email;

        foreach ( $lists as $list_id => $interests ) {
            
            try {

                $reset_status = $this->api->delete_list_member($list_id, $user_email);
            
            } catch (MC4WP_API_Connection_Exception $e) {

                if (Synchro_MailChimp_Log_Service::is_enabled() ) {
                    
                    $message = $e->getMessage();
                    $code = $e->getCode();
                    $this->log->debug("Unsubscribe User Config: MC4WP_API_Connection_Exception [ message :: $message ] [ code :: $code]");
                
                }
                throw new Exception(__("Problema di connessione. $message",'synchro_mailchimp'));
            
            }
            catch (MC4WP_API_Resource_Not_Found_Exception $e) {
            
                if (Synchro_MailChimp_Log_Service::is_enabled() ) {
                    
                    $message = $e->getMessage();
                    $code = $e->getCode();
                    $this->log->debug("Unsubscribe User Config: MC4WP_API_Resource_Not_Found_Exception [ message :: $message ] [ code :: $code]");
                
                }
                throw new Exception(__('Risorsa not trovata.','synchro_mailchimp'));
            
            }
            catch (MC4WP_API_Exception $e) {
            
                if (Synchro_MailChimp_Log_Service::is_enabled() ) {

                    $message = $e->getMessage();
                    $code = $e->getCode();
                    $this->log->debug("Unsubscribe User Config: MC4WP_API_Exception [ message :: $message ] [ code :: $code]");
                
                }
                throw new Exception(__('Errore nelle API di connessione.','synchro_mailchimp'));
            
            }
            catch (Exception $e) {
            
                if (Synchro_MailChimp_Log_Service::is_enabled() ) {

                    $message = $e->getMessage();
                    $code = $e->getCode();
                    $this->log->debug("Unsubscribe User Config: Exception [ message :: $message ] [ code :: $code]");

                }
                throw new Exception(__('Errore generico.','synchro_mailchimp'));
            
            }

        }

        return ( true );
    }

    /**
     * Elimino l'iscrizione basandomi sullo stato di configurazione di mailchimp.
     *
     * @param $user_email
     *
     * @since 1.0.0
     */
    public function unsubscribe_user_mailchimp( $user_email ) 
    {

        // Reset iscrizione incompleta
        $reset_args['email'] = $user_email;

        try {
            
            $res_user_lists      = $this->api->get_lists($reset_args);

            foreach ( $res_user_lists as $list ) {
                $reset_status = $this->api->delete_list_member($list->id, $user_email);
            }

        } catch (MC4WP_API_Connection_Exception $e) {

                if (Synchro_MailChimp_Log_Service::is_enabled() ) {
                    
                    $message = $e->getMessage();
                    $code = $e->getCode();
                    $this->log->debug("Unsubscribe User MailChimp: MC4WP_API_Connection_Exception [ message :: $message ] [ code :: $code]");
                
                }
                throw new Exception(__("Problema di connessione. $message",'synchro_mailchimp'));
            
            }
            catch (MC4WP_API_Resource_Not_Found_Exception $e) {
            
                if (Synchro_MailChimp_Log_Service::is_enabled() ) {
                    
                    $message = $e->getMessage();
                    $code = $e->getCode();
                    $this->log->debug("Unsubscribe User MailChimp: MC4WP_API_Resource_Not_Found_Exception [ message :: $message ] [ code :: $code]");
                
                }
                throw new Exception(__('Risorsa not trovata.','synchro_mailchimp'));
            
            }
            catch (MC4WP_API_Exception $e) {
            
                if (Synchro_MailChimp_Log_Service::is_enabled() ) {

                    $message = $e->getMessage();
                    $code = $e->getCode();
                    $this->log->debug("Unsubscribe User MailChimp: MC4WP_API_Exception [ message :: $message ] [ code :: $code]");
                
                }
                throw new Exception(__('Errore nelle API di connessione.','synchro_mailchimp'));
            
            }
            catch (Exception $e) {
            
                if (Synchro_MailChimp_Log_Service::is_enabled() ) {

                    $message = $e->getMessage();
                    $code = $e->getCode();
                    $this->log->debug("Unsubscribe User MailChimp: Exception [ message :: $message ] [ code :: $code]");

                }
                throw new Exception(__('Errore generico.','synchro_mailchimp'));
            
            }

        return ( true );
    }
}