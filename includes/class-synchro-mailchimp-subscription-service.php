<?php
/**
 * The MailChimp subscription service.
 *
 * @link
 * @since 1.0
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
class Synchro_Mailchimp_Subscription_Service {
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
	 * @var object $api Handler for the MailChimp api, a {@link Synchro_MailChimp_Api_Service} instance.
	 */
	public $api;

	/**
	 * Plugin Settings.
	 *
	 * @since  1.0
	 * @access private
	 * @var object $configuration Handler for the MailChimp api, a {@link Synchro_Mailchimp_Configuration_Service} instance.
	 */
	private $configuration;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 1.0
	 */
	public function __construct() {

		$this->log = Synchro_MailChimp_Log_Service::create( 'Synchro_Mailchimp_Subscription_Service' );
		$this->api = new Synchro_Mailchimp_Api_Service();
		$this->configuration = new Synchro_Mailchimp_Configuration_Service();

	}

	/**
	 * Configuration set method.
	 *
	 * @param object $configuration The configuration object.
	 * @since 1.0
	 * @access public
	 */
	public function set_configuration( $configuration ) {
		$this->configuration = $configuration;
	}

	/**
	 * Implements the subscription process.
	 *
	 * @param int    $subscription_status Code describing the status of the configuration for the user.
	 * @param string $user_email Email of the user.
	 * @param string $user_role Role of the user.
	 *
	 * @since 1.0
	 */
	public function subscribe_process( $subscription_status, $user_email, $user_role ) {

		$this->log->debug( "Subscribing [ subscription status :: $subscription_status ][ user e-mail :: $user_email ][ user role :: $user_role ]..." );

		$res = false;

		switch ( $subscription_status ) {
			case 0:
				// Empty configuration: nothing to do.
				break;
			case 1:
				// Proceed to subscribe.
				$res = $this->subscribe_user( $user_email );

				break;
			case 2:
				// User already subscribed.
				break;
			case 3:
				// User partially subscribed or subscribed unlike the configuration.
				// Reset the partial subscription.
				if ( $this->unsubscribe_user_mailchimp( $user_email ) ) {
					// Proceed with subscription according to the configuration.
					$res = $this->subscribe_user( $user_email );
				}

				break;

			default:
				break;
		}

		$this->log->info( "Subscribed [ subscription status :: $subscription_status ][ user e-mail :: $user_email ][ user role :: $user_role ]." );

		return ( $res );

	}

	/**
	 * Implements the unsubscription process.
	 *
	 * @param int    $subscription_status Code describing the status of the configuration for the user.
	 * @param string $user_email Email of the user.
	 * @param string $user_role Role of the user.
	 *
	 * @since 1.0
	 */
	public function unsubscribe_process( $subscription_status, $user_email, $user_role ) {

		$res = false;

		switch ( $subscription_status ) {
			case 0:
				// Empty configuration: nothing to do.
				break;
			case 1:
				// Unsubscribed user: nothing to do.
				break;
			case 2:
				// User already subscribed according to the configuration.
				$res = $this->unsubscribe_user_config( $user_email );
				break;
			case 3:
				// User partially subscribed or subscribed unlike the configuration.
				$res = $this->unsubscribe_user_mailchimp( $user_email );

				break;
			default:
				break;
		}

		return ( $res );

	}

	/**
	 * Verify the subscription status returning a integer that define the status.
	 *
	 * @param string $user_email The user email.
	 * @param string $user_role The user role.
	 * @return int Staus code. {
	 *                             0 - Empty configuration found.
	 *                             1 - User not subscribed and configuration not empty.
	 *                             2 - User already subscribed according to the configuration.
	 *                             3 - User partially subscribed or subscribed unlike the configuration.
	 *                             4 - MailChimp configuration has changed, but the configuration setting on WordPress is not updated.
	 *                          }.
	 * @throws Exception If there are connection issues or generic problems in checking the status.
	 * @access public
	 * @since 1.0
	 */
	public function check_subscription_status( $user_email, $user_role ) {

		$this->log->debug( "Checking subscription status [ user e-mail :: $user_email ][ user role :: $user_role ]..." );

		// Extract configuration parameters.
		$smc = $this->configuration->get_by_role( $user_role );

		$c = count( $smc );
		$this->log->debug( "Checking configuration [ count smc :: $c ][ user role :: $user_role ]" );

		// User lists extraction and verifies alignment with configuration.
		$args['email']  = $user_email;

		try {

			$res_user_lists = $this->api->get_lists( $args );

			$num_list_mailchimp = count( (array) $res_user_lists );
			$num_list_config    = count( $smc );

			if ( 0 != $num_list_config && 0 == $num_list_mailchimp ) {
				return ( 1 );
			} // Unchecked.

			$this->log->debug( "Check Subscription Status [ num_list_config :: $num_list_config ]" );

			if ( 0 == $num_list_config ) {
				return ( 0 );
			} //unchecked

			if ( 0 != $num_list_config && 0 != $num_list_mailchimp ) {

				// Check if the number of linked lists in configuration and on MailChimp is the same.
				if ( $num_list_config == $num_list_mailchimp ) {

					foreach ( $res_user_lists as $list ) {

						// Verify that the list ids coincide with the configuration.
						if ( array_key_exists( $list->id, $smc ) ) {

							// Interests extraction from MailChimp.
							$res_user_list_interests = $this->api->get_list_member( $list->id, $user_email );

							if ( isset( $res_user_list_interests->interests ) ) {
								$interest_ids = (array) $res_user_list_interests->interests;

								foreach ( $interest_ids as $key => $value ) {
									if ( ! isset( $smc[ $list->id ][ $key ] ) ) {
										return ( 4 );
									}
									if ( $smc[ $list->id ][ $key ] !== $value ) {
										return ( 3 );
									}
								}
							}
						} else {
							// A list on mailchimp is not present in the local configuration.
							return ( 3 );
						}
					}
				} else {
					return ( 3 );
				}

				return ( 2 );
			}
		} catch ( MC4WP_API_Connection_Exception $e ) {

			$message = $e->getMessage();
			$code = $e->getCode();
			$this->log->debug( "Check Subscription Status: MC4WP_API_Connection_Exception [ message :: $message ] [ code :: $code]" );

			$exep_message = __( 'Connection failure.','synchro_mailchimp' );
			throw new Exception( $exep_message . ' ' . $message );

		} catch ( MC4WP_API_Resource_Not_Found_Exception $e ) {

			$message = $e->getMessage();
			$code = $e->getCode();
			$this->log->debug( "Check Subscription Status: MC4WP_API_Resource_Not_Found_Exception [ message :: $message ] [ code :: $code]" );

			throw new Exception( __( 'Resource not found.','synchro_mailchimp' ) );

		} catch ( MC4WP_API_Exception $e ) {

			$message = $e->getMessage();
			$code = $e->getCode();
			$this->log->debug( "Check Subscription Status: MC4WP_API_Exception [ message :: $message ] [ code :: $code]" );

			throw new Exception( __( 'Connection API error.','synchro_mailchimp' ) );

		} catch ( Exception $e ) {

			$message = $e->getMessage();
			$code = $e->getCode();
			$this->log->debug( "Check Subscription Status: Exception [ message :: $message ] [ code :: $code]" );

			throw new Exception( __( 'Generic error.','synchro_mailchimp' ) );
		}
	}

	/**
	 * Subscribe the user.
	 *
	 * @param string $user_email The email of the user.
	 * @throws Exception If there are connection issues or generic problems in subscribing the user.
	 * @return bool True if the process end without errors.
	 * @since 1.0
	 * @access public
	 */
	public function subscribe_user( $user_email ) {

		$this->log->debug( "Subscribing user [ user e-mail :: $user_email ]..." );

		$args['email_address'] = $user_email;
		$args['status']        = 'subscribed';

		// Get the user id.
		$user = get_user_by( 'email', $user_email );

		$lists = array();
		$lists = apply_filters( 'sm_user_list', $lists, $user->ID );

		foreach ( $lists as $list_id => $interests ) {

			$args['interests'] = apply_filters( 'sm_user_list_interests', $interests, $user->ID, $list_id );

			/**
			 * Call the `sm_merge_fields` filter to allow 3rd parties to preprocess the `merge_fields` before the
			 * request to MailChimp.
			 *
			 * @since 1.0
			 *
			 * @api
			 *
			 * @param array array() An empty array of merge fields.
			 * @param string $user_email The user's e-mail address.
			 * @param string $list_id The MailChimp list's id.
			 * @param array  $interests An array of interests' ids.
			 * @param array  $configuration The Synchro_Mailchimp configuration's array.
			 */
			$args['merge_fields'] = apply_filters( 'sm_merge_fields', array(), $user_email, $list_id, $interests );

			try {

				$add_status = $this->api->add_list_member( $list_id, $args );

			} catch ( MC4WP_API_Connection_Exception $e ) {
				$message = $e->getMessage();
				$code = $e->getCode();

				$this->log->debug( "Subscribing user: MC4WP_API_Connection_Exception [ message :: $message ] [ code :: $code]" );

				$excep_message = __( 'Connection problem.','synchro_mailchimp' );
				throw new Exception( $excep_message . ' ' . $message );

			} catch ( MC4WP_API_Resource_Not_Found_Exception $e ) {
				$message = $e->getMessage();
				$code = $e->getCode();
				$this->log->debug( "Subscribing user: MC4WP_API_Resource_Not_Found_Exception [ message :: $message ] [ code :: $code]" );

				throw new Exception( __( 'Resource not found in subscribing user.','synchro_mailchimp' ) );

			} catch ( MC4WP_API_Exception $e ) {
				$message = $e->getMessage();
				$code = $e->getCode();
				$this->log->debug( "Subscribing user: MC4WP_API_Exception [ message :: $message ] [ code :: $code]" );

				$excep_message = __( 'Subscribing user: API error. MailChimp message:','synchro_mailchimp' );
				throw new Exception( $excep_message . ' ' . $e->detail );

			} catch ( Exception $e ) {
				$message = $e->getMessage();
				$code = $e->getCode();
				$this->log->debug( "Subscribing user: Exception [ message :: $message ] [ code :: $code]" );

				throw new Exception( __( 'Subscribing user: generic error.','synchro_mailchimp' ) );

			}

			$this->log->trace( 'Call to `add_list_member` returned [ ' . var_export( $add_status, true ) . ' ]' );
		}

		return ( true );
	}

	/**
	 * Delete the subscription according to the local configuration
	 *
	 * @param string $user_email The user email.
	 * @throws Exception If there are connection issues or generic problems in unsubscribing the user.
	 * @access public
	 * @since 1.0
	 */
	public function unsubscribe_user_config( $user_email ) {

		$this->log->debug( "Unsubscribing user config [ user e-mail :: $user_email ]" );

		// Get the user id.
		$user = get_user_by( 'email', $user_email );

		$lists = array();
		$lists = apply_filters( 'sm_user_list', $lists, $user->ID );

		$c = count( $lists );
		$this->log->debug( "Unsubscribing user config [ lists after apply filter :: $c ]" );

		$reset_args['email'] = $user_email;

		foreach ( $lists as $list_id => $interests ) {

			try {

				$reset_status = $this->api->delete_list_member( $list_id, $user_email );

			} catch ( MC4WP_API_Connection_Exception $e ) {

				$message = $e->getMessage();
				$code = $e->getCode();
				$this->log->debug( "Unsubscribe User Config: MC4WP_API_Connection_Exception [ message :: $message ] [ code :: $code]" );

				$excep_message = __( 'Connection problem.','synchro_mailchimp' );
				throw new Exception( $excep_message . ' ' . $message );

			} catch ( MC4WP_API_Resource_Not_Found_Exception $e ) {

				$message = $e->getMessage();
				$code = $e->getCode();
				$this->log->debug( "Unsubscribe User Config: MC4WP_API_Resource_Not_Found_Exception [ message :: $message ] [ code :: $code]" );

				throw new Exception( __( 'Resource not found.','synchro_mailchimp' ) );

			} catch ( MC4WP_API_Exception $e ) {

				$message = $e->getMessage();
				$code = $e->getCode();
				$this->log->debug( "Unsubscribe User Config: MC4WP_API_Exception [ message :: $message ] [ code :: $code]" );

				throw new Exception( __( 'API connection error.','synchro_mailchimp' ) );

			} catch ( Exception $e ) {

				$message = $e->getMessage();
				$code = $e->getCode();
				$this->log->debug( "Unsubscribe User Config: Exception [ message :: $message ] [ code :: $code]" );

				throw new Exception( __( 'Generic error.','synchro_mailchimp' ) );

			}
		}

		return ( true );
	}

	/**
	 * Delete the subscription according to the MailChimp configuration status.
	 *
	 * @param string $user_email The user email.
	 * @throws Exception If there are connection issues or generic problems in unsubscribing the user.
	 * @access public
	 * @since 1.0
	 */
	public function unsubscribe_user_mailchimp( $user_email ) {

		// Reset incomplete subscription.
		$reset_args['email'] = $user_email;

		try {
			$res_user_lists      = $this->api->get_lists( $reset_args );

			foreach ( $res_user_lists as $list ) {
				$reset_status = $this->api->delete_list_member( $list->id, $user_email );
			}
		} catch ( MC4WP_API_Connection_Exception $e ) {

			$message = $e->getMessage();
			$code = $e->getCode();
			$this->log->debug( "Unsubscribe User MailChimp: MC4WP_API_Connection_Exception [ message :: $message ] [ code :: $code]" );

			$excep_message = __( 'Connection problem.','synchro_mailchimp' );
			throw new Exception( $excep_message . ' ' . $message );

		} catch ( MC4WP_API_Resource_Not_Found_Exception $e ) {

			$message = $e->getMessage();
			$code = $e->getCode();
			$this->log->debug( "Unsubscribe User MailChimp: MC4WP_API_Resource_Not_Found_Exception [ message :: $message ] [ code :: $code]" );

			throw new Exception( __( 'Resource not found.','synchro_mailchimp' ) );

		} catch ( MC4WP_API_Exception $e ) {

			$message = $e->getMessage();
			$code = $e->getCode();
			$this->log->debug( "Unsubscribe User MailChimp: MC4WP_API_Exception [ message :: $message ] [ code :: $code]" );

			throw new Exception( __( 'API connection error.','synchro_mailchimp' ) );

		} catch ( Exception $e ) {

			$message = $e->getMessage();
			$code = $e->getCode();
			$this->log->debug( "Unsubscribe User MailChimp: Exception [ message :: $message ] [ code :: $code]" );

			throw new Exception( __( 'Generic error.','synchro_mailchimp' ) );

		}

		return ( true );
	}
}
