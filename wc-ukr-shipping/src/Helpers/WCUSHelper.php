<?php

namespace kirillbdev\WCUkrShipping\Helpers;

if ( ! defined('ABSPATH')) {
    exit;
}

class WCUSHelper
{
    /**
     * @param \WC_Order $order
     *
     * @return \WC_Order_Item_Shipping
     */
    public static function getOrderShippingMethod($order)
    {
        $shippingMethods = $order->get_shipping_methods();

        if (empty($shippingMethods)) {
            return null;
        }

        return array_shift($shippingMethods);
    }

    public static function hasChosenShippingMethodInstance(\WC_Shipping_Method $instance): bool
    {
        try {
            $methods = WC()->session->get( 'chosen_shipping_methods', []);

            return in_array($instance->id . ':' . $instance->instance_id, $methods, true);
        } catch (\Throwable $e) {
            return false;
        }
    }

    public static function hasChosenShippingMethod(string $method): bool
    {
        return in_array($method, wc_get_chosen_shipping_method_ids(), true);
    }

    public static function getChosenShippingMethods(): array
    {
        if ( ! is_callable([WC()->session, 'get' ])) {
            return [];
        }

        return WC()->session->get('chosen_shipping_methods', []);
    }

    public static function preparePhone(string $phone): string
    {
        $phone = str_replace(['+', '-', ' ', '(', ')'], '', $phone);

        if (preg_match('/^\d{12,12}$/', $phone)) {
            return $phone;
        }

        if (preg_match('/^\d{10,10}$/', $phone)) {
            return '38' . $phone;
        }

        if (preg_match('/^\d{11,11}$/', $phone)) {
            return '3' . $phone;
        }

        return $phone;
    }

    public static function prepareUIString($str)
    {
        return wp_unslash(wp_specialchars_decode($str, ENT_QUOTES));
    }

    public static function getSelectNextOption(string $key): array
    {
        $option = json_decode(wc_ukr_shipping_get_option($key) ?? '', true);

        return empty($option) ? [
            'value' => '',
            'name' => '',
        ] : $option;
    }

    public static function safeGetJsonOption(string $key, $default = []): array
    {
        $option = json_decode(wc_ukr_shipping_get_option($key) ?? '', true);

        return array_replace_recursive($default, empty($option) ? [] : $option);
    }

    public static function getDefaultCities()
    {
        return [
            [
                'ref' => '8d5a980d-391c-11dd-90d9-001a92567626',
                'description' => 'Київ',
                'description_ru' => 'Киев',
            ],
            [
                'ref' => 'db5c88e0-391c-11dd-90d9-001a92567626',
                'description' => 'Харків',
                'description_ru' => 'Харьков',
            ],
            [
                'ref' => 'db5c88f0-391c-11dd-90d9-001a92567626',
                'description' => 'Дніпро',
                'description_ru' => 'Днепр',
            ],
            [
                'ref' => 'db5c88c6-391c-11dd-90d9-001a92567626',
                'description' => 'Запоріжжя',
                'description_ru' => 'Запорожье',
            ],
            [
                'ref' => 'db5c88d0-391c-11dd-90d9-001a92567626',
                'description' => 'Одеса',
                'description_ru' => 'Одесса',
            ],
            [
                'ref' => 'db5c88f5-391c-11dd-90d9-001a92567626',
                'description' => 'Львів',
                'description_ru' => 'Львов',
            ],
            [
                'ref' => 'db5c890d-391c-11dd-90d9-001a92567626',
                'description' => 'Кривий Ріг',
                'description_ru' => 'Кривой Рог',
            ],
            [
                'ref' => 'db5c888c-391c-11dd-90d9-001a92567626',
                'description' => 'Миколаїв',
                'description_ru' => 'Николаев',
            ],
            [
                'ref' => 'db5c897c-391c-11dd-90d9-001a92567626',
                'description' => 'Чернігів',
                'description_ru' => 'Чернигов',
            ],
            [
                'ref' => 'db5c88e5-391c-11dd-90d9-001a92567626',
                'description' => 'Суми',
                'description_ru' => 'Сумы',
            ],
            [
                'ref' => 'db5c88de-391c-11dd-90d9-001a92567626',
                'description' => 'Вінниця',
                'description_ru' => 'Винница',
            ],
            [
                'ref' => 'db5c8902-391c-11dd-90d9-001a92567626',
                'description' => 'Черкаси',
                'description_ru' => 'Черкассы',
            ],
            [
                'ref' => 'db5c88cc-391c-11dd-90d9-001a92567626',
                'description' => 'Херсон',
                'description_ru' => 'Херсон',
            ],
            [
                'ref' => 'db5c8892-391c-11dd-90d9-001a92567626',
                'description' => 'Полтава',
                'description_ru' => 'Полтава',
            ],
            [
                'ref' => 'db5c88c4-391c-11dd-90d9-001a92567626',
                'description' => 'Житомир',
                'description_ru' => 'Житомир',
            ],
            [
                'ref' => 'db5c8927-391c-11dd-90d9-001a92567626',
                'description' => 'Краматорськ',
                'description_ru' => 'Краматорск',
            ],
            [
                'ref' => 'db5c896a-391c-11dd-90d9-001a92567626',
                'description' => 'Рівне',
                'description_ru' => 'Ровно',
            ],
            [
                'ref' => 'db5c8904-391c-11dd-90d9-001a92567626',
                'description' => 'Івано-Франківськ',
                'description_ru' => 'Ивано-Франковск',
            ],
            [
                'ref' => '8d5a9813-391c-11dd-90d9-001a92567626',
                'description' => 'Кременчук',
                'description_ru' => 'Кременчуг',
            ],
            [
                'ref' => 'db5c8900-391c-11dd-90d9-001a92567626',
                'description' => 'Тернопіль',
                'description_ru' => 'Тернополь',
            ],
            [
                'ref' => 'db5c893b-391c-11dd-90d9-001a92567626',
                'description' => 'Луцьк',
                'description_ru' => 'Луцк',
            ],
            [
                'ref' => 'db5c88ce-391c-11dd-90d9-001a92567626',
                'description' => 'Біла Церква',
                'description_ru' => 'Белая Церковь',
            ],
            [
                'ref' => 'e221d642-391c-11dd-90d9-001a92567626',
                'description' => 'Чернівці',
                'description_ru' => 'Черновцы',
            ],
            [
                'ref' => 'db5c88ac-391c-11dd-90d9-001a92567626',
                'description' => 'Хмельницький',
                'description_ru' => 'Хмельницкий',
            ],
            [
                'ref' => 'db5c8914-391c-11dd-90d9-001a92567626',
                'description' => 'Кам\'янець-Подільський',
                'description_ru' => 'Каменец-Подольский',
            ],
        ];
    }

