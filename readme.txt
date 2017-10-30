=== Plugin Name ===
Synchro MailChimp
Contributors: madaritech
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=7F3RJK8PYECUY
Tags: mailchimp, synchro
Requires at least: 4.8
Tested up to: 4.8.2
Stable tag: 1.6
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

The plugin permits administrators to subscribe/unsubscribe WordPress users to MailChimp by a checkbox on the WordPress user settings page. Every user, depending on their roles and the template defined in plugin settings page, can be associated to MailChimp lists and interests. "Synchro MailChimp" requires "MailChimp for WordPress" plugin to work properly.

== Description ==


== Installation ==

1. This plugin needs "MailChimp for WordPress" installed and connected to MailChimp, so install, activate and connect this plugin first.
1. Upload the plugin files to the `/wp-content/plugins/synchro-mailchimp` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress.
1. Use the link "Synchro MC" on the admin menu to enter in the plugin setting page.
1. In the Synchro Mailchimp settings page you will see for every role your lists and interests as defined in MailChimp. Select with checkbox the lists and interests you want to be able to subscribe for every role.
1. To actually subscribe a user go in WordPress user settings page, choose the user, scroll down and select the Synchro MailChimp checkbox. To unsubscribe uncheck it.

== Frequently Asked Questions ==

= Subscription was successful, but on MailChimp lists are not updated =

Sometimes the MailChimp lists view doesn't refresh quickly. Wait and refresh the browser to get an updated page with the expected results.

= I get error message claiming: "your email has signed up to a lot of lists very recently; we're not allowing more signups for now" =

For security reasons MailChimp can temporary ban email addresses that subscribe/unsubscribe too often. This issue is due to MailChimp policy, and not to Synchro MailChimp Plugin.

== Screenshots ==

1. If your MailChimp has no lists the settings page shows empty list for every role.

2. If your MailChimp has lists and groups the settings page shows them for every role, ready for configuration.

3. We can select lists, interests of corresponding groups for the role we need. "Save Settings" will save this configuration.

4. In User Settings page, for the role we checked the list we can actually subscribe the user with the subscription checkbox.

5. We can check/uncheck the checkbox and the plugin will subscribe/unsubscribe the user following the configuration of the plugin settings page.
 
6. If the subscribing/unsusribing process is successful we get a feedback.

7. We can chek on MailChimp to see the subscription result.

== Changelog ==

= 1.6 =
* Bug fixed

= 1.5 =
* bug fixed
* Comments to code added
* Code refactoring

= 1.3 =
* Refactoring based on WordPress Standard

= 1.2 =
* Activator fixed: now doesn't overwrite settings if they was set by the user in previous activation

= 1.1 =
* Bug in plugin name

= 1.0 =
* First release.

== Upgrade Notice ==

= 1.6 =
This version fixes a bug. Upgrade immediately.

= 1.5 =
This version fixes a security issue.  Upgrade immediately.

= 1.3 =
This version fixes a security issue.  Upgrade immediately.

= 1.2 =
This version fixes a security related bug.  Upgrade immediately.
