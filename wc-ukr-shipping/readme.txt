=== WC Ukraine Shipping - Integration of Nova Poshta and Ukrposhta for WooCommerce ===
Contributors: kirillbdev
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl.html
Tags: nova poshta, нова пошта, укрпошта, rozetka delivery, shipping
Requires PHP: 7.4
Tested up to: 6.8
Stable tag: 1.16.2

Connect Nova Poshta, Ukrposhta or international delivery services with your store. Create labels, track orders and calculate rates in one place.

== Description ==

Connect Nova Poshta, Ukrposhta or international delivery services with your store. Create labels, track orders and calculate rates in one place.

[Documentation](https://kirillbdev.pro/docs/wcus-base-setup/)
[PRO features](https://kirillbdev.pro/wc-ukr-shipping-pro/?utm_source=wporg)

== Installation and setup tutorial ==

https://www.youtube.com/watch?v=K_NFNQ1qkko

== Features ==

* Simple and intuitive setup
* Ability to select Nova Poshta warehouse, doors or poshtomat on checkout page
* Ability to select Ukrposhta warehouse on checkout page
* Ability to select Rozetka Delivery warehouse on checkout page
* Ability to select Nova Post (Europe) warehouse on checkout page
* Ability to set up fixed shipping cost
* Ability to calculate cost without adding it to order total
* Ability to create Nova Poshta TTN (warehouse-warehouse, warehouse-doors, warehouse-poshtomat)
* Ability to print Nova Poshta labels (A4, marking 85x85, marking 100x100 zebra)
* Ability to create Ukrposhta TTN (warehouse-warehouse)
* Ability to print Ukrposhta labels (100x100, 100x100 (A4), 100x100 (A5))
* Ability to track parcels
* Support many functions includes COD and Payment control
* Advanced shipment analytics
* Integration with popular plugins for localization: WPML and Polylang

== Premium features ==

WC Ukraine Shipping has additional premium features that help you to optimize your daily shipping routine.

* Unlimited shipments
* Unlimited carrier accounts
* Automatic calculation of shipping costs via carrier (Smart Rates API)
* Shipping costs calculation based on order total
* Ability to separate cost calculation for address shipping
* Ability to bulk generation of shipping labels in one click
* Tracking shipment statuses
* Automatic email notifications after generating TTN
* Automatic sms notifications after generating TTN (with our friendly plugin)
* Premium support

[Switch to SmartyParcel Advanced](https://kirillbdev.pro/wc-ukr-shipping-pro/?utm_source=wporg)

== Installation ==

= Minimum Requirements =

* PHP 7.4 or greater is recommended
* MySQL 5.7 or greater is recommended

= Automatic installation =

Automatic installation is the easiest option as WordPress handles the file transfers itself and you don’t need to leave your web browser. To do an automatic install of plugin, log in to your WordPress dashboard, navigate to the Plugins menu and click Add New.

In the search field type “WC Ukr Shipping” and click Search Plugins. Once you’ve found it you can view details about it such as the point release, rating and description. Most importantly of course, you can install it by simply clicking “Install Now”.

= Manual installation =

The manual installation method involves downloading this plugin and uploading it to your webserver via your favourite FTP application. The WordPress codex contains instructions on how to do this here.

= Updating =

Automatic updates should work like a charm; as always though, ensure you backup your site just in case.

== FAQ ==

= Does plugin supports WooCommerce checkout blocks? =

Unfortunately plugin doesn't support checkout blocks yet.

== Changelog ==

= Version 1.16.2 / (08.07.2025) =
* Code quality improvements.

= Version 1.16.1 / (02.07.2025) =
* Fixed PHP 8.2 deprecation notice.
* Code quality improvements.

= Version 1.16.0 / (01.07.2025) =
* [New] Rozetka Delivery shipping method.
* [New] Nova Post (EU) shipping method.
* Added ability to attach exist label to order.
* Added displaying tracking number at my account page (orders).
* [Fixed] Ukrposhta batch label creation.
* Carrier account management was removed from plugin and moved to cloud account panel.
* Checked compatibility with latest WordPress and WooCommerce versions.

= Version 1.15.11 / (19.06.2025) =
* [Ukrposhta] Fixed sending incorrect service type.

= Version 1.15.10 / (19.06.2025) =
* Fixed translates for middle name field.
* Added escaping in translates section.

= Version 1.15.9 / (18.06.2025) =
* [Ukrposhta] Removed trailing slash when search for some settlements.
* [Ukrposhta] Added COD payer, added ability to set up default service options.
* Added option for inject additional fields in checkout.
* Restored wcus_ttn_form_additional filter.
* Checked compatibility with latest Wordpress and WooCommerce versions.

= Version 1.15.8 / (13.06.2025) =
* [Hotfix] Empty zones behavior
* Added CSS versioning for checkout.

= Version 1.15.6 / (13.06.2025) =
* [Hotfix] Array unique case in checkout.

= Version 1.15.5 / (12.06.2025) =
* [Nova Poshta] Added ability to create label with global params.
* [Nova Poshta] Added usage of default description option on bulk label creation.
* [Nova Poshta] Fixed label creation for poshtomats on bulk label creation.
* [Ukrposhta] Fixed spread operator backward compatibility with PHP 7.4.
* [Ukrposhta] Added new option - default payer.
* Added ability to create labels for any orders.
* Fixed critical error in some cases on Order list and Edit checkout pages.
* [Tracking] Improved signature comparing.
* Checkout improvements.

= Version 1.15.4 / (02.06.2025) =
* [Ukrposhta] Ability to create labels from private entrepreneur.
* [Ukrposhta] Ability to create labels with COD.
* [Ukrposhta] Added default sender city and warehouse options.
* [Ukrposhta] Now supports cloud tracking API.
* [Ukrposhta] Added automation rules (carrier status changed event etc.).
* Added new hook wcus_shipping_label_created.
* Fixed internal server error for Ukrposhta shipping method in checkout.

= Version 1.15.3 / (28.05.2025) =
* Restored previous city format (Sales Drive compatibility).
* [Tracking] Status notifications now work via webhooks.
* [Tools] Added skipping duplicated ttn on sync with WC Ukraine Shipping PRO (legacy plugin).

= Version 1.15.2 / (27.05.2025) =
* [Hotfix] Internal server error on checkout when Ukrposhta sender city not selected.

= Version 1.15.1 / (24.05.2025) =
* Fixed a critical error for some cases on the order edit page.

= Version 1.15.0 / (24.05.2025) =
* Added Ukrposhta integration (select warehouse, create TTN, calculate rates).
* Legacy checkout UI was fully removed.
* Improved Nova Poshta cities search.
* Architecture improvements.

= Version 1.14.5 / (18.05.2025) =
* Change default values for some options.
* Improve UI / UX on creating labels.

= Version 1.14.4 / (14.05.2025) =
* Remove PHP 8.3 deprecation notices.
* Fixed critical issues when WooCommerce is disabled.
* Architecture and UI improvements.

= Version 1.14.3 / (30.04.2025) =
* [SmartyParcel] Added more readable errors for Bad Request responses.
* [SmartyParcel] Added the ability to update carrier account credentials.
* Added tools to migrate from WC Ukraine Shipping PRO.

= Version 1.14.2 / (17.04.2025) =
* Removed duplicated payment control option.
* Improved cron workers.
* Checked compatibility with latest Wordpress and WooCommerce versions.

= Version 1.14.1 / (14.04.2025) =
* [SmartyParcel] Added batch label generation feature.
* Added automation feature (no-code business rule constructor).
* [Checkout] Added placeholders to house and flat fields.
* Checked compatibility with latest Wordpress and WooCommerce versions.

= Version 1.14.0 / (08.04.2025) =
* Added ship to doors feature
* Implement SmartyParcel Rates API (Advanced plan only)
* Implement SmartyParcel Tracking API (Advanced plan only)
* Removed jQuery deprecated methods from checkout.js
* Checked compatibility with latest Wordpress and WooCommerce versions.

= Version 1.13.5 / (09.03.2025) =
* Added COD feature
* Added Payment Control feature
* Added shipping payer features: shipping payer and payment method
* Added dimensions support and default dimensions option
* Checked compatibility with latest Wordpress and WooCommerce versions

= Version 1.13.4 / (03.03.2025) =
* [Fixed] PHP 8.2+ deprecation notices

= Version 1.13.3 / (03.03.2025) =
* [Fixed] Migrator PHP 8.2+ deprecation notice
* Checked compatibility with latest Wordpress and WooCommerce versions.

= Version 1.13.2 / (13.02.2025) =
* [Feature] Nova Poshta labels printing (A4, 85x85, 100x100 zebra).
* Added sender default city/warehouse option.
* [Fix] Some UI errors on TTN form page.

= Version 1.13.1 / (06.02.2025) =
* [Hotfix] Fatal error on server with PHP 7.4

= Version 1.13.0 / (06.02.2025) =
* Now the plugin allows you to create TTN using Smarty Parcel service (our new service). At the moment, limited capabilities from the PRO version are implemented. The functionality will be expanded with every new releases.
* [Checkout] Improved UX and usability.
* Checked compatibility with latest Wordpress and WooCommerce versions.

= Version 1.12.7 / (24.10.2024) =
* Architecture improvements.
* Checked compatibility with latest Wordpress and WooCommerce versions.

= Version 1.12.6 / (04.10.2024) =
* Checkout process and performance improvements.
* Added new option "Calculate shipping cost view only". Allows you to calculate shipping rates but without adding it to order total.
* Checked compatibility with latest Wordpress and WooCommerce versions.

= Version 1.12.5 / (12.09.2024) =
* [Feature] Migration mechanism V2.
* Checked compatibility with latest Wordpress and WooCommerce versions.

= Version 1.12.4 / (29.06.2024) =
* [Fix] Removed extra slashes when saving shipping address.
* Checked compatibility with latest Wordpress and WooCommerce versions.

= Version 1.12.3 / (26.04.2024) =
* Divided search of warehouses and poshtomats.
* [New UI] Saving Nova Poshta area when order placed.
* Some localization improvements.
* Checked compatibility with latest Wordpress and WooCommerce versions.

= Version 1.12.2 / (02.03.2024) =
* [Fix] Broken ukrainian localization.
* [Feature] Trimming spaces for search cities/warehouses query.
* Checked compatibility with latest Wordpress and WooCommerce versions.

= Version 1.12.1 / (19.10.2023) =
* Added HPOS support.

= Version 1.12.0 / (09.07.2023) =
* Improved warehouse data loading performance.
* [Checkout old] Removed country check as required condition for show shipping fields.
* Checked compatibility with latest Wordpress and WooCommerce versions.

= Version 1.11.3 / (25.09.2022) =
* Improved integration with migration plugins.
* Improved plugin localization.

= Version 1.11.2 / (07.06.2022) =
* Remove old files from vcs.

= Version 1.11.1 / (07.06.2022) =
* Плановые улучшения кодовой базы.
* Плановые улучшения производительности.

= Version 1.11.0 / (08.04.2022) =
* Минимальная версия PHP увеличена до 7.4
* Новый UI теперь включен по-умолчанию для всех новых магазинов.
* Улучшено описание некоторых настроек.
* Проведены мелкие улучшения производительности.

= Version 1.10.0 / (12.12.2021) =
* Плановые улучшения и переработки кодовой базы.

= Version 1.9.1 / (29.09.2021) =
* Мелкие исправления.

= Version 1.9.0 / (29.09.2021) =
* Обновлен модуль загрузки отделений (аналогичен PRO версии).
* Восстановлена опция сохранения последнего отделения (работает только с новым UI).
* Улучшена синхронизация данных на странице оформления заказа (новый UI).
* Улучшения архитектуры и кодовой базы.

= Version 1.8.2 / (01.08.2021) =
* Backend UI rework.

= Version 1.8.1 / (01.08.2021) =
* Улучшена отзывчивость нового UI на мобильных устройствах.
* Увеличен базовый таймаут запросов к API Новой Почты до 15 секунд.
* Интеграция работы опции "Включить блок Адресной доставки" для нового UI.

= Version 1.8.0 / (16.06.2021) =
* Внедрен новый UI для страницы оформления заказа (включается во вкладке Общие). Подробное описание его работы: https://kirillbdev.pro/wcus-pro-new-ui-110/
* Добавлена новая вкладка Доставка для страницы настроек (для более логического разделения настроек).
* Добавлен фильтр wcus_checkout_i18n (будет описан в документации).
* Core update.

= Version 1.7.7 / (17.05.2021) =
* Исправлена ошибка при загрузке областей на странице оформления заказа.

= Version 1.7.6 / (16.05.2021) =
* Core update.

= Version 1.7.5 / (07.05.2021) =
* Основная логика была вынесена в отдельное ядро (которое также используется для PRO версии).
* Добавлен фильтр wcus_checkout_validation_active. С помощью него можно отключить валидацию полей плагина на странице оформления заказа.
* Добавлен фильтр wcus_dynamic_shipping_label (будет описан в документации).
* Теперь для корректной работы плагина требуется PHP не ниже версии 7.0.
* Улучшения безопасности и производительности.

= Version 1.7.4 / (05.02.2021) =
* Исправлена ошибка сохранения адреса доставки на украинском языке, даже если в настройках выбран русский.
* Добавлена опция "Показывать почтоматы" (включена по-умолчанию).
* Добавлен фильтр wcus_get_areas.
* Улучшения локализации админ-части.
* Общие улучшения производительности.

= Version 1.7.3 / (24.12.2020) =
* Исправлены некоторые несовместимости с PHP8.
* Добавлен фильтр: wcus_http_post_timeout. Позволяет увеличить таймаут опроса API при загрузке отделений.

= Version 1.7.2 / (11.12.2020) =
* Исправлен некорректный вывод названия доставки, если оно содержит кавычки.
* Добавлен вывод стоимости доставки на странице корзины.
* Исправлены некоторые ошибки несовместимости с плагином Saphali Woocommerce.
* Добавлена локализация сообщения о незаполненных данных Новой Почты.

= Version 1.7.1 / (29.09.2020) =
* Исравлен баг, когда плагин при любом выбранном методе доставки добавлял свою стоимость к итоговой сумме заказа.

= Version 1.7.0 / (06.09.2020) =
* Глобальные работы по оптимизации кода (улучшения скорости работы, удаление старого и ненужного кода).
* Полная смена логики сохранения заказа (по аналогии с PRO версией).
* Новые возможности для расширения расчета стоимости доставки (по аналогии с PRO версией).
* Убрана настройка "Сохранять последнее отделение пользователя" (в связи с подготовкой нового функционала).
* Убрана кнопка "Наш сайт" с верней панели настроек.
* Новая, улучшенная обработка ошибок, которые могут возникать при первоначальных настройках плагина (вывод ошибок API, ошибок доступности API).

= Version 1.6.3 / (09.05.2020) =
* Корректировка вывода верстки на странице оформления заказа.

= Version 1.6.2 / (09.05.2020) =
* Небольшие исправления подгрузки переводов.

= Version 1.6.1 / (09.05.2020) =
* Интеграция локализации отделений (русский и украинский) с плагином Polylang.
* Внедрена опция "Использовать новые UI компоненты". По-умолчанию эта опция включена. Опция должна решить частую проблему несовместимости с плагином WooCommerce Checkout Manager.
* Блок выбора отделения доставки теперь функционирует как для billing секции, так и для shipping (доставка по другому адресу) секции. Следовательно, теперь плагин пишет данные адреса заказа в одну из этих секций.
* Исправлены некоторые мелкие баги, а также проведен плановый рефакторинг кодовой базы.

= Version 1.6.0 / (22.04.2020) =
* Исправлена ошибка монопольной установки отделения Новой Почты, даже, если для заказа выбран другой способ доставки.
* Теперь поле произвольного адреса не убирает выбор области и города (соответственно, для адресной доставки эти поля теперь необходимы к заполнению).
* Сохранение последнего отделения доставки для пользователя теперь можно опционально отключить в настройках плагина.
* Добавлена CSRF защита на странице оформления заказа.
* Добавлены базовы файлы переводов для 3-х языков (русский, украинский, английский). Домен локализации: wc-ukr-shipping-l10n
* Добавлена настройка выбора типа переводов, которые будет использовать плагин (из mo файлов или из вкладки "Переводы"). Соответственно, улучшилась интеграция с плагинами переводов типа WPML, Polylang. Если ваш сайт не имеет языковых версий, то значение данной настройки можно оставить без изменений. Если же у вас несколько языковых версий, то установите значение опции в "из mo файлов". Это даст возможность получить переводы для 3-х базовых языков. Также, с помощью соответствующих плагинов (например WPML String Translations), вы сможете изменять данные переводы (домен локализации плагина: wc-ukr-shipping-l10n).
* Добавлен перевод для пустого результата поиска в полях выбора области / города / отделения (ранее был "No results found").
* Добавлена автоматическая интеграция языка отделений (русский, украинский) с плагином WPML.