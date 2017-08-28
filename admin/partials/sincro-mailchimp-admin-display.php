<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link
 * @since      1.0.0
 *
 * @package    Sincro_Mailchimp
 * @subpackage Sincro_Mailchimp/admin/partials
 */
?>

<h3><?php echo __("Iscrizione MailChimp", "sincro_mailchimp"); ?></h3>
	    <table class="form-table">
	        <tr>
	            <th>
	                <label for="mc_subscribe"><?php echo __("Iscrivi alla Mailing List", "sincro_mailchimp"); ?></label>
	                <div id="spinner"></div>
	            </th>
	            <td id="sm_result"><h4><?php echo __("Operazione Completata!", "sincro_mailchimp"); ?></h4></td>
	            <td id="chk_block">
	                <input type="checkbox"
	                       class="regular-text ltr"
	                       id="mc_subscribe"
	                       name="mc_subscribe"
	                       <?php checked($checked,1); ?>
	                       title=<?php echo __("Iscrizione utente alla/e mailing list MailChimp", "sincro_mailchimp"); ?>
	                       <?php if (!current_user_can('administrator')) echo "disabled" ?>
	                       >
	                <p class="description">
	                    <?php echo __("Selezionare il checkbox se si intende iscrivere l'utente alla/e mailing list MailChimp", "sincro_mailchimp"); ?>
	                </p>
	            </td>
	        </tr>
	    </table>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