    public static function getUkrposhtaDefaultCities(): array
    {
        return [
            [
                'value' => '29713',
                'name' => 'м. Київ, Київ р-н, Київ обл.',
            ],
            [
                'value' => '24550',
                'name' => 'м. Харків, Харківський р-н, Харківська обл.',
            ],
            [
                'value' => '3641',
                'name' => 'м. Дніпро, Дніпровський р-н, Дніпропетровська обл.'
            ],
            [
                'value' => '8968',
                'name' => 'м. Запоріжжя, Запорізький р-н, Запорізька обл.'
            ],
            [
                'value' => "17069",
                'name' => 'м. Одеса, Одеський р-н, Одеська обл.',
            ],
            [
                'value' => '14288',
                'name' => 'м. Львів, Львівський р-н, Львівська обл.'
            ],
            [
                'value' => '4292',
                'name' => 'м. Кривий Ріг, Криворізький р-н, Дніпропетровська обл.'
            ],
            [
                'value' => '16169',
                'name' => 'м. Миколаїв, Миколаївський р-н, Миколаївська обл.'
            ],
            [
                'value' => '29712',
                'name' => 'м. Чернігів, Чернігівський р-н, Чернігівська обл.'
            ],
            [
                'value' => '21680',
                'name' => 'м. Суми, Сумський р-н, Сумська обл.'
            ],
            [
                'value' => '1057',
                'name' => 'м. Вінниця, Вінницький р-н, Вінницька обл.'
            ],
            [
                'value' => '27760',
                'name' => 'м. Черкаси, Черкаський р-н, Черкаська обл.'
            ],
            [
                'value' => '25448',
                'name' => 'м. Херсон, Херсонський р-н, Херсонська обл.'
            ],
            [
                'value' => '19234',
                'name' => 'м. Полтава, Полтавський р-н, Полтавська обл.'
            ],
            [
                'value' => '6708',
                'name' => 'м. Житомир, Житомирський р-н, Житомирська обл.'
            ],
            [
                'value' => '5925',
                'name' => 'м. Краматорськ, Краматорський р-н, Донецька обл.'
            ],
            [
                'value' => '20296',
                'name' => 'м. Рівне, Рівненський р-н, Рівненська обл.'
            ],
            [
                'value' => '9826',
                'name' => 'м. Івано-Франківськ, Івано-Франківський р-н, Івано-Франківська обл.'
            ],
            [
                'value' => '17802',
                'name' => 'м. Кременчук, Кременчуцький р-н, Полтавська обл.'
            ],
            [
                'value' => '22662',
                'name' => 'м. Тернопіль, Тернопільський р-н, Тернопільська обл.'
            ],
            [
                'value' => '3477',
                'name' => 'м. Луцьк, Луцький р-н, Волинська обл.'
            ],
            [
                'value' => '10472',
                'name' => 'м. Біла Церква, Білоцерківський р-н, Київська обл.'
            ],
            [
                'value' => '28188',
                'name' => 'м. Чернівці, Чернівецький р-н, Чернівецька обл.'
            ],
            [
                'value' => '26481',
                'name' => 'м. Хмельницький, Хмельницький р-н, Хмельницька обл.'
            ],
        ];
    }

    public static function getLabelDownloadFormats(?string $carrierSlug): array
    {
        if ($carrierSlug === null) {
            return [];
        }

        $validFormats = [
            'nova_poshta' => [
                'a4' => 'A4',
                'm85' => '85x85',
                'm100' => '100x100 (zebra)',
            ],
            'ukrposhta' => [
                's100' => '100x100',
                's100a4' => '100x100 (A4)',
                's100a5' => '100x100 (A5)',
            ],
        ];

        return $validFormats[$carrierSlug] ?? [];
    }
}
