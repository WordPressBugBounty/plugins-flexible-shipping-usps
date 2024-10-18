<?php

namespace FlexibleShippingUspsVendor\WPDesk\ApiClient\Response\Traits;

use FlexibleShippingUspsVendor\WPDesk\ApiClient\Response\AuthApiResponse;
trait AuthApiResponseDecorator
{
    use ApiResponseDecorator;
    /**
     * @return bool
     */
    public function isBadCredentials()
    {
        return $this->getResponseCode() === AuthApiResponse::RESPONSE_CODE_BAD_CREDENTIALS;
    }
    /**
     * Is bad credential because token expires
     *
     * @return bool
     */
    public function isTokenExpired()
    {
        return $this->isBadCredentials();
    }
    /**
     * Is bad credential because token is invalid
     *
     * @return bool
     */
    public function isTokenInvalid()
    {
        return $this->isBadCredentials();
    }
}
