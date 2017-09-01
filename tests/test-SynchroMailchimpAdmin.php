<?php
/**
 * Class SynchroMailchimpConfigurationServiceTest
 *
 * @package Synchro_Mailchimp
 */

/**
 * Synchro_Mailchimp_configuration_Service Class test case.
 */
class SynchroMailchimpAdminTest extends WP_UnitTestCase
{
    private $config;
    private $lists;
    private $interests;
    private $all_roles;
    private $settings_lists;
    private $settings_interest_categories;
    private $settings_interests;
 
    public function setUp()
    {
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

        //['list_id' => ['name' => 'list_name', 'checked' => false] ]
        $settings_lists_role = array( $lists['test'] => array('name' => 'Test', 'checked' => false),
                                        $lists['acme'] => array('name' => 'Acme', 'checked' => false));

        $this->settings_lists = array('administrator' => $settings_lists_role,
                                'editor' => $settings_lists_role,
                                'author' => $settings_lists_role,
                                'contributor' => $settings_lists_role,
                                'subscriber' => $settings_lists_role); 
        
        //['list_id' => ['category_id' => 'category_name'] ]
        $settings_interest_categories_role = array( '060a231f4f' => array( 'd0cbaa8d26' => 'test-cat' ),
                                                    'e87b1536bb' => array( '434e28e591' => 'cat1-interest', 
                                                                            '618dd1d9a1' => 'cat2-interest'));

        $this->settings_interest_categories = array('administrator' => $settings_interest_categories_role,
                                            'editor' => $settings_interest_categories_role,
                                            'author' => $settings_interest_categories_role,
                                            'contributor' => $settings_interest_categories_role,
                                            'subscriber' => $settings_interest_categories_role);

        //['category_id' => ['interest_id' => ['name' => 'interest_name', 'checked' => false]]]
        $settings_interests_role = array('d0cbaa8d26' => array('dafaf73c29' => array('name' => 'group1', 'checked' => false),
                                                            'ab364ca78d' => array('name' => 'group2', 'checked' => false),
                                                            '2bec2ca41d' => array('name' => 'group3', 'checked' => false) ), 
                                        '434e28e591' => array('27e89ce542' => array('name' => 'acme-group1', 'checked' => false),
                                                                '9e05bcd6ce' => array('name' => 'acme-group2', 'checked' => false)),
                                        '618dd1d9a1' => array('cf5965131a' => array('name' => 'acme-group3', 'checked' => false),
                                                                '7cd76ab7e9' => array('name' => 'acme-group4', 'checked' => false)));
        
        $this->settings_interests = array('administrator' => $settings_interests_role,
                                    'editor' => $settings_interests_role,
                                    'author' => $settings_interests_role,
                                    'contributor' => $settings_interests_role,
                                    'subscriber' => $settings_interests_role);  

        $this->mock_configuration_service = $this->getMockBuilder('Synchro_Mailchimp_Configuration_Service')
                                                ->disableOriginalConstructor()
                                                ->setMethods(null)
                                                ->getMock();

        $this->mock_api = $this->getMockBuilder('Synchro_Mailchimp_Api_Service')
            ->disableOriginalConstructor()
            ->setMethods(array( 'get_lists', 'get_list_interest_categories', 'get_list_interest_category_interests' ))
            ->getMock();

        $this->all_roles = array('administrator' => 'Administrator', 'editor' => 'Editor', 'author' => 'Author', 'contributor' => 'Contributor', 'subscriber' => 'Subscriber',);
    }

