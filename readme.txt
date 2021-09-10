=== dotdigital Signup Form ===
Contributors: dotdigital
Donate link: http://dotdigital.com
Tags: email marketing, newsletter signup
Requires at least: 4.0
Tested up to: 5.8
Requires PHP: 5.6
Version: 4.0.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html


== Description ==

Add the dotdigital signup form plugin to your site and allow your visitors to sign up to your dotdigital-powered newsletter and email marketing campaigns. The email addresses of new subscribers can be added to multiple dotdigital address books.

If you're not a dotdigital user already you can find out more about us at <a href="https://www.dotdigital.com">dotdigital.com</a>.

== Installation ==

If you already have v1 installed, a message will pop up in the admin area of your WordPress account informing you that a new version is available. Simply update from there.

If you don't already have v1, log into your WordPress account and follow these steps:

1. Go to 'Plugins' in the left-hand menu
2. Select 'Add New'
3. Search for 'dotdigital Signup Form'
4. Click on 'Install Now'
5. When installed, click on 'Activate Plugin'

The plugin will appear as 'dotdigital Signup Form' in your left-hand menu.

<a href="https://support.dotdigital.com/hc/en-gb/articles/212216058-Using-the-dotmailer-WordPress-sign-up-form-plugin-v2#install">Read more detailed information on installation</a>.


== Frequently Asked Questions ==

= Q. My site is hosted by WordPress.com. Will the plugin work for me? =
A. No. The plugin can only be uploaded to the installed version of WordPress (WordPress.org), not the hosted version (WordPress.com).

= Q. Can I select more than one address book to sign contacts up to? =
A. Yes you can. This latest version of the plugin allows you to put addresses into multiple address books.

= Q. Can contacts who have previously unsubscribed from my mailing lists re-subscribe through the plugin? =
A. Yes they can.

= Q. My contacts are not appearing in my address book. Why is this? =
A. Check you have followed the installation steps correctly and that your API email and API password appear exactly as they do in your dotdigital account. Remember that the API email is automatically generated and should not be changed.

= Q. Can I send the user to a custom Thank You page after subscription? =
A. From plugin version 3.4 it is possible. You can find the redirection options under the Redirections tab in the plugin dashboard. Here you can set up 3 options:
* No redirection (default): the user will stay on the same page where a short message will be displayed about the result of the subscription
* Local page: you can select a page from your website that will be your dotdigital Thank you page
* Custom URL: with this option you can redirect your user wherever you want to. If you choose this option, please use a valid URL starting with "http://" or "https://" here.

= Q. How can I insert the dotmailer form into my posts and pages? =
A. From plugin version 3.3, you can use the [dotmailer-signup] shortcode to show the form inside the post's content. Shortcode parameters (works only when you use the form with a shortcode):

showtitle=0 - Hide the title of the widget
showdesc=0 - Hide the description under the title
redirection="URL" - Redirect the user to a custom URL after successful submission

Example: [dotmailer-signup showtitle=0 showdesc=0 redirection="http://www.example.com"] (will show the form without a title and description and will redirect the user to http://www.example.com on success)

Since plugin version 5, [dotdigital-signup] can also be used with the same parameters.

== Screenshots ==

1. The plugin will appear as 'dotdigital Signup Form' in your left-hand menu
2. Selecting an address book
3. Changing address book visibility
4. Reordering address books
5. Adding the form to your website

== Copyright ==

Copyright 2014-2021  dotdigital (email : support@dotdigital.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

dotdigital Signup Form bundles the following third-party resources:

dotMailer API v2 PHP client
v1.1.2, Copyright 2014-2015 Roman Piták
Licenses: The MIT License (MIT)
Source: https://github.com/romanpitak/dotMailer-API-v2-PHP-client

PHP REST Client
v1.2.1, Copyright 2014-2015 Roman Piták
Licenses: The MIT License (MIT)
Source: https://github.com/romanpitak/PHP-REST-Client

Composer
v1.0.0-alpha11, Copyright 2012-2015 Nils Adermann, Jordi Boggiano
Licenses: The MIT License (MIT)
Source: https://getcomposer.org/


== Changelog ==
4.0.5 (2017-11-28)

* Mod: PHP7 support
* Mod: Redirection url is always followed regardless of validation result

4.0.1 (2016-06-06)

* Fix: endpoint discovery error where region ID > 1
* Fix: small calendar display issue

4.0 (2016-03-04)

* Mod: the plugin is rewritten using the dotmailer REST (v2) API
* Add: endpoint discovery added - now your API calls use your regional API URL
* Fix: a few small layout fixes


3.4.2 (2015-07-01)

* Fix: position of the shortcode output wasn't correct in the previous versions


3.4.1 (2014-07-17)

* All strings from 'dotMailer' are changed to 'dotmailer' due to a branding change


3.4 (2014-07-03)

* Add: Redirection options in plugin admin, now you can redirect your users to a Thank You page after subscription
* Add: redirection attribute for the shortcode, which lets you use redirections locally from your form shortcodes


3.3 (2014-06-14)

* Add: Now you can add the dotMailer form with the [dotmailer-signup] shortcode to your posts and pages
* Fix: Wrong input type in forms (String) changed to (text)
* Fix: Some code cleanup


3.2.1 (2014-05-16)

* Fix: Now the user stays on the same page instead of being redirected to the home page after submission


3.2 (2014-05-02)

* Fix: Remove warning in the widget if no contact data was saved into the DB
* Fix: Version number confusion
* Mod: Now initial default messages are saved automatically to the database during plugin activation, so users need one step less to set it up properly.
* Mod: Now user settings are not deleted on plugin deactivation. Settings are only deleted if you uninstall the plugin.


== Upgrade Notice ==

= 4.0 =

* Mod: the plugin is rewritten using the dotmailer REST (v2) API
* Add: endpoint discovery added - now your API calls use your regional API URL
* Fix: a few small layout fixes

= 3.4 =

* Add: Redirection options in plugin admin, now you can redirect your users to a Thank You page after subscription
* Add: redirection attribute for the shortcode, which lets you use redirections locally from your form shortcodes

= 3.3 =
* Add: Now you can add the dotMailer form with the [dotmailer-signup] shortcode to your posts and pages
* Fix: Wrong input type in forms (String) changed to (text)
* Fix: Some code cleanup

= 3.2.1 =
* Fix: Now the user stays on the same page instead of being redirected to the home page after submission

= 3.2 =
* Fix: Remove warning in the widget if no contact data was saved into the DB
* Fix: Version number confusion
* Mod: Now initial default messages are save automatically to the database during plugin activation, so users need one step less to set it up properly.

* Mod: Now user settings are not deleted on plugin deactivation. Settings are only deleted if you uninstall the plugin.

= 3.1 =
* Fix for closing php tag on dm_widget

= 3.0 =
* Fix for reported javascript conflicts and styling issues

= 2.1 =
* Fix for Jquery Mini File, upgraded version to fix conflict

= 2.0 =
* Using the new Settings API.
* Extra features added.

= 1.1 =
* Fixed an error thrown
