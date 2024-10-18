<?php

namespace FlexibleShippingUspsVendor\WPDesk\ApiClient\Client;

use FlexibleShippingUspsVendor\WPDesk\Cache\WordpressCache;
use FlexibleShippingUspsVendor\WPDesk\HttpClient\HttpClientFactory;
use FlexibleShippingUspsVendor\WPDesk\ApiClient\Serializer\SerializerFactory;
class ClientFactory
{
    /**
     * @param ApiClientOptions $options
     * @return Client
     */
    public function createClient(ApiClientOptions $options)
    {
        $httpClientFactory = new HttpClientFactory();
        $serializerFactory = new SerializerFactory();
        $className = $options->getApiClientClass();
        $client = new $className($httpClientFactory->createClient($options), $serializerFactory->createSerializer($options), $options->getLogger(), $options->getApiUrl(), $options->getDefaultRequestHeaders(), $options instanceof ApiClientOptionsTimeout ? $options->getTimeout() : null);
        if ($options->isCachedClient()) {
            $client = new CachedClient($client, new WordpressCache());
        }
        return $client;
    }
}
