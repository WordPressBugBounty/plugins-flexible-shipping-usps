<?php

namespace FlexibleShippingUspsVendor\WPDesk\UspsShippingService\Api\WebApi;

use FlexibleShippingUspsVendor\Psr\Log\LoggerInterface;
use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Exception\UnitConversionException;
use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Shipment\Package;
use FlexibleShippingUspsVendor\WPDesk\UspsShippingService\Api\Countries;
use FlexibleShippingUspsVendor\WPDesk\UspsShippingService\UspsSettingsDefinition;
/**
 * Build request for USPS international rate.
 */
class UspsRateRequestInternationalBuilder extends UspsRateRequestBuilder
{
    /**
     * Prepare rate request.
     *
     * @return Rate
     */
    protected function prepare_rate_request(LoggerInterface $logger): Rate
    {
        $rate = new Rate($this->settings->get_value(UspsSettingsDefinition::USER_ID), $this->settings->get_value(UspsSettingsDefinition::PASSWORD, ''), $logger);
        $rate->setInternationalCall(\true);
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
        $package = new RatePackage();
        $this->set_package_weight($package, $shipment_package);
        $package->setField('MailType', 'ALL');
        $package->setField('ValueOfContents', $this->calculate_package_value($shipment_package));
        $package->setField('Country', (new Countries())->get_country($this->shipment->ship_to->address->country_code));
        $package = $this->set_dimensions_if_present($package, $shipment_package);
        $package->setField('OriginZip', $this->shipment->ship_from->address->postal_code);
        $this->request->addPackage($package);
    }
}
