<?php

namespace FlexibleShippingUspsVendor\Octolize\Usps\OAuth;

use FlexibleShippingUspsVendor\Psr\Log\LoggerAwareInterface;
use FlexibleShippingUspsVendor\Psr\Log\LoggerAwareTrait;
use FlexibleShippingUspsVendor\Psr\Log\LoggerInterface;
use FlexibleShippingUspsVendor\WPDesk\ApiClient\Client\ApiClientOptions;
use FlexibleShippingUspsVendor\WPDesk\HttpClient\Curl\CurlClient;
class OAuthClientOptions implements ApiClientOptions, LoggerAwareInterface
{
    use LoggerAwareTrait;
    private string $api_url;
    private array $default_request_headers = [];
    public function __construct(LoggerInterface $logger, string $api_url, string $client_version)
    {
        $this->setLogger($logger);
        $this->api_url = $api_url;
        $this->setDefaultRequestHeaders(['User-Agent' => 'Octolize USPS client ' . $client_version]);
    }
    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }
    public function getApiUrl(): string
    {
        return $this->api_url;
    }
    public function getDefaultRequestHeaders(): array
    {
        return $this->default_request_headers;
    }
    public function setDefaultRequestHeaders(array $default_request_headers): void
    {
        $this->default_request_headers = $default_request_headers;
    }
    public function isCachedClient(): bool
    {
        return \false;
    }
    public function getApiClientClass(): string
    {
        return OAuthClient::class;
    }
    public function getHttpClientClass(): string
    {
        return CurlClient::class;
    }
    public function getSerializerClass(): string
    {
        return ContentSerializer::class;
    }
}