    /**
     * read_mailchimp_schema test.
     */
    public function test_read_mailchimp_schema() 
    {
        $sm_admin_obj = new Synchro_Mailchimp_Admin('Synchro MailChimp','1.0.0');

        $obj1 = new stdClass();
        $obj1->id = $this->lists['test'];
        $obj1->name = 'test';

        $dummy_list = array();
        $dummy_list[1] = $obj1;

        $this->mock_api->expects($this->any())
            ->method('get_lists')
            ->willReturn($dummy_list);

        $obj3 = new stdClass();
        $obj3->title = 'test-cat';
        $obj3->list_id = $this->lists['test']; //'060a231f4f'
        $obj3->id = 'd0cbaa8d26';

        $obj4 = new stdClass();
        $obj4->title = 'cat1-interest';
        $obj4->list_id = $this->lists['test']; //'e87b1536bb'
        $obj4->id = '434e28e591';

        $dummy_categories = array();
        $dummy_categories[0] = $obj3;
        $dummy_categories[1] = $obj4;

        $this->mock_api->expects($this->any())
                        ->method('get_list_interest_categories')
                        ->willReturn($dummy_categories);

        $obj5 = new stdClass();
        $obj5->id = 'dafaf73c29';
        $obj5->name = 'group1';

        $dummy_interests = array();
        $dummy_interests[0] = $obj5;

        $this->mock_api->expects($this->any())
                        ->method('get_list_interest_category_interests')
                        ->willReturn($dummy_interests);

        $sm_admin_obj->api = $this->mock_api;

        $mailchimp_lists = array();
        $mailchimp_interest_categories = array(); 
        $mailchimp_interests = array();

        $sm_admin_obj->read_mailchimp_schema($mailchimp_lists, $mailchimp_interest_categories, $mailchimp_interests);

        $this->assertEquals(count($mailchimp_lists), 1);
        $this->assertEquals(count($mailchimp_interest_categories), 1);
        $this->assertEquals(count($mailchimp_interest_categories[$this->lists['test']]), 2);
        $this->assertEquals($mailchimp_interest_categories[$this->lists['test']]['d0cbaa8d26'], 'test-cat');
        $this->assertEquals($mailchimp_interest_categories[$this->lists['test']]['434e28e591'], 'cat1-interest');
        $this->assertEquals(count($mailchimp_interests), 2);
        $this->assertEquals(count($mailchimp_interests['d0cbaa8d26']), 1);
        $this->assertEquals(count($mailchimp_interests['434e28e591']), 1);
        $this->assertEquals($mailchimp_interests['d0cbaa8d26']['dafaf73c29']['name'], 'group1');
        $this->assertEquals($mailchimp_interests['d0cbaa8d26']['dafaf73c29']['checked'], false);
        $this->assertEquals($mailchimp_interests['434e28e591']['dafaf73c29']['name'], 'group1');
        $this->assertEquals($mailchimp_interests['434e28e591']['dafaf73c29']['checked'], false);

    }

