<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link
 * @since      1.0
 *
 * @package    Synchro_Mailchimp
 * @subpackage Synchro_Mailchimp/admin/partials
 */

?>

<h3><?php esc_html_e( 'Synchro MailChimp Subscrition', 'synchro_mailchimp' ); ?></h3>
		<table class="form-table">
			<tr>
				<th>
					<label for="mc_subscribe"><?php esc_html_e( 'Subscribe to Mailing List', 'synchro_mailchimp' ); ?></label>
					<div id="spinner"></div>
				</th>
				<td id="sm_result"><h4><?php esc_html_e( 'Success!', 'synchro_mailchimp' ); ?></h4></td>
				<td id="chk_block">
					<input type="checkbox"
						   class="regular-text ltr"
						   id="mc_subscribe"
						   name="mc_subscribe"
							<?php checked( $checked,1 ); ?>
						   title=<?php esc_html_e( 'User subscription to MailChimp list/s', 'synchro_mailchimp' ); ?>
							<?php
							if ( ! current_user_can( 'administrator' ) ) {
								echo 'disabled';
							}
?>
						   >
					<p class="description">
						<?php esc_html_e( 'Select the checkbox to subscribe user to MailChimp mailig lists', 'synchro_mailchimp' ); ?>
					</p>
				</td>
			</tr>
		</table>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
