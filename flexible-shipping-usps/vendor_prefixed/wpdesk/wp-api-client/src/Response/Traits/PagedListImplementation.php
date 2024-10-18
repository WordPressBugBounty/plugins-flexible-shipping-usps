<?php

namespace FlexibleShippingUspsVendor\WPDesk\ApiClient\Response\Traits;

trait PagedListImplementation
{
    /*
     * @return array
     */
    public function getRawPage()
    {
        $body = $this->getResponseBody();
        if ($body['_embedded'] !== null && $body['_embedded']['item'] !== null) {
            return $body['_embedded']['item'];
        }
        return [];
    }
    /**
     * @return int
     */
    public function getPageCount()
    {
        return (int) floor($this->getItemCount() / $this->getItemsPerPage());
    }
    /**
     * @return int
     */
    public function getItemsPerPage()
    {
        $body = $this->getResponseBody();
        return (int) $body['itemsPerPage'];
    }
    /**
     * @return int
     */
    public function getItemCount()
    {
        $body = $this->getResponseBody();
        return (int) $body['totalItems'];
    }
}
