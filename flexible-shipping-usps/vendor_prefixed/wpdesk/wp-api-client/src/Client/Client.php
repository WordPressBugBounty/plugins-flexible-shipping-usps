<?php

namespace FlexibleShippingUspsVendor\WPDesk\ApiClient\Client;

use FlexibleShippingUspsVendor\WPDesk\HttpClient\HttpClient;
use FlexibleShippingUspsVendor\WPDesk\ApiClient\Request\Request;
use FlexibleShippingUspsVendor\WPDesk\ApiClient\Response\Response;
use FlexibleShippingUspsVendor\WPDesk\ApiClient\Serializer\Serializer;
interface Client
{
    /**
     * Send given request trough HttpClient
     *
     * @param Request $request
     * @return Response
     */
    public function sendRequest(Request $request);
    /**
     * @return HttpClient
     */
    public function getHttpClient();
    /**
     * @param HttpClient $client
     */
    public function setHttpClient(HttpClient $client);
    /**
     * @return Serializer
     */
    public function getSerializer();
    /**
     * Returns api url. Always without ending /
     *
     * @return string
     */
    public function getApiUrl();
}
