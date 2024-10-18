<?php

namespace FlexibleShippingUspsVendor\WPDesk\HttpClient\Curl;

use FlexibleShippingUspsVendor\WPDesk\HttpClient\Curl\Exception\CurlException;
use FlexibleShippingUspsVendor\WPDesk\HttpClient\Curl\Exception\CurlTimedOutException;
class CurlExceptionFactory
{
    /**
     * Convert curl code to appropriate exception class.
     *
     * @param int $curlCode Code from https://curl.haxx.se/libcurl/c/libcurl-errors.html
     * @param string $curlMessage
     * @return CurlException
     */
    public static function createCurlException($curlCode, $curlMessage)
    {
        switch ($curlCode) {
            case \CURLE_OPERATION_TIMEDOUT:
                return new CurlTimedOutException($curlMessage, $curlCode);
            default:
                return self::createDefaultException($curlMessage, $curlCode);
        }
    }
    /**
     * Creates default Curl exception
     *
     * @param $code
     * @param $message
     * @param \Exception|null $prev
     * @return CurlException
     */
    public static function createDefaultException($code, $message, \Exception $prev = null)
    {
        return new CurlException('Default exception: ' . $message, $code, $prev);
    }
}
