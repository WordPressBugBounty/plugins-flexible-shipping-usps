<?php

namespace FlexibleShippingUspsVendor\Octolize\Usps;

use FlexibleShippingUspsVendor\Octolize\Usps\InternationalPrices\Model\InternationalSearchParameters;
use FlexibleShippingUspsVendor\Octolize\Usps\InternationalPrices\Request\TotalRatesSearch;
use FlexibleShippingUspsVendor\Psr\Log\LoggerAwareInterface;
use FlexibleShippingUspsVendor\Psr\Log\LoggerAwareTrait;
use FlexibleShippingUspsVendor\Psr\Log\LoggerInterface;
use FlexibleShippingUspsVendor\WPDesk\ApiClient\Client\Client;
class InternationalPricesApi implements LoggerAwareInterface
{
    use LoggerAwareTrait;
    private OAuthApi $oauth_api;
    private Client $client;
    public function __construct(OAuthApi $oauth_api, Client $client, LoggerInterface $logger)
    {
        $this->oauth_api = $oauth_api;
        $this->client = $client;
        $this->setLogger($logger);
    }
    public function get_rates(InternationalSearchParameters $search_parameters): array
    {
        $token = $this->oauth_api->get_token();
        $request = new TotalRatesSearch($token['access_token'], $search_parameters);
        $response = $this->client->sendRequest($request);
        $this->logger->info('USPS API Request', ['request' => $request->getBody(), 'endpoint' => $request->getEndPoint()]);
        $this->logger->info('USPS API Response', ['response' => $response->getResponseBody(), 'code' => $response->getResponseCode()]);
        if ($response->isError()) {
            $this->logger->error('Error while getting rates', ['response' => $response->getResponseBody()]);
            throw new ResponseErrorException($response->getResponseBody());
        }
        return $response->getResponseBody();
    }
}
