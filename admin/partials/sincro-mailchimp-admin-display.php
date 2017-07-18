<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       h
 * @since      1.0.0
 *
 * @package    Sincro_Mailchimp
 * @subpackage Sincro_Mailchimp/admin/partials
 */
?>

<h3>Iscrizione MailChimp</h3>
	    <table class="form-table">
	        <tr>
	            <th>
	                <label for="mc_subscribe">Iscrivi alla Mailing List</label>
	                <div id="spinner"></div>
	            </th>
	            <td id="sm_result"><h4>Operazione Completata!</h4></td>
	            <td id="chk_block">
	                <input type="checkbox"
	                       class="regular-text ltr"
	                       id="mc_subscribe"
	                       name="mc_subscribe"
	                       value="<?= esc_attr(get_user_meta($user->ID, 'mc_subscribe', true)); ?>"
	                       <?php checked($checked,1); ?>
	                       title="Iscrizione utente alla/e mailing list MailChimp"
	                       <?php if (!current_user_can('administrator')) echo "disabled" ?>
	                       >
	                <p class="description">
	                    Selezionare il checkbox se si intende iscrivere l'utente alla/e mailing list MailChimp
	                </p>
	            </td>
	        </tr>
	    </table>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
