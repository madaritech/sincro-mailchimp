<?php
/**
 * Class SincroMailchimpConfigurationServiceTest
 *
 * @package Sincro_Mailchimp
 */

/**
 * Sincro_Mailchimp_configuration_Service Class test case.
 */
class SincroMailchimpConfigurationServiceTest extends WP_UnitTestCase
{
    private $config;
    private $lists;
    private $interests;
 
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

        $this->smcs_mock = $this->getMockBuilder('Sincro_Mailchimp_Configuration_Service')
                                ->disableOriginalConstructor()
                                ->setMethods(null)
                                ->getMock();
    }

    /**
     * get_by_role test.
     */
    public function test_get_by_role() 
    {
        $lists = $this->lists;
        $interests = $this->interests;

        $cs_obj = $this->smcs_mock;
        $cs_obj->set_configuration($this->config);


        $res_administrator = $cs_obj->get_by_role('administrator');
        $res_editor = $cs_obj->get_by_role('editor');
        $res_author = $cs_obj->get_by_role('author');
        $res_contributor = $cs_obj->get_by_role('contributor');
        $res_subscriber = $cs_obj->get_by_role('subscriber');

        $this->assertCount(1, $res_administrator);
        $this->assertCount(1, $res_editor);
        $this->assertCount(0, $res_author);
        $this->assertCount(1, $res_contributor);
        $this->assertCount(2, $res_subscriber);

        $this->assertEquals([$lists['acme'] => array($interests['acme-group1'] => true, 
                                                 $interests['acme-group2'] => false, 
                                                 $interests['acme-group3'] => true, 
                                                 $interests['acme-group4'] => true), 
                            $lists['test'] => array($interests['group1'] => false,
                                                   $interests['group2'] => true,
                                                   $interests['group3'] => false)], $res_subscriber);
    }

    /**
     * get_by_role_and_list test.
     */
    public function test_get_by_role_and_list() 
    {
        $lists = $this->lists;
        $interests = $this->interests;

        $cs_obj = $this->smcs_mock;
        $cs_obj->set_configuration($this->config);

        $res_administrator = $cs_obj->get_by_role_and_list('administrator', $lists['acme']);
        $res_editor = $cs_obj->get_by_role_and_list('editor', $lists['acme']);
        $res_author = $cs_obj->get_by_role_and_list('author', $lists['acme']);
        $res_contributor = $cs_obj->get_by_role_and_list('contributor', $lists['acme']);
        $res_subscriber = $cs_obj->get_by_role_and_list('subscriber', $lists['acme']);

        $this->assertEquals(array(), $res_administrator);
        $this->assertEquals(array($interests['acme-group1'] => true, 
                                 $interests['acme-group2'] => false, 
                                 $interests['acme-group3'] => true, 
                                 $interests['acme-group4'] => false), $res_editor);
        $this->assertEquals(array(), $res_author);
        $this->assertEquals(array(), $res_contributor);
        $this->assertNotEquals(array($interests['acme-group1'] => true, 
                                 $interests['acme-group2'] => false, 
                                 $interests['acme-group3'] => true, 
                                 $interests['acme-group4'] => true), $res_contributor);
        $this->assertEquals(array($interests['acme-group1'] => true, 
                                 $interests['acme-group2'] => false, 
                                 $interests['acme-group3'] => true, 
                                 $interests['acme-group4'] => true), $res_subscriber);
        $this->assertNotEquals(array($interests['acme-group1'] => false, 
                                 $interests['acme-group2'] => false, 
                                 $interests['acme-group3'] => true, 
                                 $interests['acme-group4'] => true), $res_subscriber);

    }

}
