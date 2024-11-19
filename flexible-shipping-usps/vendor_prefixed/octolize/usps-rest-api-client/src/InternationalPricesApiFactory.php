<?php

namespace FlexibleShippingUspsVendor\Octolize\Usps;

use FlexibleShippingUspsVendor\Octolize\Usps\InternationalPrices\InternationalPricesClientOptions;
use FlexibleShippingUspsVendor\Psr\Log\LoggerInterface;
use FlexibleShippingUspsVendor\WPDesk\ApiClient\Client\ClientFactory;
class InternationalPricesApiFactory
{
    const CLIENT_VERSION = '1.0';
    const API_URL = 'https://apis.usps.com/';
    public function create(OAuthApi $oauth_api, LoggerInterface $logger, string $api_url = null): InternationalPricesApi
    {
        $api_url = $api_url ?? self::API_URL;
        $client_factory = new ClientFactory();
        $client_options = new InternationalPricesClientOptions($logger, $api_url, self::CLIENT_VERSION);
        return new InternationalPricesApi($oauth_api, $client_factory->createClient($client_options), $logger);
    }
}
