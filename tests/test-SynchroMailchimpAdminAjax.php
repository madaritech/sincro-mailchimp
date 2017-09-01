<?php
/**
 * Class Synchro_Mailchimp_Admin_Ajax_Test
 *
 * @package Synchro_Mailchimp
 */


/**
 * Synchro_Mailchimp_Admin_Ajax Class test case.
 *
 * @group ajax
 */
class Synchro_Mailchimp_Admin_Ajax_Test extends WP_Ajax_UnitTestCase
{

    function test_ajax_esegui_iscrizione() 
    {

        $_POST['check_status'] = 1;
        $_POST['user_email'] = 'test@madaritech.com';
        $_POST['user_role'] = 'contributor';
        $_POST['_wpnonce'] = wp_create_nonce('esegui_iscrizione');
        $_POST['ut'] = 1;

        try {
            $this->_handleAjax('esegui_iscrizione');
        } catch ( Exception $e ) {
        }

        $response = json_decode($this->_last_response);
        $this->assertInternalType('object', $response);
        $this->assertObjectHasAttribute('success', $response);
        $this->assertTrue($response->success);
        $this->assertObjectHasAttribute('data', $response);
        $this->assertEquals('Verifica Unit Test', $response->data);
    }

}