<?php

namespace FlexibleShippingUspsVendor\WPDesk\ApiClient\Response;

interface Response
{
    /**
     * @return int
     */
    public function getResponseCode();
    /** @return array */
    public function getResponseBody();
    /** @return array */
    public function getResponseHeaders();
    /** @return array */
    public function getResponseErrorBody();
    /**
     * Is any error occured
     *
     * @return bool
     */
    public function isError();
    /**
     * Is maintenance
     *
     * @return bool
     */
    public function isMaintenance();
    /**
     * Get platform version hash string.
     *
     * @return bool|string
     */
    public function getPlatformVersionHash();
}
