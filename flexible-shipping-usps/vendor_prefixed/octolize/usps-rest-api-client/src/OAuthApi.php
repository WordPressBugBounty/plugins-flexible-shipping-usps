<?php

namespace FlexibleShippingUspsVendor\Octolize\Usps;

use FlexibleShippingUspsVendor\Octolize\Usps\OAuth\Keys;
use FlexibleShippingUspsVendor\Octolize\Usps\OAuth\Request\Token;
use FlexibleShippingUspsVendor\Psr\Log\LoggerAwareInterface;
use FlexibleShippingUspsVendor\Psr\Log\LoggerAwareTrait;
use FlexibleShippingUspsVendor\Psr\Log\LoggerInterface;
use FlexibleShippingUspsVendor\WPDesk\ApiClient\Client\Client;
class OAuthApi implements LoggerAwareInterface
{
    use LoggerAwareTrait;
    private Keys $keys;
    private Client $client;
    public function __construct(Keys $keys, Client $client, LoggerInterface $logger)
    {
        $this->keys = $keys;
        $this->setLogger($logger);
        $this->client = $client;
    }
    public function get_token(): array
    {
        $request = new Token($this->keys, 'prices international-prices');
        $response = $this->client->sendRequest($request);
        if ($response->isError()) {
            $this->logger->error('Error while getting token', ['response' => $response->getResponseBody()]);
            throw new ResponseErrorException($response->getResponseBody());
        }
        return $response->getResponseBody();
    }
    protected function get_keys(): Keys
    {
        return $this->keys;
    }
}
