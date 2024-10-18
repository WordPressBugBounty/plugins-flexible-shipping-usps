<?php

namespace FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\Usps\Dto;

use FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\Usps\UspsShippingMethod;
class ShippingMethodWithShippingZone
{
    private UspsShippingMethod $shipping_method;
    private \WC_Shipping_Zone $zone;
    public function __construct(UspsShippingMethod $shipping_method, \WC_Shipping_Zone $zone)
    {
        $this->shipping_method = $shipping_method;
        $this->zone = $zone;
    }
    public function get_shipping_method(): UspsShippingMethod
    {
        return $this->shipping_method;
    }
    public function get_zone(): \WC_Shipping_Zone
    {
        return $this->zone;
    }
}
