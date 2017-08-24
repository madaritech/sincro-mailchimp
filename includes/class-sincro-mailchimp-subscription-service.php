<?php

/**
 * The MailChimp subscription service.
 *
 * @link
 * @since      1.0.0
 *
 * @package    Sincro_Mailchimp
 * @subpackage Sincro_Mailchimp/includes
 */

/**
 * The core subscription functionality of the plugin.
 *
 * @package    Sincro_Mailchimp
 * @subpackage Sincro_Mailchimp/includes
 * @author     Madaritech <dm@madaritech.com>
 */
class Sincro_Mailchimp_Subscription_Service {

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
	 * @since    1.0.0
	 * @access   public
	 */
	public $api;

		/**
	 * Configurazione Plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 */
	private $smc;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->log = Sincro_MailChimp_Log_Service::create( 'Sincro_Mailchimp_Subscription_Service' );
		$this->api = new Sincro_Mailchimp_Api_Service();

	}

	/**
	 * Richiama la configurazione del plugin relativa ad un certo ruolo.
	 *
	 * @since    1.0.0
	 * @access   protected
	 *
	 * @param    string $user_role Ruolo dell'utente.
	 */
	protected function get_config_role( $user_role ) {
		$this->smc = unserialize( SINCRO_MAILCHIMP_CONFIG );

		return ( $this->smc[ $user_role ] );
	}

	/**
	 * Implementa la logica del processo di sottoscrizione.
	 *
	 * @param    $subscription_status
	 * @param    $user_email
	 * @param    $user_role
	 *
	 * @since    1.0.0
	 */
	public function subscribe_process( $subscription_status, $user_email, $user_role ) {

		if ( Sincro_MailChimp_Log_Service::is_enabled() ) {
			$this->log->debug( "Subscribing [ subscription status :: $subscription_status ][ user e-mail :: $user_email ][ user role :: $user_role ]..." );
		}

		$res = false;

		switch ( $subscription_status ) {
			case 0:
				// Configurazione vuota: non eseguo nulla
				break;
			case 1:
				// Procedo con l'iscrizione

				// Estrazione parametri configurazione
				$smc = $this->get_config_role( $user_role );

				$res = $this->subscribe_user( $user_email, $smc );

				break;
			case 2:
				// Utente già iscritto correttamente
				break;
			case 3:
				// Utente iscritto parzialmente o in modo diverso rispetto la configurazione

				// Estrazione parametri configurazione
				$smc = $this->get_config_role( $user_role );

				//Reset iscrizione parziale
				if ( $this->unsubscribe_user_mailchimp( $user_email ) ) {
					// Procedo con iscrizione da configurazione
					$res = $this->subscribe_user( $user_email, $smc );
				}

				break;

			default:
				break;
		}

		if ( Sincro_MailChimp_Log_Service::is_enabled() ) {
			$this->log->info( "Subscribed [ subscription status :: $subscription_status ][ user e-mail :: $user_email ][ user role :: $user_role ]." );
		}

		return ( $res );

	}

	/**
	 * Implementa la logica del processo di cancellazione della sottoscrizione.
	 *
	 * @param    $subscription_status
	 * @param    $user_email
	 * @param    $user_role
	 *
	 * @since    1.0.0
	 */
	public function unsubscribe_process( $subscription_status, $user_email, $user_role ) {

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

				// Estrazione parametri configurazione
				$smc = $this->get_config_role( $user_role );

				$res = $this->unsubscribe_user_config( $user_email, $smc );

				break;
			case 3:
				// Utente iscritto parzialmente o in modo diverso rispetto la configurazione

				// Estrazione parametri configurazione
				$smc = $this->get_config_role( $user_role );

				$res = $this->unsubscribe_user_mailchimp( $user_email );

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
	 *
	 * @param    $user_email
	 * @param    $user_role
	 *
	 * @since    1.0.0
	 */
	public function check_subscription_status( $user_email, $user_role ) {

		// Estrazione parametri configurazione
		$smc = $this->get_config_role( $user_role );

		// Estrazione List associate all'utente e verifica allineamento rispetto la configurazione
		$args['email']  = $user_email;
		$res_user_lists = $this->api->get_lists( $args );

		$num_list_mailchimp = count( (array) $res_user_lists );
		$num_list_config    = count( $smc );

		if ( $num_list_config != 0 && $num_list_mailchimp == 0 ) {
			return ( 1 );
		} //unchecked

		if ( $num_list_config == 0 ) {
			return ( 0 );
		} //unchecked

		if ( $num_list_config != 0 && $num_list_mailchimp != 0 ) {

			//Controllo se il numero di liste associate in configurazione e su Mailchimp è uguale
			if ( $num_list_config == $num_list_mailchimp ) {

				foreach ( $res_user_lists as $list ) {

					//Verifico che gli id lista coincidano con la configurazione
					if ( array_key_exists( $list->id, $smc ) ) {

						//Estrazione interests da Mailchimp
						$res_user_list_interests = $this->api->get_list_member( $list->id, $user_email );

						$interest_ids = (array) $res_user_list_interests->interests;

						foreach ( $interest_ids as $key => $value ) {
							if ( $smc[ $list->id ][ $key ] !== $value ) {
								return ( 3 );
							}
						}

					} else {

						return ( 3 );
					}
				}

			} else {
				return ( 3 );
			}

			return ( 2 );
		}
	}

	/**
	 * Eseguo l'iscrizione dell'utente.
	 *
	 * @param    $user_email
	 * @param    $smc
	 *
	 * @since    1.0.0
	 */
	public function subscribe_user( $user_email, $smc ) {

		if ( Sincro_MailChimp_Log_Service::is_enabled() ) {
			$this->log->debug( "Subscribing user [ user e-mail :: $user_email ]..." );
		}

		$args['email_address'] = $user_email;
		$args['status']        = 'subscribed';

		// Get the user id.
		$user = get_user_by( 'email', $user_email );

		foreach ( $smc as $list_id => $interests ) {

			//$args['interests'] = $interests;
			$args['interests'] = apply_filters( 'sm_user_list_interests', $interests, $user->ID, $list_id );

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
			 * @param array  $smc The Sincro_Mailchimp configuration's array.
			 */
			$args['merge_fields'] = apply_filters( 'sm_merge_fields', array(), $user_email, $list_id, $interests, $smc );
			//$args['merge_fields'] = $this->sm_merge_fields->run(array(array(), $user_email, $list_id, $interests, $smc ) );
			
			//$add_status = $this->add_list_member( $list_id, $args );
			$add_status = $this->api->add_list_member( $list_id, $args );

			if ( Sincro_MailChimp_Log_Service::is_enabled() ) {
				$this->log->trace( "Call to `add_list_member` returned [ " . var_export( $add_status, true ) . " ]" );
			}

		}

		return ( true );
	}

	/**
	 * Elimino l'iscrizione basandomi sullo stato della configurazione locale.
	 *
	 * @param    $user_email
	 * @param    $smc
	 *
	 * @since    1.0.0
	 */
	public function unsubscribe_user_config( $user_email, $smc ) {

		$reset_args['email'] = $user_email;

		foreach ( $smc as $list_id => $interests ) {
			$reset_status = $this->api->delete_list_member( $list_id, $user_email );
		}

		return ( true );
	}

	/**
	 * Elimino l'iscrizione basandomi sullo stato di configurazione di mailchimp.
	 *
	 * @param    $user_email
	 *
	 * @since    1.0.0
	 */
	public function unsubscribe_user_mailchimp( $user_email ) {

		// Reset iscrizione incompleta
		$reset_args['email'] = $user_email;
		$res_user_lists      = $this->api->get_lists( $reset_args );

		foreach ( $res_user_lists as $list ) {
			$reset_status = $this->api->delete_list_member( $list->id, $user_email );
		}

		return ( true );
	}
}