    /**
     * build_setting_form test.
     */
    public function test_build_setting_form() 
    {
        $lists = $this->lists;
        $sm_admin_obj = new Synchro_Mailchimp_Admin('Synchro MailChimp','1.0.0');

        $sm_admin_obj->build_setting_form($this->all_roles, $this->config, $this->settings_lists, $this->settings_interest_categories, $this->settings_interests);

        $res_lists['administrator'] = array( $lists['test'] => array('name' => 'Test', 'checked' => true),
                                            $lists['acme'] => array('name' => 'Acme', 'checked' => false));
        $this->assertEquals($this->settings_lists['administrator'], $res_lists['administrator']);

        $res_lists['editor'] = array( $lists['test'] => array('name' => 'Test', 'checked' => false),
                                    $lists['acme'] => array('name' => 'Acme', 'checked' => true));
        $this->assertEquals($this->settings_lists['editor'], $res_lists['editor']);

        $res_lists['author'] = array( $lists['test'] => array('name' => 'Test', 'checked' => false),
                                    $lists['acme'] => array('name' => 'Acme', 'checked' => false));
        $this->assertEquals($this->settings_lists['author'], $res_lists['author']);

        $res_lists['contributor'] = array( $lists['test'] => array('name' => 'Test', 'checked' => true),
                                    $lists['acme'] => array('name' => 'Acme', 'checked' => false));
        $this->assertEquals($this->settings_lists['contributor'], $res_lists['contributor']);

        $res_lists['subscriber'] = array( $lists['test'] => array('name' => 'Test', 'checked' => true),
                                $lists['acme'] => array('name' => 'Acme', 'checked' => true));
        $this->assertEquals($this->settings_lists['subscriber'], $res_lists['subscriber']);

        $res_settings_interest_categories = array( '060a231f4f' => array( 'd0cbaa8d26' => 'test-cat' ),
                                                    'e87b1536bb' => array( '434e28e591' => 'cat1-interest', 
                                                                            '618dd1d9a1' => 'cat2-interest'));
        $this->assertEquals($this->settings_interest_categories['subscriber'], $res_settings_interest_categories);

        $res_settings_interests['administrator'] = ['d0cbaa8d26' => array('dafaf73c29' => array('name' => 'group1', 'checked' => false),
                                                                'ab364ca78d' => array('name' => 'group2', 'checked' => true),
                                                                '2bec2ca41d' => array('name' => 'group3', 'checked' => true) ), 
                                            '434e28e591' => array('27e89ce542' => array('name' => 'acme-group1', 'checked' => false),
                                                                    '9e05bcd6ce' => array('name' => 'acme-group2', 'checked' => false)),
                                            '618dd1d9a1' => array('cf5965131a' => array('name' => 'acme-group3', 'checked' => false),
                                                                    '7cd76ab7e9' => array('name' => 'acme-group4', 'checked' => false))];
        $this->assertEquals($this->settings_interests['administrator'], $res_settings_interests['administrator']);

        $res_settings_interests['editor'] = ['d0cbaa8d26' => array('dafaf73c29' => array('name' => 'group1', 'checked' => false),
                                                                'ab364ca78d' => array('name' => 'group2', 'checked' => false),
                                                                '2bec2ca41d' => array('name' => 'group3', 'checked' => false) ), 
                                            '434e28e591' => array('27e89ce542' => array('name' => 'acme-group1', 'checked' => true),
                                                                    '9e05bcd6ce' => array('name' => 'acme-group2', 'checked' => false)),
                                            '618dd1d9a1' => array('cf5965131a' => array('name' => 'acme-group3', 'checked' => true),
                                                                    '7cd76ab7e9' => array('name' => 'acme-group4', 'checked' => false))];
        $this->assertEquals($this->settings_interests['editor'], $res_settings_interests['editor']);

        $res_settings_interests['author'] = ['d0cbaa8d26' => array('dafaf73c29' => array('name' => 'group1', 'checked' => false),
                                                                    'ab364ca78d' => array('name' => 'group2', 'checked' => false),
                                                                    '2bec2ca41d' => array('name' => 'group3', 'checked' => false) ), 
                                                '434e28e591' => array('27e89ce542' => array('name' => 'acme-group1', 'checked' => false),
                                                                        '9e05bcd6ce' => array('name' => 'acme-group2', 'checked' => false)),
                                                '618dd1d9a1' => array('cf5965131a' => array('name' => 'acme-group3', 'checked' => false),
                                                                    '7cd76ab7e9' => array('name' => 'acme-group4', 'checked' => false))];
        $this->assertEquals($this->settings_interests['author'], $res_settings_interests['author']);

        $res_settings_interests['contributor'] = ['d0cbaa8d26' => array('dafaf73c29' => array('name' => 'group1', 'checked' => true),
                                                                'ab364ca78d' => array('name' => 'group2', 'checked' => false),
                                                                '2bec2ca41d' => array('name' => 'group3', 'checked' => true) ), 
                                            '434e28e591' => array('27e89ce542' => array('name' => 'acme-group1', 'checked' => false),
                                                                    '9e05bcd6ce' => array('name' => 'acme-group2', 'checked' => false)),
                                            '618dd1d9a1' => array('cf5965131a' => array('name' => 'acme-group3', 'checked' => false),
                                                                    '7cd76ab7e9' => array('name' => 'acme-group4', 'checked' => false))];
        $this->assertEquals($this->settings_interests['contributor'], $res_settings_interests['contributor']);

        $res_settings_interests['subscriber'] = ['d0cbaa8d26' => array('dafaf73c29' => array('name' => 'group1', 'checked' => false),
                                                                'ab364ca78d' => array('name' => 'group2', 'checked' => true),
                                                                '2bec2ca41d' => array('name' => 'group3', 'checked' => false) ), 
                                            '434e28e591' => array('27e89ce542' => array('name' => 'acme-group1', 'checked' => true),
                                                                    '9e05bcd6ce' => array('name' => 'acme-group2', 'checked' => false)),
                                            '618dd1d9a1' => array('cf5965131a' => array('name' => 'acme-group3', 'checked' => true),
                                                                    '7cd76ab7e9' => array('name' => 'acme-group4', 'checked' => true))];
        $this->assertEquals($this->settings_interests['subscriber'], $res_settings_interests['subscriber']);
    }

}
