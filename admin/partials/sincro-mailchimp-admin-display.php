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

<h1><?php esc_attr_e( 'Sincro MailChimp Plugin', 'sincro_mailchimp' ); ?></h1>

<div class="wrap">

    <div id="icon-options-general" class="icon32"></div>
    <h2><?php esc_attr_e( 'Impostazioni', 'sincro_mailchimp' ); ?></h2>
    <div id="poststuff">

        <div id="post-body" class="metabox-holder columns-2">

            <!-- main content -->
            <div id="post-body-content">

                <div class="meta-box-sortables ui-sortable">

                    <div class="postbox">

                        <div class="handlediv" title="Click to toggle"><br></div>
                        <!-- Toggle -->

                        <div class="inside">

                            <form method="POST">
                                <input type="hidden" name="form_submitted" value="Y">
                                <table class="widefat">
<?php 

foreach ($all_roles as $role => $role_val) {
    $schema_name = $role . '_subscription_schema';
    $name = $role_val['name'];
?>

                                    <tr>
                                        <td class="row-title" style="background: #0074a2;color: #fff;border: 2px solid #fff;padding: 2em 
0"><label for="tablecell">
                                            <?php esc_attr_e(
                                                    $name, 'sincro_mailchimp'
                                                ); ?></label>
                                        </td>
                                        <td>

<?php foreach ($settings_lists[$role] as $list_id => $list_array) { ?>



                                                    <h1 style="margin-top: 30px"><input name="<?php echo $role.'-list-'.$list_id; ?>" type="checkbox" id="" value="<?php echo $list_id; ?>"  <?php if ($list_array['checked']) echo " checked"; ?>/>
                                                    <span><?php esc_attr_e( $list_array['name'], 'sincro_mailchimp' ); ?></span></h1>


<?php
        foreach ($settings_interest_categories[$role][$list_id] as $category_id => $category_name) {
             echo "<h4>$category_name</h4>";

             foreach ($settings_interests[$role][$category_id] as $interest_id => $interest_array) {
?>

                <input style="margin-left: 30px" name="<?php echo $role.'-list-'.$list_id.'-interest-'.$interest_id; ?>" type="checkbox" id="" value="<?php echo $interest_id; ?>" <?php if ($interest_array['checked']) echo " checked"; ?>/>
                <span ><?php esc_attr_e( $interest_array['name'], 'sincro_mailchimp' ); ?></span>

<?php
             }

        } 
    } 
?>
                                            <!--textarea id="<?php echo $schema_name; ?>" name="<?php echo $schema_name; ?>" cols="80" rows="10" class="large-text"><?php echo $sincro_mailchimp_options[$schema_name]; ?></textarea--><br>
                                        </td>
                                    </tr>

<?php } ?>

                                </table>
                                <br>
                                <input class="button-primary" type="submit" name="salva" id="save_dec_nat_plugin" value="<?php esc_attr_e( 'Save Settings' ); ?>" />
                                <div id="save_spinner_effect" class="spinner" style="float:none;width:auto;height:auto;padding:10px 0 10px 50px;background-position:20px 0;"></div>
                            </form>
                        </div>
                        <!-- .inside -->

                    </div>
                    <!-- .postbox -->

                </div>
                <!-- .meta-box-sortables .ui-sortable -->

            </div>
            <!-- post-body-content -->

            <!-- sidebar -->
            <div id="postbox-containerwp-1" class="postbox-container">

                <div class="meta-box-sortables">

                    <div class="postbox">

                        <div class="handlediv" title="Click to toggle"><br></div>
                        <!-- Toggle -->

                        <h2 class="hndle"><span><?php esc_attr_e(
                                    'Istruzioni', 'wp_admin_style'
                                ); ?></span></h2>

                        <div class="inside">
                            <p><?php esc_attr_e( 'Nel riquadro a sinistra è possibile selezionare le impostazioni di visualizzazione per la corretta visualizzazione delle coming soon Page o Maintenance Mode Page. Per visualizzare le modifiche premere il pulsante "Salva Impostazioni". Per attivare il plugin ricordarsi di abilitare la voce "Attiva la pagina".', 'wp_admin_style' ); ?></p>

                            <div align="center"><p><form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
                            <input type="hidden" name="cmd" value="_s-xclick">
                            <input type="hidden" name="hosted_button_id" value="G48ZMD7HLATSE">
                            <input type="image" src="https://www.paypalobjects.com/en_US/GB/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal – The safer, easier way to pay online!">
                            <img alt="" border="0" src="https://www.paypalobjects.com/it_IT/i/scr/pixel.gif" width="1" height="1">
                            </form></p></div>

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

</div> <!-- .wrap -->
