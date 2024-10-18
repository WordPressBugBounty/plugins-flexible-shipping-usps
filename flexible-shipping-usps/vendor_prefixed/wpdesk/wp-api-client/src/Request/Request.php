<?php

namespace FlexibleShippingUspsVendor\WPDesk\ApiClient\Request;

interface Request
{
    /**
     * @return string
     */
    public function getMethod();
    /**
     * @return array
     */
    public function getHeaders();
    /**
     * @return array
     */
    public function getBody();
    /**
     * @return string
     */
    public function getEndpoint();
}
