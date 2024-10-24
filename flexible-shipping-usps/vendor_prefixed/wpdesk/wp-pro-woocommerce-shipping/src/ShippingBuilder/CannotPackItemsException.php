<?php

/**
 * Cannot pack items Exception.
 *
 * @package WPDesk\WooCommerceShippingPro\ShippingBuilder
 */
namespace FlexibleShippingUspsVendor\WPDesk\WooCommerceShippingPro\ShippingBuilder;

use FlexibleShippingUspsVendor\WPDesk\Packer\Item;
/**
 * Cannot pack items exception (multiple items).
 */
class CannotPackItemsException extends \RuntimeException
{
    /**
     * CannotPackItemsException constructor.
     *
     * @param Item[] $items .
     */
    public function __construct($items)
    {
        $items_list = '';
        foreach ($items as $item) {
            /** @var WC_Product $product */
            // phpcs:ignore
            $internal_data = $item->get_internal_data();
            $product = $internal_data['data'];
            $items_list .= $product->get_name() . ', ';
        }
        $items_list = trim(trim($items_list), ',');
        // Translators: product names.
        $message = sprintf(__('Cannot pack items: %1$s.', 'flexible-shipping-usps'), $items_list);
        parent::__construct($message);
    }
}
