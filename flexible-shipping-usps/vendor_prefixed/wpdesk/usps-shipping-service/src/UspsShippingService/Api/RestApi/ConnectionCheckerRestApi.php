<?php

namespace FlexibleShippingUspsVendor\WPDesk\UspsShippingService\Api\RestApi;

use FlexibleShippingUspsVendor\Octolize\Usps\DomesticPrices\Model\DomesticPricesSearchParameters;
use FlexibleShippingUspsVendor\Octolize\Usps\DomesticPricesApi;
use FlexibleShippingUspsVendor\Psr\Log\LoggerInterface;
use FlexibleShippingUspsVendor\WPDesk\UspsShippingService\Exception\ConnectionCheckerException;
/**
 * Can check connection.
 */
class ConnectionCheckerRestApi
{
    private DomesticPricesApi $domestic_prices_api;
    private LoggerInterface $logger;
    public function __construct(DomesticPricesApi $domestic_prices_api, LoggerInterface $logger)
    {
        $this->domestic_prices_api = $domestic_prices_api;
        $this->logger = $logger;
    }
    /**
     * Pings API.
     *
     * @throws \Exception .
     */
    public function check_connection(): void
    {
        $search_parameters = new DomesticPricesSearchParameters(91601, 91730);
        $search_parameters->set_mail_class('ALL')->set_price_type('RETAIL')->set_weight(1)->set_length(1)->set_width(1)->set_height(1);
        try {
            $this->logger->info(__('Connection checking', 'flexible-shipping-usps'));
            $this->domestic_prices_api->setLogger($this->logger);
            $this->domestic_prices_api->get_rates($search_parameters);
        } catch (\Exception $e) {
            throw new ConnectionCheckerException($e->getMessage());
        }
    }
}
