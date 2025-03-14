<?php

namespace FlexibleShippingUspsVendor\WPDesk\UspsShippingService\Api\WebApi;

use FlexibleShippingUspsVendor\Psr\Log\LoggerInterface;
use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Exception\UnitConversionException;
use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Settings\SettingsValues;
use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Shipment\Package;
use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Shipment\Shipment;
use FlexibleShippingUspsVendor\WPDesk\UspsShippingService\UspsSettingsDefinition;
use FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\ShopSettings;
/**
 * Build request for USPS domestic rate
 */
class UspsRateRequestDomesticBuilder extends UspsRateRequestBuilder
{
    private string $service;
    /**
     * UspsRateRequestBuilder constructor.
     *
     * @param SettingsValues $settings Settings.
     * @param Shipment $shipment Shipment.
     * @param ShopSettings $helper Helper.
     * @param array $services Services.
     * @param LoggerInterface $logger Logger.
     */
    public function __construct(SettingsValues $settings, Shipment $shipment, ShopSettings $helper, LoggerInterface $logger, string $service = 'ONLINE')
    {
        parent::__construct($settings, $shipment, $helper, $logger);
        $this->service = $service;
    }
    /**
     * Prepare rate request.
     *
     * @param LoggerInterface $logger
     *
     * @return Rate
     */
    protected function prepare_rate_request(LoggerInterface $logger): Rate
    {
        $rate = new Rate($this->settings->get_value(UspsSettingsDefinition::USER_ID), $this->settings->get_value(UspsSettingsDefinition::PASSWORD, ''), $logger);
        $rate->setInternationalCall(\false);
        return $rate;
    }
    /**
     * Add package.
     *
     * @param Package $shipment_package .
     *
     * @throws UnitConversionException .
     */
    protected function add_package(Package $shipment_package): void
    {
        $this->add_package_for_service($shipment_package, $this->service);
    }
    private function add_package_for_service(Package $shipment_package, string $service): void
    {
        $package = new RatePackage();
        $package->setService($service);
        $package->setZipOrigination($this->shipment->ship_from->address->postal_code);
        $package->setZipDestination(substr($this->clean_postal_code($this->shipment->ship_to->address->postal_code), 0, 5));
        $this->set_package_weight($package, $shipment_package);
        if (in_array($this->service, ['PRIORITY MAIL CUBIC', 'GROUND ADVANTAGE CUBIC'], \true)) {
            $package->setContainer('CUBIC PARCELS');
        } else {
            $package->setContainer('VARIABLE');
        }
        $package = $this->set_dimensions_if_present($package, $shipment_package);
        $package = $this->set_insurance_if_enabled($package, $shipment_package);
        $package->setField('Machinable', \true);
        $this->request->addPackage($package);
    }
    /**
     * @param string $postal_code .
     *
     * @return string
     */
    private function clean_postal_code(string $postal_code): string
    {
        return trim(str_replace([' ', '-'], '', $postal_code));
    }
    /**
     * Set insurance.
     *
     * @param RatePackage $usps_package .
     * @param Package $shipment_package .
     *
     * @return RatePackage
     */
    private function set_insurance_if_enabled(RatePackage $usps_package, Package $shipment_package): RatePackage
    {
        if ('yes' === $this->settings->get_value(UspsSettingsDefinition::INSURANCE, 'no')) {
            $usps_package->setField('Value', $this->calculate_package_value($shipment_package));
            $usps_package->addSpecialService(100);
            $usps_package->addSpecialService(125);
        }
        return $usps_package;
    }
}
