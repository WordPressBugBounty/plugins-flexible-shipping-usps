<?php

namespace FlexibleShippingUspsVendor\WPDesk\ApiClient\Response;

class RawResponse implements Response
{
    const RESPONSE_CODE_SUCCESS = 200;
    const RESPONSE_CODE_CREATED = 201;
    const RESPONSE_CODE_ERROR_BAD_REQUEST = 400;
    const RESPONSE_CODE_DOMAIN_NOT_ALLOWED = 462;
    const RESPONSE_CODE_ERROR_FATAL = 500;
    const RESPONSE_CODE_MAINTENANCE = 503;
    const HEADER_X_PLATFORM_VERSION_HASH = 'X-Platform-Version-Hash';
    /** @var array */
    private $data;
    /** @var int */
    private $code;
    /** @var array */
    private $headers;
    /**
     * RawResponse constructor.
     * @param array $body
     * @param int $code
     * @param array $headers
     */
    public function __construct(array $body, $code, array $headers)
    {
        $this->data = $body;
        $this->code = (int) $code;
        $this->headers = $headers;
    }
    /**
     * Returns response http code
     *
     * @return int
     */
    public function getResponseCode()
    {
        return $this->code;
    }
    /**
     * Returns response body as array
     *
     * @return array
     */
    public function getResponseBody()
    {
        return $this->data;
    }
    /**
     * Returns response body as array
     *
     * @return array
     */
    public function getResponseErrorBody()
    {
        return $this->data;
    }
    /**
     * Returns response body as array
     *
     * @return array
     */
    public function getResponseHeaders()
    {
        return $this->headers;
    }
    /**
     * Is any error occured
     *
     * @return bool
     */
    public function isError()
    {
        $code = $this->getResponseCode();
        return ($code < 200 || $code >= 300) && !$this->isMaintenance();
    }
    /**
     * Is maintenance.
     *
     * @return bool
     */
    public function isMaintenance()
    {
        $code = $this->getResponseCode();
        return self::RESPONSE_CODE_MAINTENANCE === $code;
    }
    /**
     * Get platform version hash string.
     *
     * @return bool|string
     */
    public function getPlatformVersionHash()
    {
        if (isset($this->headers[self::HEADER_X_PLATFORM_VERSION_HASH])) {
            return $this->headers[self::HEADER_X_PLATFORM_VERSION_HASH];
        }
        return \false;
    }
}
