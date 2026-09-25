=== SmartyParcel – Shipping Automation & Order Tracking for GLS, InPost, Nova Post and more ===
Contributors: kirillbdev
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Tags: gls, postnord, meest, nova post, order tracking
Requires PHP: 8.0
Tested up to: 7.1
Stable tag: 1.23.1

The easiest way to automate shipping in WooCommerce: pickup points at checkout (list or map), one-click shipping labels and order tracking.

== Description ==

SmartyParcel helps your WooCommerce store grow by taking shipping routine off your hands. Connect your carriers and automate shipping from checkout to delivery: pickup points at checkout, one-click shipping labels, order tracking and customer notifications — all from one platform. Trusted by 7,000+ WooCommerce stores.

[Product Overview](https://smartyparcel.com/woocommerce-integration/?utm_source=wporg)
[Supported carriers](https://smartyparcel.com/supported-carriers/?utm_source=wporg)
[Documentation](https://smartyparcel.com/docs/knowledge-base-woocommerce/?utm_source=wporg)

Example: setting up PostNord pickup points in the WooCommerce checkout.

https://www.youtube.com/watch?v=UIfRYDeO9Lo

= Features =

* Pickup point selection built into the WooCommerce checkout — a searchable list, plus an optional Google map view.
* Separate shipping methods per delivery type, so "To pickup point" and "Courier to address" are two distinct options in the WooCommerce shipping zone.
* Points are loaded live from the carrier network, with opening hours, addresses and parcel size limits where the carrier provides them.
* Works with WooCommerce shipping zones, so each country gets exactly the carriers you ship there with.
* Create domestic and international shipping labels in a few clicks.
* Bulk label creation and printing (paid plans).
* Rule-based automation: create labels automatically on order status change, on payment, or on your own conditions.
* Shipping rules: prepare shipments automatically by your criteria (for example, choose the carrier by parcel weight or add extra services and more).
* Easy to ship from multiple sender addresses.
* Provide carrier live-rates at checkout for your customers (paid plans).
* Advanced order tracking with real-time status sync and customer notifications (Email and SMS).

= Pickup point selector at checkout (ParcelShops, Parcel Lockers, Service Points) =

* GLS (Europe)
* InPost (Poland only, for now)
* PostNord
* Nova Post
* Nova Poshta (Ukraine)
* Ukrposhta
* Meest
* Rozetka Delivery

No API keys or contracts required.

= Supported carriers =

GLS, InPost, Meest, Nova Post, Nova Poshta, PostNord, Nova Global, Rozetka Delivery, Ukrposhta and many more. We’re continuously integrating new carriers and features!

= Compatibility =

* WooCommerce HPOS (High-Performance Order Storage) and legacy order storage.
* WPML, Polylang and multi-currency stores.
* PHP 8.0+, tested with the latest WordPress and WooCommerce releases.

== Installation ==

= Minimum Requirements =

* PHP 8.0 or greater is recommended
* MySQL 5.7 or greater is recommended

= Automatic installation =

Automatic installation is the easiest option as WordPress handles the file transfers itself and you don't need to leave your web browser. To do an automatic install of plugin, log in to your WordPress dashboard, navigate to the Plugins menu, and click Add New.

In the search field type "SmartyParcel" and click Search Plugins. Once you've found it you can view details about it such as the point release, rating, and description. Most importantly of course, you can install it by simply clicking "Install Now".

= Manual installation =

The manual installation method involves downloading this plugin and uploading it to your webserver via your favourite FTP application. The WordPress codex contains instructions on how to do this here.

= Updating =

Automatic updates should work like a charm; as always though, ensure you backup your site just in case.

== External services ==

This plugin uses SmartyParcel API to provide advanced logistic functions (like create labels, tracking etc.) and also external API to collect user feedbacks and installation telemetry, which includes the store URL and the WordPress administrator email address ([Privacy Policy](https://smartyparcel.com/privacy/)).

== FAQ ==

= Which carriers does SmartyParcel support? =

SmartyParcel natively integrates GLS, InPost, PostNord, Nova Post, Nova Poshta, Ukrposhta, Rozetka Delivery, Meest and Nova Global.

= How do customers choose a pickup point or parcel locker at checkout? =

The plugin adds a pickup point selector to the WooCommerce checkout for every enabled carrier. Customers search the carrier network by city or address and pick a pickup point, parcel locker or service point; the selected point is saved with the order and used when the shipping label is created.

= Can customers select a pickup point on a map? =

Yes. Besides the searchable list, the pickup point selector has a Google Maps mode with search by city and street. It requires a Google Maps API key, which is set in the plugin settings.

= Can I create and print shipping labels in bulk? =

Yes, on paid plans. Select any number of WooCommerce orders and create and print their shipping labels in one run, instead of opening orders one by one. Labels can also be generated automatically by rules — on order status change, on payment, or on your own conditions.

= Does the plugin support cash on delivery (COD)? =

Yes. COD is supported for Nova Poshta, Ukrposhta, Meest and Rozetka Delivery. We are working on extending cash on delivery support to more carriers.

= Can I use SmartyParcel solely for order tracking? =

Yes. If you already create shipping labels in other tools or directly with carriers, SmartyParcel can run as a centralized tracking engine only: real-time status updates, automated status syncing in WooCommerce and customer notifications.

= Does the plugin support WooCommerce HPOS? =

Yes. The plugin is fully compatible with WooCommerce High-Performance Order Storage (custom order tables) and with the legacy order storage.

= Does the plugin work with WPML, Polylang and multi-currency stores? =

Yes. The plugin is compatible with WPML and Polylang, and works in multi-currency stores. Carrier data at checkout is shown in the language configured for the carrier.

= Does the plugin support WooCommerce checkout blocks? =

Unfortunately, the plugin doesn't support checkout blocks yet, but we are working on it.

== Changelog ==

= Version 1.23.1 / (25.09.2026) =
* Added GLS Service Point delivery shipping method.
* Added GLS Address delivery shipping method.
* Added GLS to pickup point selection from Google Map.
* Added Nova Post Address delivery shipping method.
* Added rates calculation for Nova Post address delivery.
* Changed api endpoint for batch and auto label creation.

= Version 1.23.0 / (19.09.2026) =
* Introduced Pickup point selection from Google Map. Supported carriers: Nova Poshta, InPost and PostNord. Requires SmartyParcel Locator API.
* Added checking for manage_woocommerce permissions for admin routes (Patchstack issue).
* Remove legacy Tools page.

= Version 1.22.5 / (07.09.2026) =
* Restored default value for the active carriers option.

= Version 1.22.4 / (07.09.2026) =
* Added InPost Service Point delivery shipping method.
* Added InPost Address delivery shipping method.
* Implemented Pickup Points selection for InPost (Poland).

= Version 1.22.3 / (01.09.2026) =
* Improved order sync mechanism.

= Version 1.22.2 / (28.08.2026) =
* Added PostNord Service Point delivery shipping method.
* Added PostNord Address delivery shipping method.
* Implemented SmartyParcel Locator (PUDO search) for PostNord carrier.

= Version 1.22.1 / (26.08.2026) =
* Improved telemetry data.
* Improved translations for a feedback form.

= Version 1.22.0 / (23.08.2026) =
* Improved and refactored options UI.
* Legacy plugin settings are now migrated to platform shipping rules.
* Improved bulk label creation.
* Fixed checkout js issue.

= Version 1.21.13 / (14.08.2026) =
* Increased php version requirement to 8.0.
* Restored text-domain localization.
* Improved Add Tracking widget (added more carriers).
* Fixed cost-view-only option issue for rates calculation.
* Fixed patchstack vulnerability issue.
* Checked compatibility with the latest WordPress and WooCommerce versions.

= Version 1.21.12 / (16.07.2026) =
* Added DPD tracking.
* Added PostNord tracking.
* Replaced sending full state name instead of code on label creation.
* Improve Dashboard and billing UI.

= Version 1.21.11 / (03.07.2026) =
* Added sync orders endpoint (pull model).
* Added additional data to label requests.
* Checked compatibility with latest WordPress and WooCommerce versions.

= Version 1.21.10 / (16.06.2026) =
* [Labels] Fixed loading default carrier account.
* Removed extra meta keys and translated shipping meta keys at order edit page.
* Checked compatibility with latest WordPress and WooCommerce versions.

= Version 1.21.9 / (02.06.2026) =
* [Nova Poshta] Added new option "Use dimensions for rates calculation"
* [UkrPoshta] Added new option "Use dimensions for rates calculation"

= Version 1.21.8 / (27.05.2026) =
* Plugin is now using only SmartyParcel addresses to create shipments.
* Improved SmartyParcel admin elements.
* [Rates] Fixed bug with incorrect calculation when combine poshtomats option is active.
* Checked compatibility with latest WordPress and WooCommerce versions.

= Version 1.21.7 / (06.04.2026) =
* Added max weight columns to Nova Poshta warehouses table.
* Added new filter - wcus_pudo_points_request. Allows overriding search warehouses request.
* Added basic order sync with SmartyParcel.

= Version 1.21.6 / (26.02.2026) =
* [Rozetka Delivery] New option - Default shipping payer.
* [Nova Poshta] Fix payment control hook call.
* [Automation] Improve order status change action.

= Version 1.21.5 / (22.02.2026) =
* International shipping improvements.

= Version 1.21.4 / (21.02.2026) =
* [UkrPoshta] Added ability to create international shipping labels.
* [Automation] Added auto-creating label feature for UkrPoshta and Rozetka Delivery.
* Label auto creation now work asynchronously (wp cron).

= Version 1.21.3 / (16.02.2026) =
* Improved automation logic.

= Version 1.21.2 / (15.02.2026) =
* [Nova Poshta] Added multiple sender addresses support.
* [Automation] Added new event - Order status changed.
* [Automation] Added new action - Automatic label creation. Now supports only for Nova Poshta.
* Checked compatibility with latest WordPress and WooCommerce versions.

= Version 1.21.1 / (29.01.2026) =
* UI / UX upgrade.

= Version 1.21.0 / (23.01.2026) =
* [Meest] Added full flow integration (creating labels, tracking).
* [Rozetka Delivery] Old label form was replaced with SmartyParcel Elements.
* [Ukrposhta] Added UkrPoshta Address shipping method.
* Added request caching for SmartyParcel Rates API.
* [Nova Poshta] Added address notes field when creating shipment to address.