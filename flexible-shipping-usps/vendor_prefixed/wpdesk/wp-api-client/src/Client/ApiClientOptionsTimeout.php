<?php

namespace FlexibleShippingUspsVendor\WPDesk\ApiClient\Client;

interface ApiClientOptionsTimeout extends ApiClientOptions
{
    /**
     * @return int
     */
    public function getTimeout();
}
