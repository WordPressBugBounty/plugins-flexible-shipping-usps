<?php

namespace FlexibleShippingUspsVendor\WPDesk\ShowDecision\WooCommerce;

use FlexibleShippingUspsVendor\WPDesk\ShowDecision\GetStrategy;
class ShippingMethodStrategy extends GetStrategy
{
    public function __construct(string $method_id)
    {
        parent::__construct([['page' => 'wc-settings', 'tab' => 'shipping', 'section' => $method_id]]);
    }
}