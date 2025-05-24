<?php
    if ( ! defined('ABSPATH')) {
        exit;
    }

    use \kirillbdev\WCUkrShipping\Helpers\HtmlHelper;
?>

<div id="wcus-pane-shipping" class="wcus-tab-pane">

    <?php
        HtmlHelper::selectField(
            'wcus[cod_payment_id]',
            __('COD method', 'wc-ukr-shipping-i18n'),
            $payment_methods,
            $cod_payment_id
        );
    ?>

</div>
