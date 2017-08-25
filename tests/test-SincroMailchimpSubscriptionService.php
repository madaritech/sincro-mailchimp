<?php
/**
 * Class SincroMailchimpSubscriptionServiceTest
 *
 * @package Sincro_Mailchimp
 */

/**
 * Sincro_Mailchimp_Admin Class test case.
 */
class SincroMailchimpSubscriptionServiceTest extends WP_UnitTestCase
{

    private $emailRoleStack;
    private $lists;
    private $interests;
    private $email_test;
    private $config;
    public $dummy_api;
 
    public function setUp()
    {
        $test_user = $this->factory->user->create_and_get();
        $this->email_test = $test_user->user_email;

        $this->emailRoleStack = array(
                                         $this->factory->user->create_and_get(array( 'role' => 'administrator' )),
                                         $this->factory->user->create_and_get(array( 'role' => 'editor' )),
                                         $this->factory->user->create_and_get(array( 'role' => 'author' )),
                                         $this->factory->user->create_and_get(array( 'role' => 'contributor' )),
                                         $this->factory->user->create_and_get(array( 'role' => 'subscriber' )),
                                     );

        $lists['acme'] = 'e87b1536bb';
        $lists['test'] = '060a231f4f';
        $interests['group1']         = 'dafaf73c29';
        $interests['group2']         = 'ab364ca78d';
        $interests['group3']         = '2bec2ca41d';
        $interests['acme-group1']     = '27e89ce542';
        $interests['acme-group2']     = '9e05bcd6ce';
        $interests['acme-group3']     = 'cf5965131a';
        $interests['acme-group4']     = '7cd76ab7e9';

        $scm_config = array('administrator' => array($lists['test'] => array($interests['group1'] => false,
                                                                       $interests['group2'] => true,
                                                                       $interests['group3'] => true)),

                             'editor'          => array($lists['acme'] => array($interests['acme-group1'] => true, 
                                                                             $interests['acme-group2'] => false, 
                                                                             $interests['acme-group3'] => true, 
                                                                             $interests['acme-group4'] => false)),
                             'author'          => array(),

                             'contributor'   => array($lists['test'] => array($interests['group1'] => true,
                                                                               $interests['group2'] => false,
                                                                               $interests['group3'] => true)),

                             'subscriber'      => array($lists['acme'] => array($interests['acme-group1'] => true, 
                                                                             $interests['acme-group2'] => false, 
                                                                             $interests['acme-group3'] => true, 
                                                                             $interests['acme-group4'] => true), 
                                                        $lists['test'] => array($interests['group1'] => false,
                                                                               $interests['group2'] => true,
                                                                               $interests['group3'] => false)));

        $ser_scm_config = serialize($scm_config);
        $this->config = unserialize($ser_scm_config);

        $this->lists = $lists;
        $this->interests = $interests;

        $this->smss_stub = $this->getMockBuilder('Sincro_Mailchimp_Subscription_Service')
            ->setConstructorArgs(array())
            ->setMethods(array( 'get_config_role' ))
            ->getMock();

        $this->dummy_api = $this->getMockBuilder('Sincro_Mailchimp_Api_Service')
            ->setConstructorArgs(array())
            ->setMethods(array( 'get_lists', 'get_list_member', 'add_list_member', 'delete_list_member' ))
            ->getMock();

        //$this->wp_error_response = new \WP_Error( 100, 'Issue with API' );
    }

    /**
     * subscribe_user test.
     */
    public function test_subscribe_user() 
    {

        $smss_obj = new Sincro_Mailchimp_Subscription_Service();

        $this->dummy_api->expects($this->any())
            ->method('add_list_member')
            ->willReturn(true);

        $smss_obj->api = $this->dummy_api;

        foreach ($this->emailRoleStack as $user) {
            $smc = $this->config[$user->roles[0]];
            $res = $smss_obj->subscribe_user($user->user_email, $smc);

            $this->assertEquals($res, true);
        }
    }

    /**
     * unsubscribe_user_config test.
     */
    public function test_unsubscribe_user_config() 
    {

        $smss_obj = new Sincro_Mailchimp_Subscription_Service();

        $this->dummy_api->expects($this->any())
            ->method('delete_list_member')
            ->willReturn(true);

        $smss_obj->api = $this->dummy_api;
        
        foreach ($this->emailRoleStack as $user) {
            $smc = $this->config[$user->roles[0]];
            $res = $smss_obj->unsubscribe_user_config($user->user_email, $smc);
            
            $this->assertEquals($res, true);
        }
    }

    /**
     * unsubscribe_user_mailchimp test.
     */
    public function test_unsubscribe_user_mailchimp() 
    {

        $smss_obj = new Sincro_Mailchimp_Subscription_Service();

        $this->dummy_api->expects($this->any())
            ->method('delete_list_member')
            ->willReturn(true);

        $obj0 = new stdClass();
        $obj0->id = $this->lists['acme'];

        $obj1 = new stdClass();
        $obj1->id = $this->lists['test'];

        $stub_list = array();
        $stub_list[0] = $obj0;
        $stub_list[1] = $obj1;

        $this->dummy_api->expects($this->any())
            ->method('get_lists')
            ->willReturn($stub_list);

        $smss_obj->api = $this->dummy_api;

        $email = $this->emailRoleStack[0]->user_email;
        $res = $smss_obj->unsubscribe_user_mailchimp($email);

        $this->assertEquals($res, true);

    }

