<?php

namespace FlexibleShippingUspsVendor\WPDesk\ApiClient\Client;

use FlexibleShippingUspsVendor\Psr\Log\LoggerInterface;
use FlexibleShippingUspsVendor\WPDesk\ApiClient\Serializer\SerializerOptions;
use FlexibleShippingUspsVendor\WPDesk\HttpClient\HttpClientOptions;
interface ApiClientOptions extends HttpClientOptions, SerializerOptions
{
    /**
     * @return LoggerInterface
     */
    public function getLogger();
    /**
     * @return string
     */
    public function getApiUrl();
    /**
     * @return array
     */
    public function getDefaultRequestHeaders();
    /**
     * @return bool
     */
    public function isCachedClient();
    /**
     * @return string
     */
    public function getApiClientClass();
}
