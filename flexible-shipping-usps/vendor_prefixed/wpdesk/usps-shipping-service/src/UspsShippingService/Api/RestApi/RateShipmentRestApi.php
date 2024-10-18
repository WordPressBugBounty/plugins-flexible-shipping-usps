<?php

namespace FlexibleShippingUspsVendor\WPDesk\UspsShippingService\Api\RestApi;

use FlexibleShippingUspsVendor\Octolize\Usps\CommonSearchParameters;
use FlexibleShippingUspsVendor\Octolize\Usps\DomesticPrices\Model\DomesticPricesSearchParameters;
use FlexibleShippingUspsVendor\Octolize\Usps\InternationalPrices\Model\InternationalSearchParameters;
use FlexibleShippingUspsVendor\Octolize\Usps\RestApi;
use FlexibleShippingUspsVendor\Psr\Log\LoggerAwareInterface;
use FlexibleShippingUspsVendor\Psr\Log\LoggerAwareTrait;
use FlexibleShippingUspsVendor\Psr\Log\LoggerInterface;
use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Exception\InvalidSettingsException;
use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Exception\RateException;
use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Exception\UnitConversionException;
use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Rate\Money;
use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Rate\ShipmentRating;
use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Rate\ShipmentRatingImplementation;
use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Rate\SingleRate;
use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Settings\SettingsValues;
use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Shipment\Dimensions;
use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Shipment\Item;
use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Shipment\Package;
use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Shipment\Shipment;
use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Shipment\Weight;
use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\UnitConversion\UniversalDimension;
use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\UnitConversion\UniversalWeight;
use FlexibleShippingUspsVendor\WPDesk\UspsShippingService\UspsSettingsDefinition;
use FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\ShopSettings;
class RateShipmentRestApi implements LoggerAwareInterface
{
    const WEIGHT_ROUNDING_PRECISION = 4;
    const INSURANCE_TO_500 = 930;
    const INSURANCE_OVER_500 = 931;
    use LoggerAwareTrait;
    private ShopSettings $shop_settings;
    private string $origin_country;
    private RestApi $rest_api;
    private SettingsValues $settings;
    public function __construct(RestApi $rest_api, ShopSettings $shop_settings, SettingsValues $settings, LoggerInterface $logger)
    {
        $this->rest_api = $rest_api;
        $this->shop_settings = $shop_settings;
        $this->settings = $settings;
        $this->setLogger($logger);
    }
    /**
     * Rate shipment.
     *
     * @param SettingsValues $settings             Settings.
     * @param Shipment       $shipment             Shipment.
     * @param bool           $is_domestic_shipment Is domestic shipment.
     *
     * @return ShipmentRating
     * @throws InvalidSettingsException InvalidSettingsException.
     * @throws RateException RateException.
     * @throws UnitConversionException Weight exception.
     */
    public function rate_shipment(SettingsValues $settings, Shipment $shipment, bool $is_domestic_shipment): ShipmentRating
    {
        $insurance = 'yes' === $settings->get_value(UspsSettingsDefinition::INSURANCE, 'no');
        if ($is_domestic_shipment) {
            $rates = $this->rate_domestic_shipment($settings, $shipment, $insurance);
        } else {
            $rates = $this->rate_international_shipment($settings, $shipment, $insurance);
        }
        $rates_filter = new ServiceRatesFilterRestApi($this->logger);
        $rates = $rates_filter->filter_service_rates($settings, $rates);
        return new ShipmentRatingImplementation($rates);
    }
    private function rate_domestic_shipment(SettingsValues $settings, Shipment $shipment, bool $insurance): array
    {
        $api_rates_per_package = [];
        $domestic_prices_api = $this->rest_api->get_domestic_prices_api();
        $domestic_prices_api->setLogger($this->logger);
        foreach ($shipment->packages as $package_id => $package) {
            $search_parameters = new DomesticPricesSearchParameters($shipment->ship_from->address->postal_code, $shipment->ship_to->address->postal_code);
            $package_value = $this->calculate_package_value($package);
            $special_services = $this->prepare_special_services($package_value, $insurance);
            $this->set_package_data($search_parameters, $package, $package_value, $settings, $special_services, $insurance);
            $api_rates_per_package[$package_id] = ['rates' => $domestic_prices_api->get_rates($search_parameters), 'special_services' => $special_services];
        }
        return $this->merge_package_rates($api_rates_per_package);
    }
    private function rate_international_shipment(SettingsValues $settings, Shipment $shipment, bool $insurance): array
    {
        $api_rates_per_package = [];
        $international_prices_api = $this->rest_api->get_international_prices_api();
        $international_prices_api->setLogger($this->logger);
        foreach ($shipment->packages as $package_id => $package) {
            $search_parameters = new InternationalSearchParameters($shipment->ship_from->address->postal_code, $shipment->ship_to->address->postal_code, $shipment->ship_to->address->country_code);
            $package_value = $this->calculate_package_value($package);
            $special_services = $this->prepare_special_services($package_value, $insurance);
            $this->set_package_data($search_parameters, $package, $package_value, $settings, $special_services, $insurance);
            $api_rates_per_package[$package_id] = ['rates' => $international_prices_api->get_rates($search_parameters), 'special_services' => $special_services];
        }
        return $this->merge_package_rates($api_rates_per_package);
    }
    private function prepare_special_services(float $package_value, bool $insurance): array
    {
        $special_services = [];
        if ($insurance) {
            if ($package_value <= 500) {
                $special_services[] = self::INSURANCE_TO_500;
            } else {
                $special_services[] = self::INSURANCE_OVER_500;
            }
        }
        return $special_services;
    }
    private function set_package_data(CommonSearchParameters $search_parameters, Package $package, float $package_value, SettingsValues $settings, array $special_services = [], $insurance = \false): void
    {
        $commercial_rates = 'yes' === $settings->get_value(UspsSettingsDefinition::COMMERCIAL_RATES, 'no');
        $weight = $package->weight->weight ? (new UniversalWeight($package->weight->weight, $package->weight->weight_unit, self::WEIGHT_ROUNDING_PRECISION))->as_unit_rounded(Weight::WEIGHT_UNIT_LB, self::WEIGHT_ROUNDING_PRECISION) : $this->get_default_weight($settings);
        $width = $package->dimensions ? (new UniversalDimension($package->dimensions->width, $package->dimensions->dimensions_unit))->as_unit_rounded(Dimensions::DIMENSION_UNIT_IN) : $this->get_default_width($settings);
        $height = $package->dimensions ? (new UniversalDimension($package->dimensions->height, $package->dimensions->dimensions_unit))->as_unit_rounded(Dimensions::DIMENSION_UNIT_IN) : $this->get_default_height($settings);
        $length = $package->dimensions ? (new UniversalDimension($package->dimensions->length, $package->dimensions->dimensions_unit))->as_unit_rounded(Dimensions::DIMENSION_UNIT_IN) : $this->get_default_length($settings);
        $search_parameters->set_mail_class('ALL')->set_price_type($commercial_rates ? 'COMMERCIAL' : 'RETAIL')->set_weight($weight)->set_length($length)->set_width($width)->set_height($height)->set_special_services($special_services);
        if ($insurance) {
            $search_parameters->set_item_value($package_value);
        }
    }
    /**
     * Calculate package value.
     *
     * @param Package $shipment_package .
     *
     * @return float
     */
    protected function calculate_package_value(Package $shipment_package): float
    {
        if ($this->settings->get_value(UspsSettingsDefinition::OVERWRITE_VALUE_OF_CONTENTS, 'NO') === 'yes') {
            return round((float) $this->settings->get_value(UspsSettingsDefinition::VALUE_OF_CONTENTS, 0.0), 2);
        }
        $total_value = 0.0;
        /** @var Item $item */
        // phpcs:ignore
        foreach ($shipment_package->items as $item) {
            $total_value += $item->declared_value->amount;
        }
        return round($total_value, 2);
    }
    private function get_default_weight(SettingsValues $settings): float
    {
        return (float) $settings->get_value(UspsSettingsDefinition::PACKAGE_WEIGHT, 0);
    }
    private function get_default_length(SettingsValues $settings): float
    {
        return (float) $settings->get_value(UspsSettingsDefinition::PACKAGE_LENGTH, 0);
    }
    private function get_default_width(SettingsValues $settings): float
    {
        return (float) $settings->get_value(UspsSettingsDefinition::PACKAGE_WIDTH, 0);
    }
    private function get_default_height(SettingsValues $settings): float
    {
        return (float) $settings->get_value(UspsSettingsDefinition::PACKAGE_HEIGHT, 0);
    }
    private function merge_package_rates(array $api_rates_per_package): array
    {
        $merged_rates = [];
        foreach ($api_rates_per_package as $api_rates_for_package) {
            $api_rates = $api_rates_for_package['rates'];
            $include_special_services = $api_rates_for_package['special_services'];
            foreach ($api_rates['rateOptions'] as $api_rate) {
                $sku = $api_rate['rates'][0]['SKU'];
                $price = $api_rate['rates'][0]['price'] + $this->get_extra_services_price($api_rate['extraServices'], $include_special_services);
                $description = $api_rate['rates'][0]['description'];
                $service_id = $sku . '-' . $description;
                if (!isset($merged_rates[$service_id])) {
                    $rate = new SingleRate();
                    $rate->service_name = $description;
                    $rate->service_type = $this->service_type_from_sku($sku);
                    $rate->total_charge = new Money();
                    $rate->total_charge->amount = $price;
                    $rate->total_charge->currency = 'USD';
                    $merged_rates[$service_id] = ['count' => 1, 'rate' => $rate];
                } else {
                    $merged_rates[$service_id]['count']++;
                    $merged_rates[$service_id]['rate']->total_charge->amount += $price;
                }
            }
        }
        return $this->get_complete_rates($merged_rates, count($api_rates_per_package));
    }
    private function get_extra_services_price(array $extra_services, array $include_special_services): float
    {
        $price = 0.0;
        foreach ($extra_services as $extra_service) {
            if (in_array($extra_service['extraService'], $include_special_services)) {
                $price += $extra_service['price'];
            }
        }
        return $price;
    }
    private function get_complete_rates(array $merged_rates, int $packages_count): array
    {
        $rates = [];
        foreach ($merged_rates as $rate_data) {
            if ($rate_data['count'] === $packages_count) {
                $rates[] = $rate_data['rate'];
            }
        }
        return $rates;
    }
    private function service_type_from_sku(string $sku): string
    {
        $service_type = $sku;
        $sku_parts = str_split($sku);
        return $service_type;
    }
}
