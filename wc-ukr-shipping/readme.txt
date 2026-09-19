=== SmartyParcel – Multi-Carrier Shipping, Pickup Points & Labels for WooCommerce ===
Contributors: kirillbdev
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Tags: shipping, pickup points, parcel locker, shipping label, inpost
Requires PHP: 8.0
Tested up to: 7.1
Stable tag: 1.23.0

Pickup points, live rates and bulk shipping labels for InPost, PostNord, GLS, DHL, Nova Post, Nova Poshta, Ukrposhta and Meest in WooCommerce.

== Description ==

**SmartyParcel is a full-cycle shipping platform for WooCommerce.** One integration covers the entire delivery cycle: the customer picks a pickup point or parcel locker at checkout, the carrier returns a live shipping rate, you create the shipping label in one click, and every parcel is tracked back into the order — inside WooCommerce admin, without a carrier back office open in a second tab.

Instead of installing a separate plugin for every courier, you enable the carriers your store actually ships with and run them from one place: one checkout experience, one label queue, one tracking history, one set of automation rules.

[Product Overview](https://smartyparcel.com/woocommerce-integration/?utm_source=wporg)
[Supported carriers](https://smartyparcel.com/supported-carriers/?utm_source=wporg)
[Documentation](https://smartyparcel.com/docs/knowledge-base-woocommerce/?utm_source=wporg)

**Example: setting up PostNord pickup points in the WooCommerce checkout.**

https://www.youtube.com/watch?v=UIfRYDeO9Lo

= Supported carriers =

Every carrier below is a native integration — a real API connection, not a generic tracking-number lookup.

* **InPost (Poland)** — service point selection at checkout with map search, courier delivery to address, parcel tracking.
* **PostNord (Sweden, Denmark, Norway, Finland)** — service points and parcel boxes at checkout with map search, delivery to address, parcel tracking.
* **Nova Post (Europe and international)** — pickup points with map search, live rates, shipping labels, cash on delivery, tracking.
* **Nova Poshta (Ukraine, Нова Пошта)** — pickup points and poshtomats with map search, courier delivery, live rates, single and bulk shipping labels, cash on delivery, payment control, tracking.
* **Ukrposhta (Ukraine, Укрпошта)** — pickup points, delivery to address, live rates, domestic and international shipping labels, cash on delivery, tracking.
* **Meest (international and Ukraine)** — pickup points, delivery to address, live rates, shipping labels, tracking.
* **Nova Global (international)** — delivery to address, live rates, shipping labels, tracking.
* **Rozetka Delivery (Ukraine)** — pickup points, shipping labels, tracking.
* **DHL, DPD, GLS, FedEx** — automatic parcel tracking by tracking number, with status sync into WooCommerce orders.

Label creation for InPost, PostNord, DHL and DPD is in active development. The [supported carriers page](https://smartyparcel.com/supported-carriers/?utm_source=wporg) always shows the current state of every integration.

= Pickup points and parcel lockers at checkout =

* Pickup point, parcel locker and service point selection built into the WooCommerce checkout — searchable list or interactive Google map.
* Separate shipping methods per delivery type, so "Courier to address" and "To pickup point" are two distinct options in the WooCommerce shipping zone.
* Points are loaded live from the carrier network, with opening hours, addresses and parcel size limits where the carrier provides them.
* Works with WooCommerce shipping zones, so each country gets exactly the carriers you ship there with.

= Shipping labels and bulk fulfillment =

* Create domestic and international shipping labels from the WooCommerce order screen — the order data is already filled in.
* Bulk label creation and printing: select dozens or hundreds of orders and process them in one run.
* Cash on delivery, declared value, customs declaration fields and multiple sender addresses.
* Rule-based automation: create labels automatically on order status change, on payment, or on your own conditions.

= Live shipping rates =

* Real-time rates pulled from the carrier for the customer's destination, cart weight and dimensions.
* Or fixed and rule-based costs when you would rather control the price yourself.
* Rates are cached and calculated per shipping method, so checkout stays fast.

= Parcel tracking and customer notifications =

* Tracking numbers are attached to the order automatically when a label is created, or added manually for shipments made elsewhere.
* Automatic status synchronization keeps WooCommerce order statuses in line with the carrier.
* Email and SMS notifications to the customer on status changes.

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

SmartyParcel natively integrates InPost (Poland), PostNord (Sweden, Denmark, Norway, Finland), Nova Post (Europe), Nova Poshta, Ukrposhta and Rozetka Delivery (Ukraine), Meest and Nova Global. Parcels sent with DHL, DPD, GLS and FedEx are tracked by tracking number. Supported features differ per carrier and are listed on the supported carriers page.

= Does SmartyParcel work with InPost in Poland? =

Yes. InPost service points are selectable directly in the WooCommerce checkout, from a searchable list or on a map, and InPost parcels are tracked automatically with order status sync. InPost label creation is in development.

= Does SmartyParcel work with PostNord in Sweden, Denmark, Norway and Finland? =

Yes. PostNord service points and parcel boxes are selectable in the WooCommerce checkout, delivery to address is available as a separate shipping method, and PostNord parcels are tracked automatically. PostNord label creation is in development.

= Can I track DHL, DPD, GLS and FedEx parcels in WooCommerce? =

Yes. Add the tracking number to the WooCommerce order and SmartyParcel pulls carrier statuses automatically, syncs them with the order status and notifies the customer. This works for DHL, DPD, GLS and FedEx shipments created anywhere, including outside the plugin.

= How do customers choose a pickup point or parcel locker at checkout? =

The plugin adds a pickup point selector to the WooCommerce checkout for every enabled carrier. Customers search the carrier network by city or address and pick a pickup point, parcel locker or service point; the selected point is saved with the order and used when the shipping label is created.

= Can customers select a pickup point on a map? =

Yes. The pickup point selector has a Google Maps mode with search by city and street, currently available for Nova Poshta, InPost and PostNord. It requires a Google Maps API key, which is set in the plugin settings.

= Can I create and print shipping labels in bulk? =

Yes. Select any number of WooCommerce orders and create and print their shipping labels in one run, instead of opening orders one by one. Labels can also be generated automatically by rules — on order status change, on payment, or on your own conditions.

= Does SmartyParcel create Nova Poshta and Ukrposhta waybills (ТТН)? =

Yes. Waybills (ТТН) for Nova Poshta and Ukrposhta are created straight from the WooCommerce order screen or in bulk, including international Ukrposhta shipments, with cash on delivery, declared value and customs data filled from the order.

= Do I need my own carrier account or contract? =

Yes — you connect your own carrier accounts and contracts in the SmartyParcel dashboard, so you keep your own carrier prices and delivery conditions. The number of carrier accounts you can connect depends on your plan.

= Does the plugin support cash on delivery? =

Yes. Cash on delivery is supported for Nova Poshta, Nova Post, Ukrposhta and Meest, including control of who pays the delivery and the COD fee, and the COD amount can be included in the shipping cost shown at checkout.

= Is there a free plan available for SmartyParcel? =

Yes — the plugin is free to install and the SmartyParcel account has a lifetime Free Plan, with no upfront commitment and no credit card required. Pickup point and parcel locker selection at checkout is free. Shipping labels work pay-as-you-go: the Free Plan covers a quota of 30 shipments per month, with a per-label fee on top of it. Live rates through the Rates API, higher volumes and automation are available on paid plans — see the [pricing page](https://smartyparcel.com/app-pricing/?utm_source=wporg).

= Can I use SmartyParcel solely for order tracking? =

Yes. If you already create shipping labels in other tools or directly with carriers, SmartyParcel can run as a centralized tracking engine only: real-time status updates, automated status syncing in WooCommerce and customer notifications. We offer standalone tracking plans for this use case.

= Can I use SmartyParcel solely for checkout pickup point selection and live rate calculations? =

Yes. You can use SmartyParcel strictly to improve the WooCommerce checkout with the pickup point locator and real-time shipping rates, without creating labels through the platform. Dedicated pricing and modular settings are available for stores that only need checkout and rate calculation.

= Does the plugin support WooCommerce HPOS? =

Yes. The plugin is fully compatible with WooCommerce High-Performance Order Storage (custom order tables) and with the legacy order storage.

= Does the plugin work with WPML, Polylang and multi-currency stores? =

Yes. The plugin is compatible with WPML and Polylang, and works in multi-currency stores. Carrier data at checkout is shown in the language configured for the carrier.

= Does the plugin support WooCommerce checkout blocks? =

Unfortunately, the plugin doesn't support checkout blocks yet, but we are working on it.

== Changelog ==

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