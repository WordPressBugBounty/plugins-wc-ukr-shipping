<?php

namespace kirillbdev\WCUkrShipping\Traits;

use kirillbdev\WCUkrShipping\Foundation\State;

if ( ! defined('ABSPATH')) {
    exit;
}

trait StateInitiatorTrait
{
    public function initState()
    {
        do_action('wcus_state_init');

        ?>
        <script>
          window.WCUS_APP_STATE = <?php echo wp_json_encode(State::all()); ?>;
        </script>
        <?php
    }
}