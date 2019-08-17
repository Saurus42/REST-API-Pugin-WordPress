=== Plugin Name ===
Creator: Mateusz Krasuski
Tags: api, json, json-rest-api, menu-routes, menus, REST, wp-api, wp-rest-api, v2
Requires at least: 4.4
Tested up to: 5.1
Stable tag: 0.6.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Menu operations using the REST API

== Description ==

This is a WordPress extension that uses the REST API principles for client <-> server communication.The extension allows you to download and modify not only posts but also the main menu.

The new routes available will be:

* `/menus/v1/menus` list of every registered menu.
* `/menus/v1/sub-menus` list of every registered menu location in your theme.

ACF custom fields supported

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/rest-api-menu` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress


== Frequently Asked Questions ==

= Is this an official extension of WP API? =

There's no such thing.

= Can I contribute to the project? =

Of course! This is the GitHub Repository https://github.com/thebatclaudio/wp-rest-api-v2-menus

== Screenshots ==

Nothing to show. This plugin has no settings or frontend, it just extends WP API with new routes.

== Changelog ==

0.6.1 - Bug fix

0.6 - Added menu locations features

0.5 - Added support for ACF custom fields

0.4 - Added nested menus support and pages slugs

0.3.2 - Bug fix: allowing underscore values in menu slug regexp

0.3.1 - Bug fix: allowing numeric values in menu slug regexp

0.3 - Bug fix

0.2 - Updated compatibility

0.1.1 - Bug fix
