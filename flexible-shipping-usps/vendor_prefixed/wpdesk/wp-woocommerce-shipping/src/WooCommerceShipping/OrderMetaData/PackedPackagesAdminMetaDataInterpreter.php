<?php

/**
 * Packed packages meta data interpreter.
 *
 * @package WPDesk\WooCommerceShipping\Ups
 */
namespace FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\Ups\MetaDataInterpreters;

use FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\OrderMetaData\AdminMetaDataUnchangedTrait;
use FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\OrderMetaData\PackedPackagesMetaDataBuilder;
use FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\OrderMetaData\SingleAdminOrderMetaDataInterpreter;
use FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\ShippingMethod\RateMethod\Fallback\FallbackRateMethod;
/**
 * Can interpret packed packages meta data from WooCommerce order shipping on admin.
 */
class PackedPackagesAdminMetaDataInterpreter implements SingleAdminOrderMetaDataInterpreter
{
    /**
     * Get meta key on admin order edit page.
     *
     * @param string         $display_key .
     * @param \WC_Meta_Data  $meta .
     * @param \WC_Order_Item $order_item .
     *
     * @return string
     */
    public function get_display_key($display_key, $meta, $order_item)
    {
        return __('Packages', 'flexible-shipping-usps');
    }
    /**
     * Returns items as string.
     *
     * @param array $items .
     *
     * @return string
     */
    private function get_items_as_string(array $items)
    {
        $display_value = '';
        foreach ($items as $item_name => $item_quantity) {
            $display_value .= $item_name . ' x ' . $item_quantity . ', ';
        }
        return $display_value;
    }
    /**
     * Get meta value on admin order edit page.
     *
     * @param string         $display_value .
     * @param \WC_Meta_Data  $meta .
     * @param \WC_Order_Item $order_item .
     *
     * @return string
     */
    public function get_display_value($display_value, $meta, $order_item)
    {
        $data = $meta->get_data();
        $display_value = '';
        $packages = json_decode($data['value'], \true);
        foreach ($packages as $package) {
            if (!empty($display_value)) {
                $display_value .= '<br/>';
            }
            $display_value .= ' <strong>' . $package['package'] . ':</strong> ';
            $display_value .= $this->get_items_as_string($package['items']);
            $display_value = trim(trim($display_value), ',');
        }
        return $display_value;
    }
    /**
     * Is supported key on admin?
     *
     * @param string $display_key .
     *
     * @return bool
     */
    public function is_supported_key_on_admin($display_key)
    {
        return $display_key === PackedPackagesMetaDataBuilder::META_DATA_KEY;
    }
}
