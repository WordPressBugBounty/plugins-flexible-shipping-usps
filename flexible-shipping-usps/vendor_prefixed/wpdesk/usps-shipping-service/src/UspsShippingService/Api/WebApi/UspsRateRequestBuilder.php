<?php

namespace FlexibleShippingUspsVendor\WPDesk\UspsShippingService\Api\WebApi;

use FlexibleShippingUspsVendor\Psr\Log\LoggerInterface;
use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Exception\UnitConversionException;
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
/**
 * Build request for USPS rate
 */
abstract class UspsRateRequestBuilder
{
    const WEIGHT_ROUNDING_PRECISION = 4;
    /**
     * WooCommerce shipment.
     *
     * @var Shipment
     */
    protected $shipment;
    /**
     * Settings values.
     *
     * @var SettingsValues
     */
    protected $settings;
    /**
     * Request
     *
     * @var Rate
     */
    protected $request;
    /**
     * Shop settings.
     *
     * @var ShopSettings
     */
    protected $shop_settings;
    /**
     * UspsRateRequestBuilder constructor.
     *
     * @param SettingsValues $settings Settings.
     * @param Shipment $shipment Shipment.
     * @param ShopSettings $helper Helper.
     * @param array $services Services.
     * @param LoggerInterface $logger Logger.
     */
    public function __construct(SettingsValues $settings, Shipment $shipment, ShopSettings $helper, LoggerInterface $logger)
    {
        $this->settings = $settings;
        $this->shipment = $shipment;
        $this->shop_settings = $helper;
        $this->request = $this->prepare_rate_request($logger);
    }
    /**
     * Prepare rate request.
     *
     * @return Rate
     */
    abstract protected function prepare_rate_request(LoggerInterface $logger): Rate;
    /**
     * Calculate package weight.
     *
     * @param Package $shipment_package .
     * @param string $weight_unit .
     *
     * @return float
     * @throws UnitConversionException Weight exception.
     */
    protected function calculate_package_weight(Package $shipment_package, $weight_unit): float
    {
        if (isset($shipment_package->weight)) {
            return (new UniversalWeight($shipment_package->weight->weight, $shipment_package->weight->weight_unit, self::WEIGHT_ROUNDING_PRECISION))->as_unit_rounded($weight_unit, self::WEIGHT_ROUNDING_PRECISION);
        }
        $package_weight = 0.0;
        foreach ($shipment_package->items as $item) {
            $item_weight = (new UniversalWeight($item->weight->weight, $item->weight->weight_unit, self::WEIGHT_ROUNDING_PRECISION))->as_unit_rounded($weight_unit, self::WEIGHT_ROUNDING_PRECISION);
            $package_weight += $item_weight;
        }
        return $package_weight;
    }
    /**
     * @param RatePackage $package
     * @param PAckage $shipment_package
     *
     * @return void
     * @throws UnitConversionException
     */
    protected function set_package_weight(RatePackage $package, $shipment_package)
    {
        $pounds = $this->calculate_package_weight($shipment_package, Weight::WEIGHT_UNIT_LB);
        $truncated_pounds = floor($pounds);
        $ounces = (new UniversalWeight($pounds - $truncated_pounds, Weight::WEIGHT_UNIT_LB))->as_unit_rounded(Weight::WEIGHT_UNIT_OZ);
        $pounds = $truncated_pounds;
        $package->setPounds($pounds);
        $package->setOunces($ounces);
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
    /**
     * Set package dimensions if present.
     *
     * @param RatePackage $usps_package .
     * @param Package $shipment_package .
     *
     * @return RatePackage
     * @throws UnitConversionException .
     */
    protected function set_dimensions_if_present(RatePackage $usps_package, Package $shipment_package): RatePackage
    {
        if (isset($shipment_package->dimensions)) {
            $target_dimension_unit = Dimensions::DIMENSION_UNIT_IN;
            $width = new UniversalDimension($shipment_package->dimensions->width, $shipment_package->dimensions->dimensions_unit);
            $height = new UniversalDimension($shipment_package->dimensions->height, $shipment_package->dimensions->dimensions_unit);
            $length = new UniversalDimension($shipment_package->dimensions->length, $shipment_package->dimensions->dimensions_unit);
            $usps_package->setWidth($width->as_unit_rounded($target_dimension_unit));
            $usps_package->setLength($length->as_unit_rounded($target_dimension_unit));
            $usps_package->setHeight($height->as_unit_rounded($target_dimension_unit));
        }
        return $usps_package;
    }
    /**
     * Add package.
     *
     * @param Package $shipment_package .
     *
     * @throws UnitConversionException .
     */
    abstract protected function add_package(Package $shipment_package): void;
    /**
     * Set package;
     *
     * @throws UnitConversionException Weight exception.
     */
    protected function set_packages()
    {
        $package_index = 0;
        foreach ($this->shipment->packages as $package) {
            $this->add_package($package, ++$package_index);
        }
    }
    /**
     * Build request.
     *
     * @throws UnitConversionException Weight exception.
     */
    public function build_request()
    {
        $this->set_packages();
    }
    /**
     * Get request.
     *
     * @return Rate
     */
    public function get_build_request(): Rate
    {
        return $this->request;
    }
}
