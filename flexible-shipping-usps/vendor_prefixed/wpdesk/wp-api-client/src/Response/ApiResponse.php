<?php

namespace FlexibleShippingUspsVendor\WPDesk\ApiClient\Response;

interface ApiResponse extends Response
{
    /**
     * Get links structure to the other request
     *
     * @return array
     */
    public function getLinks();
    /**
     * Is it a BAD REQUEST response
     *
     * @return bool
     */
    public function isBadRequest();
    /**
     * Is it a FATAL ERROR response
     *
     * @return bool
     */
    public function isServerFatalError();
    /**
     * Is requested resource exists
     *
     * @return bool
     */
    public function isNotExists();
}
