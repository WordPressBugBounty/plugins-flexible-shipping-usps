<?php

namespace FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\Usps;

use FlexibleShippingUspsVendor\WPDesk\Notice\Notice;
use FlexibleShippingUspsVendor\WPDesk\PluginBuilder\Plugin\Hookable;
use FlexibleShippingUspsVendor\WPDesk\UspsShippingService\UspsSettingsDefinition;
use FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\Usps\Dto\ShippingMethodWithShippingZone;
class ShippingMethodsChecker implements Hookable
{
    public function hooks(): void
    {
        add_action('admin_notices', [$this, 'check_shipping_methods']);
    }
    public function check_shipping_methods(): void
    {
        if (!class_exists('\WC_Shipping_Zones')) {
            return;
        }
        if (!class_exists(UspsShippingMethod::class)) {
            return;
        }
        $usps_shipping_method = new UspsShippingMethod();
        if ($usps_shipping_method->get_option(UspsSettingsDefinition::API_TYPE, UspsSettingsDefinition::API_TYPE_REST) === UspsSettingsDefinition::API_TYPE_WEB) {
            return;
        }
        $shipping_methods = $this->get_shipping_methods();
        if (!empty($shipping_methods)) {
            $list_of_methods = '';
            foreach ($shipping_methods as $shipping_method_with_zone) {
                $list_of_methods .= sprintf('%1$s%2$s%3$s, ', '<a href="' . admin_url('admin.php?page=wc-settings&tab=shipping&instance_id=' . $shipping_method_with_zone->get_shipping_method()->get_instance_id()) . '">', $shipping_method_with_zone->get_shipping_method()->get_title() . ' (' . $shipping_method_with_zone->get_zone()->get_zone_name() . ')', '</a>');
            }
            $notice_content = sprintf(__('%1$sYou have USPS shipping methods that are not configured for the REST API%2$s.%3$sList of methods requiring configuration: %4$s', 'flexible-shipping-usps'), '<strong>', '</strong>', '<br>', trim($list_of_methods));
            (new Notice($notice_content, Notice::NOTICE_TYPE_ERROR))->showNotice();
        }
    }
    /**
     * @return ShippingMethodWithShippingZone[]
     */
    private function get_shipping_methods(): array
    {
        $woocommerce_shipping_zones = \WC_Shipping_Zones::get_zones();
        $woocommerce_shipping_zones[] = ['id' => 0];
        $shipping_methods = [];
        foreach ($woocommerce_shipping_zones as $woocommerce_shipping_zone) {
            $zone = \WC_Shipping_Zones::get_zone($woocommerce_shipping_zone['id']);
            $zone_shipping_methods = $zone->get_shipping_methods();
            foreach ($zone_shipping_methods as $shipping_method) {
                if ($shipping_method instanceof UspsShippingMethod) {
                    if (empty($shipping_method->get_option(UspsSettingsDefinition::SKU_SERVICE_TYPE, []))) {
                        $shipping_methods[] = new ShippingMethodWithShippingZone($shipping_method, $zone);
                    }
                }
            }
        }
        return $shipping_methods;
    }
}
