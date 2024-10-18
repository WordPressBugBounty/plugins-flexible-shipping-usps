<?php

namespace FlexibleShippingUspsVendor\WPDesk\ApiClient\Response;

use FlexibleShippingUspsVendor\WPDesk\ApiClient\Response\Exception\TriedExtractDataFromErrorResponse;
use FlexibleShippingUspsVendor\WPDesk\ApiClient\Response\Traits\ApiResponseDecorator;
/**
 * Response is protected in a way so when you try to get body of the response when an error occured you will get an exception
 *
 * Class ProtectedResponse
 * @package WPDesk\ApiClient\Response
 */
class ProtectedResponse implements Response
{
    use ApiResponseDecorator;
    public function getResponseBody()
    {
        if ($this->isError()) {
            throw TriedExtractDataFromErrorResponse::createWithClassInfo(get_class($this->rawResponse), $this->getResponseCode());
        }
        return $this->rawResponse->getResponseBody();
    }
}
