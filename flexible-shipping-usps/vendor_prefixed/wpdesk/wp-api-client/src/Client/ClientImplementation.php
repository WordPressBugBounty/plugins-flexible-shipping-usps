<?php

namespace FlexibleShippingUspsVendor\WPDesk\ApiClient\Client;

use FlexibleShippingUspsVendor\Psr\Log\LoggerAwareInterface;
use FlexibleShippingUspsVendor\Psr\Log\LoggerInterface;
use FlexibleShippingUspsVendor\Psr\Log\NullLogger;
use FlexibleShippingUspsVendor\WPDesk\HttpClient\HttpClient;
use FlexibleShippingUspsVendor\WPDesk\HttpClient\HttpClientResponse;
use FlexibleShippingUspsVendor\WPDesk\ApiClient\Request\Request;
use FlexibleShippingUspsVendor\WPDesk\ApiClient\Response\RawResponse;
use FlexibleShippingUspsVendor\WPDesk\ApiClient\Response\Response;
use FlexibleShippingUspsVendor\WPDesk\ApiClient\Serializer\Serializer;
use FlexibleShippingUspsVendor\WPDesk\HttpClient\HttpClientRequestException;
class ClientImplementation implements Client, LoggerAwareInterface
{
    const CLIENT_VERSION = '1.6.5';
    const LIBRARY_LOGIN_CONTEXT = 'wp-api-client';
    /** @var HttpClient */
    private $client;
    /** @var Serializer */
    private $serializer;
    /** @var LoggerInterface */
    private $logger;
    /** @var string */
    private $apiUrl;
    /** @var array */
    private $defaultRequestHeaders;
    /** @var int */
    private $timeout;
    /** @var bool */
    private $is_logger_available = \false;
    /**
     * Client constructor.
     * @param HttpClient $client
     * @param Serializer $serializer
     * @param LoggerInterface $logger
     * @param string $apiUri
     * @param array $defaultRequestHeaders
     * @param int $timeout
     */
    public function __construct(HttpClient $client, Serializer $serializer, LoggerInterface $logger, $apiUri, array $defaultRequestHeaders, $timeout = 10)
    {
        $this->client = $client;
        $this->serializer = $serializer;
        $this->logger = $logger;
        $this->apiUrl = $apiUri;
        $this->defaultRequestHeaders = $defaultRequestHeaders;
        $this->timeout = $timeout;
        $this->is_logger_available = !$logger instanceof NullLogger;
    }
    /**
     * Send given request trough HttpClient
     *
     * @param Request $request
     * @throws HttpClientRequestException
     * @return Response
     */
    public function sendRequest(Request $request)
    {
        if ($this->is_logger_available) {
            $this->logger->debug("Sends request with METHOD: {$request->getMethod()}; to ENDPOINT {$request->getEndpoint()}", $this->getLoggerContext());
        }
        try {
            $httpResponse = $this->client->send($fullUrl = $this->prepareFullUrl($request), $method = $request->getMethod(), $body = $this->prepareRequestBody($request), $headers = $this->prepareRequestHeaders($request), $this->timeout);
            if ($this->is_logger_available) {
                $this->logger->debug("Sent request with: URL: {$fullUrl};\n METHOD: {$method};\n BODY: {$body};\n" . "HEADERS: " . json_encode($headers) . "\n\n and got response as CODE: {$httpResponse->getResponseCode()};\n" . "with RESPONSE BODY {$httpResponse->getBody()}", $this->getLoggerContext());
            }
            return $this->mapHttpResponseToApiResponse($httpResponse);
        } catch (HttpClientRequestException $e) {
            $this->logger->error("Exception {$e->getMessage()}; {$e->getCode()} occurred while sending request");
            throw $e;
        }
    }
    /**
     * Returns full request url with endpoint
     *
     * @param Request $request
     * @return string
     */
    private function prepareFullUrl(Request $request)
    {
        $endpoint = $request->getEndpoint();
        if (strpos('http', $endpoint) === 0) {
            return $endpoint;
        }
        return $this->getApiUrl() . $endpoint;
    }
    /**
     * Map response from http client to api response using serializer
     *
     * @param HttpClientResponse $response
     * @return RawResponse
     */
    private function mapHttpResponseToApiResponse(HttpClientResponse $response)
    {
        $apiResponse = new RawResponse($this->serializer->unserialize($response->getBody()), $response->getResponseCode(), $response->getHeaders());
        return $apiResponse;
    }
    /**
     * Prepare serialized request body
     *
     * @param Request $request
     * @return string
     */
    private function prepareRequestBody(Request $request)
    {
        return $this->serializer->serialize($request->getBody());
    }
    /**
     * Prepares array of http headers
     *
     * @param Request $request
     * @return array
     */
    private function prepareRequestHeaders(Request $request)
    {
        $headers = array('User-Agent' => 'saas-client-' . self::CLIENT_VERSION, 'Accept-Encoding' => '*', 'Content-Type' => $this->serializer->getMime());
        $headers = array_merge($headers, $this->defaultRequestHeaders);
        return array_merge($headers, $request->getHeaders());
    }
    /**
     * @return HttpClient
     */
    public function getHttpClient()
    {
        return $this->client;
    }
    /**
     * @param HttpClient $client
     */
    public function setHttpClient(HttpClient $client)
    {
        $this->client = $client;
    }
    /**
     * @return Serializer
     */
    public function getSerializer()
    {
        return $this->serializer;
    }
    /**
     * Returns api url. Always without ending /
     *
     * @return string
     */
    public function getApiUrl()
    {
        return trim($this->apiUrl, '/');
    }
    /**
     * Sets logger
     *
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
    /**
     * Returns logger context for
     *
     * @param string $additional_context Optional additional context
     * @return array
     */
    protected function getLoggerContext($additional_context = '')
    {
        $context = [self::LIBRARY_LOGIN_CONTEXT, self::class];
        if ($additional_context !== '') {
            $context[] = $additional_context;
        }
        return $context;
    }
}
