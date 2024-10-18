<?php

namespace FlexibleShippingUspsVendor\Octolize\Usps;

use FlexibleShippingUspsVendor\Octolize\Usps\OAuth\Keys;
use FlexibleShippingUspsVendor\Psr\Log\LoggerInterface;
class RestApiFactory
{
    public function create(string $customer_id, string $customer_secret, LoggerInterface $logger, string $oauth_class_decorator = null): RestApi
    {
        $keys = new Keys($customer_id, $customer_secret);
        $oauth_api_factory = new OAuthApiFactory();
        $domestic_prices_api_factory = new DomesticPricesApiFactory();
        $international_prices_api_factory = new InternationalPricesApiFactory();
        $oauth_api = $oauth_api_factory->create($keys, $logger);
        if ($oauth_class_decorator) {
            $oauth_api = new $oauth_class_decorator($oauth_api);
        }
        $domestic_prices_api = $domestic_prices_api_factory->create($oauth_api, $logger);
        $international_prices_api = $international_prices_api_factory->create($oauth_api, $logger);
        return new RestApi($oauth_api, $domestic_prices_api, $international_prices_api);
    }
}
