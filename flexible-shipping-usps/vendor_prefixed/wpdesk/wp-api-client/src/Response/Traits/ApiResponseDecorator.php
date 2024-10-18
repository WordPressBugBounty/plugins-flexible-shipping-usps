<?php

namespace FlexibleShippingUspsVendor\WPDesk\ApiClient\Response\Traits;

use FlexibleShippingUspsVendor\WPDesk\ApiClient\Response\AuthApiResponse;
use FlexibleShippingUspsVendor\WPDesk\ApiClient\Response\RawResponse;
use FlexibleShippingUspsVendor\WPDesk\ApiClient\Response\Response;
trait ApiResponseDecorator
{
    /** @var RawResponse */
    private $rawResponse;
    /**
     * RawResponseDecorator constructor.
     * @param Response $rawResponse
     */
    public function __construct(Response $rawResponse)
    {
        $this->rawResponse = $rawResponse;
    }
    /**
     * Returns response http code
     *
     * @return int
     */
    public function getResponseCode()
    {
        return $this->rawResponse->getResponseCode();
    }
    /**
     * Returns response body as array
     *
     * @return array
     */
    public function getResponseBody()
    {
        return $this->rawResponse->getResponseBody();
    }
    /**
     * Returns response body as array
     *
     * @return array
     */
    public function getResponseErrorBody()
    {
        return $this->rawResponse->getResponseErrorBody();
    }
    /**
     * Returns response body as array
     *
     * @return array
     */
    public function getResponseHeaders()
    {
        return $this->rawResponse->getResponseHeaders();
    }
    /**
     * Get links structure to the other request
     *
     * @return array
     */
    public function getLinks()
    {
        $body = $this->getResponseBody();
        return $body['_links'];
    }
    /**
     * Is it a BAD REQUEST response
     *
     * @return bool
     */
    public function isBadRequest()
    {
        return $this->getResponseCode() === RawResponse::RESPONSE_CODE_ERROR_BAD_REQUEST;
    }
    /**
     * Is it a DOMAIN NOT ALLOWED response
     *
     * @return bool
     */
    public function isDomainNotAllowed()
    {
        return $this->getResponseCode() === RawResponse::RESPONSE_CODE_DOMAIN_NOT_ALLOWED;
    }
    /**
     * Is it a FATAL ERROR response
     *
     * @return bool
     */
    public function isServerFatalError()
    {
        return $this->getResponseCode() === RawResponse::RESPONSE_CODE_ERROR_FATAL;
    }
    /**
     * Is any error occured
     *
     * @return bool
     */
    public function isError()
    {
        return $this->rawResponse->isError();
    }
    /**
     * Is requested resource exists
     *
     * @return bool
     */
    public function isNotExists()
    {
        return $this->getResponseCode() === AuthApiResponse::RESPONSE_CODE_NOT_EXISTS;
    }
    /**
     * Is maintenance.
     *
     * @return bool
     */
    public function isMaintenance()
    {
        return $this->rawResponse->isMaintenance();
    }
    /**
     * Get platform version hash string.
     *
     * @return bool|string
     */
    public function getPlatformVersionHash()
    {
        return $this->rawResponse->getPlatformVersionHash();
    }
}
