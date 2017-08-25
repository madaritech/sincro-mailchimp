<?php
/**
 * Class SincroMailchimpConfigurationServiceAdapterTest
 *
 * @package Sincro_Mailchimp
 */

/**
 * Sincro_Mailchimp_User_Service_Adapter Class test case.
 */
class SincroMailchimpUserServiceAdapterTest extends WP_UnitTestCase
{
    private $config;
    private $lists;
    private $interests;
    private $users;
 
    public function setUp()
    {
        $this->users['administrator'] = $this->factory->user->create_and_get(array( 'role' => 'administrator' ));
        $this->users['editor'] = $this->factory->user->create_and_get(array( 'role' => 'editor' ));
        $this->users['author'] = $this->factory->user->create_and_get(array( 'role' => 'author' ));
        $this->users['contributor'] = $this->factory->user->create_and_get(array( 'role' => 'contributor' ));
        $this->users['subscriber'] = $this->factory->user->create_and_get(array( 'role' => 'subscriber' ));

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
    }

    /**
     * user_list_interests test.
     */
    public function test_user_list_interests() 
    {
        $lists = $this->lists;
        $interests = $this->interests;

        $configuration = $this->config;
        $cs_obj = new Sincro_Mailchimp_Configuration_Service($configuration);
        $us_obj = new Sincro_Mailchimp_User_Service($cs_obj);
        $usa_obj = new Sincro_Mailchimp_User_Service_Adapter($us_obj);

        $res_editor = $usa_obj->user_list_interests(array(), $this->users['editor']->ID, $lists['acme']);
        $this->assertEquals([$interests['acme-group1'] => true, 
                             $interests['acme-group2'] => false, 
                             $interests['acme-group3'] => true, 
                             $interests['acme-group4'] => false], $res_editor);

        $res_subscriber = $usa_obj->user_list_interests(array($interests['acme-group1'] => false), $this->users['subscriber']->ID, $lists['acme'] );
        $this->assertEquals([$interests['acme-group1'] => false, 
                             $interests['acme-group2'] => false, 
                             $interests['acme-group3'] => true, 
                             $interests['acme-group4'] => true], $res_subscriber);

        $res_administrator = $usa_obj->user_list_interests(array(), $this->users['administrator']->ID, $lists['acme']);
        $this->assertEquals(array(), $res_administrator);

        $res_administrator = $usa_obj->user_list_interests(array($interests['acme-group1'] => false), $this->users['administrator']->ID, $lists['acme']);
        $this->assertEquals(array($interests['acme-group1'] => false), $res_administrator);

        $res = $usa_obj->user_list_interests(array(), 100, $lists['acme']);
        $this->assertEquals(array(), $res);
    }

}
