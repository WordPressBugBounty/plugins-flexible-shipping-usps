<?php

namespace FlexibleShippingUspsVendor\WPDesk\HttpClient\Curl\Exception;

use FlexibleShippingUspsVendor\WPDesk\HttpClient\HttpClientRequestException;
/**
 * Base class for all curl exceptions.
 *
 * @package WPDesk\HttpClient\Curl\Exception
 */
class CurlException extends \RuntimeException implements HttpClientRequestException
{
}
