<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://www.madaritech.com
 * @since      1.0.0
 *
 * @package    Madaritech_Camp_Page
 * @subpackage Plugin_Name/admin/partials
 */
?>

<form method="POST">
<input type="hidden" name="form_submitted" value="Y">
<br>
<table>
    <tr>
        <td>
            <img src="<?php echo plugins_url( '../images/logo_synchro_mailchimp.png', __FILE__ ); ?>" alt="Synchro MailChimp" height="120px">
        </td>
        <td>
            <div style="font-size: 30px; font-weight: bold; margin-bottom: 10px; color: black;">&nbsp;<?php esc_attr_e( 'Synchro MailChimp Plugin', 'synchro_mailchimp' ); ?></div>
            <div style="font-size: 14px; font-weight: bold;">&nbsp;&nbsp;by <a href="http://www.madaritech.com" target="_blank">Madaritech</a></div>
        </td>
    </tr>
</table>

<div class="wrap">
<h2></h2>
<?php if (!$this->requirements_service->mfw_is_missing()) : ?>

    <div id="icon-options-general" class="icon32"></div>
    <!--h1><?php esc_attr_e( 'Settings  ', 'synchro_mailchimp' ); ?></h1-->

    <div id="poststuff">

        <div id="post-body" class="metabox-holder columns-2">

            <!-- main content -->
            <div id="post-body-content">

                <div class="meta-box-sortables ui-sortable">
<?php 

foreach ($all_roles as $role => $role_val) {
    $schema_name = $role . '_subscription_schema';
    $name = $role_val['name'];

?>

                    <div class="postbox">

                        <h3 class="hndle"><span style="font-size: 16px; font-weight: bold;"><?php esc_attr_e( $name, 'synchro_mailchimp' ); ?></span></h3>

                        <div class="inside">
                            <table style="width: 80%;text-align: center;border: 0 none" align="center">
                                <tbody>
<?php foreach ($settings_lists[$role] as $list_id => $list_array) { ?>

                                    <tr>
                                        <td style="border: 0px solid #0073aa; padding: 2em 0">
                                            <input name="<?php echo $role.'-list-'.$list_id; ?>" type="checkbox" id="" value="<?php echo $list_id; ?>"  <?php if ($list_array['checked']) echo " checked"; ?>/>
                                            <span style="font-size: 20px; font-weight: bold;"><?php esc_attr_e( $list_array['name'], 'synchro_mailchimp' ); ?></span>
                                        </td>
                                        <td style="border: 1px solid #82878c;padding: 2em 0">

<?php 
        foreach ($settings_interest_categories[$role][$list_id] as $category_id => $category_name) {
            echo '<div style="font-size: 16px; font-weight: bold; margin: 0 0 12px 0;">'.$category_name.'</span></div>';
            echo '<div style="margin: 0 0 12px 0;">';
            foreach ($settings_interests[$role][$category_id] as $interest_id => $interest_array) {
?>

                                            <input style="margin-left: 30px" name="<?php echo $role.'-list-'.$list_id.'-interest-'.$interest_id; ?>" type="checkbox" id="" value="<?php echo $interest_id; ?>" <?php if ($interest_array['checked']) echo " checked"; ?>/>
                                            <span style="font-size: 14px" ><?php esc_attr_e( $interest_array['name'], 'synchro_mailchimp' ); ?></span>

<?php
            }
            echo '</div>';
        } 
    } 
?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <p align="right"><input class="button-primary" type="submit" name="salva" id="<?php echo $name; ?>-submit-button" value="<?php esc_attr_e( 'Save Settings', 'synchro_mailchimp' ); ?>" /></p>
                        </div>
                        <!-- .inside -->

                    </div>
                    <!-- .postbox -->
<?php } ?>

                </div>
                <!-- .meta-box-sortables .ui-sortable -->

            </div>
            <!-- post-body-content -->

            <!-- sidebar -->
            <div id="postbox-container-1" class="postbox-container">

                <div class="meta-box-sortables">

                    <div class="postbox">

                        <h2 class="hndle"><span><?php esc_attr_e(
                                    'Instructions', 'synchro_mailchimp'
                                ); ?></span></h2>

                        <div class="inside">
                            <p><?php esc_attr_e( 'This plugin let you synchronize the subscription of the WordPress users to MailChimp lists upon the selections made on this page for every role. This selection set the desired match. Then, to apply your choose to a particular user, go on the WordPress user setting page and check the Synchro MailChimp select box.', 'synchro_mailchimp' ); ?></p>
                            <p><?php esc_attr_e( 'Important: is not possibile to select interests without select the corresponding list. In this case the selection will be lost.' ); ?></p>
                            <p><?php esc_attr_e( 'Important: is possible to switch users between the lists, but is not possible to unsubscribe the user from all the MailChimp lists. In this case you need to go on MailChimp and manually delete the user. This will be addressed in next release.', 'synchro_mailchimp' ); ?></p>
                        </div>
                        <!-- .inside -->

                    </div>
                    <!-- .postbox -->

                    <div class="postbox">

                        <h2 class="hndle"><span><?php esc_attr_e(
                                    'Want to contribute?', 'synchro_mailchimp'
                                ); ?></span></h2>

                        <div class="inside">
                            <p><?php esc_attr_e( 'This plugin is completely free. Help me to improve it and release new updated versions. If you have requests for features or bug fixing leave a message: ', 'synchro_mailchimp' ); ?><a href="http://www.madaritech.com/#menu-contact" target="_blank">Madaritech contact form</a></p>

                            <div align="center">
                                <p>
                                    <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
                                        <input type="hidden" name="cmd" value="_s-xclick">
                                        <input type="hidden" name="hosted_button_id" value="G48ZMD7HLATSE">
                                        <input type="image" src="https://www.paypalobjects.com/en_US/GB/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal – The safer, easier way to pay online!">
                                        <img alt="" border="0" src="https://www.paypalobjects.com/it_IT/i/scr/pixel.gif" width="1" height="1">
                                    </form>
                                </p>
                            </div>
                        </div>
                        <!-- .inside -->

                    </div>
                    <!-- .postbox -->

                </div>
                <!-- .meta-box-sortables -->

            </div>
            <!-- #postbox-container-1 .postbox-container -->


        </div>
        <!-- #post-body .metabox-holder .columns-2 -->

        <br class="clear">
    </div>
    <!-- #poststuff -->

<?php endif; ?>

</div> <!-- .wrap -->

</form>