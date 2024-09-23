<?php

namespace kirillbdev\WCUkrShipping\Modules\Frontend;

use kirillbdev\WCUkrShipping\Helpers\WCUSHelper;
use kirillbdev\WCUkrShipping\Services\TranslateService;
use kirillbdev\WCUkrShipping\Traits\StateInitiatorTrait;
use kirillbdev\WCUSCore\Contracts\ModuleInterface;

if ( ! defined('ABSPATH')) {
    exit;
}

class AssetsLoader implements ModuleInterface
{
    use StateInitiatorTrait;

    private TranslateService $translateService;

    public function __construct(TranslateService $translateService)
    {
        $this->translateService = $translateService;
    }

    public function init()
    {
        add_action('wp_head', [ $this, 'loadCheckoutStyles' ]);
        add_action('wp_head', [ $this, 'initState' ]);
        add_action('wp_enqueue_scripts', [ $this, 'loadFrontendAssets' ]);
    }

    public function loadFrontendAssets()
    {
        if (!wc_ukr_shipping_is_checkout()) {
            return;
        }

        wp_enqueue_style(
            'wc_ukr_shipping_css',
            WC_UKR_SHIPPING_PLUGIN_URL . 'assets/css/style.min.css'
        );

        if ((int)get_option('wcus_checkout_new_ui')) {
            wp_enqueue_script(
                'wcus_checkout_js',
                WC_UKR_SHIPPING_PLUGIN_URL . 'assets/js/checkout2.min.js',
                [ 'jquery' ],
                filemtime(WC_UKR_SHIPPING_PLUGIN_DIR . 'assets/js/checkout2.min.js'),
                true
            );
        } else {
            wp_enqueue_script(
                'wcus_checkout_js',
                WC_UKR_SHIPPING_PLUGIN_URL . 'assets/js/checkout.min.js',
                ['jquery'],
                filemtime(WC_UKR_SHIPPING_PLUGIN_DIR . 'assets/js/checkout.min.js'),
                true
            );
        }

        $this->injectGlobals();
    }

    public function loadCheckoutStyles()
    {
        if (!wc_ukr_shipping_is_checkout()) {
            return;
        }

        ?>
      <style>
          .wc-ukr-shipping-np-fields {
              padding: 1px 0;
          }

          .wcus-state-loading:after {
              border-color: <?= get_option('wc_ukr_shipping_spinner_color', '#dddddd'); ?>;
              border-left-color: #fff;
          }
      </style>
        <?php
    }

    private function injectGlobals()
    {
        $translator = $this->translateService;
        $translates = $translator->getTranslates();

        $globals = [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'homeUrl' => home_url(),
            'lang' => $translator->getCurrentLanguage(),
            'nonce' => wp_create_nonce('wc-ukr-shipping'),
            'disableDefaultBillingFields' => apply_filters('wc_ukr_shipping_prevent_disable_default_fields', false) === false
                ? 1
                : 0,
            'options' => [
                'address_shipping_enable' => (int)wc_ukr_shipping_get_option('wc_ukr_shipping_address_shipping')
            ]
        ];

        if ((int)get_option('wcus_checkout_new_ui')) {
            $globals['default_cities'] = $this->getDefaultCities();
            $globals['i18n'] = [
                'fields_title' => __('Select shipping address', 'wc-ukr-shipping-i18n'),
                'shipping_type_warehouse' => __('to warehouse', 'wc-ukr-shipping-i18n'),
                'shipping_type_doors' => __('to doors', 'wc-ukr-shipping-i18n'),
                'shipping_type_poshtomat' => __('to the poshtomat', 'wc-ukr-shipping-i18n'),
                'ui' => [
                    'city_placeholder' => __('Select city', 'wc-ukr-shipping-i18n'),
                    'warehouse_placeholder' => __('Select warehouse', 'wc-ukr-shipping-i18n'),
                    'poshtomat_placeholder' => __('Select poshtomat', 'wc-ukr-shipping-i18n'),
                    'custom_address_placeholder' => __('Enter address', 'wc-ukr-shipping-i18n'),
                    'text_search' => __('Enter value for search', 'wc-ukr-shipping-i18n'),
                    'text_loading' => __('Loading...', 'wc-ukr-shipping-i18n'),
                    'text_more' => __('Load more', 'wc-ukr-shipping-i18n'),
                    'text_not_found' => __('Nothing found', 'wc-ukr-shipping-i18n'),
                    'text_more_chars' => __('Enter more chars', 'wc-ukr-shipping-i18n'),
                ]
            ];

            $globals['i18n'] = array_replace_recursive(
                $globals['i18n'],
                apply_filters('wcus_checkout_i18n', $globals['i18n'], $translator->getCurrentLanguage())
            );
        } else {
            $globals['i10n'] = [
                'placeholder_area' => $translates['placeholder_area'],
                'placeholder_city' => $translates['placeholder_city'],
                'placeholder_warehouse' => $translates['placeholder_warehouse'],
                'not_found' => $translates['not_found']
            ];
        }

        wp_localize_script('wcus_checkout_js', 'wc_ukr_shipping_globals', $globals);
    }

    private function getDefaultCities()
    {
        $locale = preg_replace(
            '/_.+$/',
            '',
            is_admin() ? get_user_locale() : $this->translateService->getCurrentLanguage()
        );

        if ($locale === 'uk') {
            $locale = 'ua';
        }

        return array_map(function($item) use($locale) {
            return [
                'name' => $item[$locale === 'ua' ? 'description' : 'description_ru'],
                'value' => $item['ref']
            ];
        }, WCUSHelper::getDefaultCities());
    }
}
