<?php

namespace FlexibleShippingUspsVendor\WPDesk\UspsShippingService;

class UspsSkuComponents
{
    public function get_service_types(): array
    {
        return ['P' => __('Priority Mail', 'flexible-shipping-usps'), 'E' => __('Priority Mail Express', 'flexible-shipping-usps'), 'M' => __('Media', 'flexible-shipping-usps'), 'F' => __('First-Class Mail', 'flexible-shipping-usps'), 'U' => __('USPS Ground Advantage*', 'flexible-shipping-usps'), 'L' => __('Library', 'flexible-shipping-usps')];
    }
    public function get_service_sub_types(): array
    {
        return ['X' => __('None', 'flexible-shipping-usps'), 'A' => __('Automation', 'flexible-shipping-usps'), 'B' => __('Nonautomation', 'flexible-shipping-usps'), 'C' => __('Carrier Route', 'flexible-shipping-usps'), 'D' => __('Carrier Route Nonautomation', 'flexible-shipping-usps'), 'E' => __('Pending Periodicals', 'flexible-shipping-usps'), 'F' => __('Flat Rate', 'flexible-shipping-usps'), 'G' => __('USPS Connect Local', 'flexible-shipping-usps'), 'H' => __('USPS Connect Regional', 'flexible-shipping-usps'), 'I' => __('Irregular', 'flexible-shipping-usps'), 'K' => __('Share Mail', 'flexible-shipping-usps'), 'L' => __('Metered', 'flexible-shipping-usps'), 'M' => __('Machinable', 'flexible-shipping-usps'), 'N' => __('Nonmachinable', 'flexible-shipping-usps'), 'O' => __('USPS Connect Flat Rate', 'flexible-shipping-usps'), 'P' => __('Presorted', 'flexible-shipping-usps'), 'Q' => __('Automation Disc', 'flexible-shipping-usps'), 'R' => __('Regional Rate', 'flexible-shipping-usps'), 'S' => __('Simple Samples', 'flexible-shipping-usps'), 'T' => __('Permit Reply Mail', 'flexible-shipping-usps'), 'U' => __('Cubic', 'flexible-shipping-usps'), 'V' => __('Nonpresorted', 'flexible-shipping-usps'), 'W' => __('Permit Reply Mail', 'flexible-shipping-usps'), 'Y' => __('Nonautomation Disc', 'flexible-shipping-usps'), 'Z' => __('Customized', 'flexible-shipping-usps')];
    }
    public function get_shapes(): array
    {
        return ['X' => __('None', 'flexible-shipping-usps'), 'A' => __('Bag', 'flexible-shipping-usps'), 'B' => __('Box', 'flexible-shipping-usps'), 'C' => __('Postcards', 'flexible-shipping-usps'), 'E' => __('Envelope', 'flexible-shipping-usps'), 'F' => __('Flats or Large Envelope', 'flexible-shipping-usps'), 'H' => __('Half Tray', 'flexible-shipping-usps'), 'I' => __('Full Tray', 'flexible-shipping-usps'), 'J' => __('EMM Tray', 'flexible-shipping-usps'), 'K' => __('Tub', 'flexible-shipping-usps'), 'L' => __('Letters', 'flexible-shipping-usps'), 'M' => __('M-Bag', 'flexible-shipping-usps'), 'N' => __('Balloon', 'flexible-shipping-usps'), 'O' => __('Oversize', 'flexible-shipping-usps'), 'P' => __('Parcel or Package', 'flexible-shipping-usps'), 'Q' => __('Keys and IDs', 'flexible-shipping-usps'), 'R' => __('Dimensional Weight', 'flexible-shipping-usps'), 'U' => __('Pallet', 'flexible-shipping-usps'), 'V' => __('Half Pallet Box', 'flexible-shipping-usps'), 'W' => __('Full Pallet Box', 'flexible-shipping-usps')];
    }
    public function get_delivery_types(): array
    {
        return ['X' => __('None', 'flexible-shipping-usps'), 'H' => __('Hold for Pickup', 'flexible-shipping-usps'), 'S' => __('Sunday/Holiday', 'flexible-shipping-usps'), 'R' => __('Return', 'flexible-shipping-usps')];
    }
    public function add_codes_to_labels(array $array): array
    {
        array_walk($array, function (&$value, $key) {
            $value = sprintf('(%1$s) %2$s', $key, $value);
        });
        return $array;
    }
    public function get_filtered_array_by_codes(array $array, array $codes): array
    {
        return array_filter($array, function ($key) use ($codes) {
            return in_array($key, $codes, \true);
        }, \ARRAY_FILTER_USE_KEY);
    }
}
