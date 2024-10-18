<?php

namespace FlexibleShippingUspsVendor\WPDesk\ApiClient\Authentication;

/**
 * Null object pattern
 *
 * @package WPDesk\SaasPlatformClient\Authentication
 */
class NullToken implements Token
{
    public function __construct()
    {
    }
    /**
     * @return string
     */
    public function __toString()
    {
        return '';
    }
    /**
     * Get string to perform authentication
     *
     * @return string
     */
    public function getAuthString()
    {
        return '';
    }
    /**
     * Is token expired or very soon to be expired?
     *
     * @return bool
     */
    public function isExpired()
    {
        return \true;
    }
    /**
     * Validates token signature
     *
     * @return bool
     */
    public function isSignatureValid()
    {
        return \false;
    }
}
