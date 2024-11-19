<?php

namespace FlexibleShippingUspsVendor\Octolize\Usps;

use FlexibleShippingUspsVendor\Octolize\Usps\OAuth\Keys;
use FlexibleShippingUspsVendor\Octolize\Usps\OAuth\OAuthClientOptions;
use FlexibleShippingUspsVendor\Psr\Log\LoggerInterface;
use FlexibleShippingUspsVendor\WPDesk\ApiClient\Client\ClientFactory;
class OAuthApiFactory
{
    const CLIENT_VERSION = '1.0';
    const API_URL = 'https://apis.usps.com/';
    public function create(Keys $keys, LoggerInterface $logger, string $api_url = null): OAuthApi
    {
        $api_url = $api_url ?? self::API_URL;
        $client_factory = new ClientFactory();
        $client_options = new OAuthClientOptions($logger, $api_url, self::CLIENT_VERSION);
        return new OAuthApi($keys, $client_factory->createClient($client_options), $logger);
    }
}