    /**
     *    check_subscription_status
     *
     *  @dataProvider roleProvider
     */
    public function test_check_subscription_status($role) 
    {

        $obj0 = new stdClass();
        $obj0->id = $this->lists['test'];

        $stub_list = array(0 => $obj0);
        $stub_interests = new stdClass();
        $stub_interests->interests = array($this->interests['group1'] => true,
                                            $this->interests['group2'] => false,
                                            $this->interests['group3'] => true);

        //Stub list restituita su mailchimp -> strutture associate al contributor
        $this->dummy_api->expects($this->any())
            ->method('get_lists')
            ->willReturn($stub_list);
        
        $this->dummy_api->expects($this->any())
            ->method('get_list_member')
            ->willReturn($stub_interests);

        //Stub ruolo utente in verifica
        $this->smss_stub->expects($this->any())
            ->method('get_config_role')
            ->willReturn($this->config[$role]);

        $this->smss_stub->api = $this->dummy_api;

        $res = $this->smss_stub->check_subscription_status($this->email_test, $role);

        switch ($role) {
        case 'administrator':
            $this->assertEquals($res, 3);
            break;
        case 'editor':
            $this->assertEquals($res, 3);
            break;
        case 'author':
            $this->assertEquals($res, 0);
            break;
        case 'contributor':
            $this->assertEquals($res, 2);
            break;
        case 'subscriber':
            $this->assertEquals($res, 3);
            break;
        default:
            // code...
            break;
        }

    }

    public function roleProvider()
    {
        return [ 
        ['administrator'],
        ['editor'],
        ['author'],
        ['contributor'],
        ['subscriber']
        ];
    }

    /**
     *    check_subscription_status test
     *
     *  Utente non iscritto e configurazione non vuota
     */
    public function test_check_subscription_status_not_empty() 
    {        

        //Stub list restituita su mailchimp -> vuota
        $this->dummy_api->expects($this->any())
            ->method('get_lists')
            ->willReturn(array());

        //Stub ruolo utente in verifica
        $this->smss_stub->expects($this->any())
            ->method('get_config_role')
            ->willReturn($this->config['editor']);

        $this->smss_stub->api = $this->dummy_api;

        $res = $this->smss_stub->check_subscription_status($this->email_test, 'editor');

        $this->assertEquals($res, 1);
        
    }

    public function unsubscribeProcessDataProvider()
    {
        return [
        [3, $this->factory->user->create_and_get(array( 'role' => 'administrator' ))->user_email, 'administrator', true],
        [1, $this->factory->user->create_and_get(array( 'role' => 'editor' ))->user_email, 'editor', false],
        [0, $this->factory->user->create_and_get(array( 'role' => 'author' ))->user_email, 'author', false],
        [3, $this->factory->user->create_and_get(array( 'role' => 'contributor' ))->user_email, 'contributor', true],
        [2, $this->factory->user->create_and_get(array( 'role' => 'subscriber' ))->user_email, 'subscriber', true]
        ];
    }

    /**
     *  unsubscribe_process test
     *
     *  @dataProvider unsubscribeProcessDataProvider
     */
    public function test_unsubscribe_process($subscription_status, $user_email, $user_role, $res) 
    {

        //Stub ruolo utente
        $this->smss_stub->expects($this->any())
            ->method('get_config_role')
            ->willReturn($this->config['subscriber']);

        //Stub eliminazione utente dalle liste mailchimp 
        $this->dummy_api->expects($this->any())
            ->method('delete_list_member')
            ->willReturn(true);

        //Stub list di mailchimp
        $this->dummy_api->expects($this->any())
            ->method('get_lists')
            ->willReturn(array());

        $this->smss_stub->api = $this->dummy_api;

        $result = $this->smss_stub->unsubscribe_process($subscription_status, $user_email, $user_role);
    
        $this->assertEquals($result, $res);
    }


    public function subscribeProcessDataProvider()
    {
        return [ 
        [3, $this->factory->user->create_and_get(array( 'role' => 'administrator' ))->user_email, 'administrator', true],
        [1, $this->factory->user->create_and_get(array( 'role' => 'editor' ))->user_email, 'editor', true],
        [0, $this->factory->user->create_and_get(array( 'role' => 'author' ))->user_email, 'author', false],
        [3, $this->factory->user->create_and_get(array( 'role' => 'contributor' ))->user_email, 'contributor', true],
        [2, $this->factory->user->create_and_get(array( 'role' => 'subscriber' ))->user_email, 'subscriber', false]
        ];
    }

    /**
     *    subscribe_process test
     *
     *  @dataProvider subscribeProcessDataProvider
     */
    public function test_subscribe_process($subscription_status, $user_email, $user_role, $res) 
    {

        //Stub ruolo utente
        $this->smss_stub->expects($this->any())
            ->method('get_config_role')
            ->willReturn($this->config['subscriber']);

        //Stub eliminazione utente dalle liste mailchimp
        $this->dummy_api->expects($this->any())
            ->method('delete_list_member')
            ->willReturn(true);

        //Stub list di mailchimp
        $this->dummy_api->expects($this->any())
            ->method('get_lists')
            ->willReturn(array());

        //Stub inserimento utente nella  mailing list
        $this->dummy_api->expects($this->any())
            ->method('add_list_member')
            ->willReturn(true);

        $this->smss_stub->api = $this->dummy_api;

        $result = $this->smss_stub->subscribe_process($subscription_status, $user_email, $user_role);
    
        $this->assertEquals($result, $res);
    }

}
