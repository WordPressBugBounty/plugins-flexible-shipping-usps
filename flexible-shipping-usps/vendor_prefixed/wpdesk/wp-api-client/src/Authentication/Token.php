<?php

namespace FlexibleShippingUspsVendor\WPDesk\ApiClient\Authentication;

interface Token
{
    /**
     * Get string to perform authentication
     *
     * @return string
     */
    public function getAuthString();
    /**
     * Is token expired or very soon to be expired?
     *
     * @return bool
     */
    public function isExpired();
    /**
     * Validates token signature
     *
     * @return bool
     */
    public function isSignatureValid();
    /**
     * @return string
     */
    public function __toString();
}
