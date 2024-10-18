<?php

namespace FlexibleShippingUspsVendor\WPDesk\ApiClient\Request;

class BasicRequest implements Request
{
    /** @var string */
    protected $method;
    /** @var array */
    protected $data;
    /** @var string */
    protected $endPoint;
    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }
    /**
     * Return endpoint in format /[^/]+/
     *
     * @return string
     */
    public function getEndpoint()
    {
        return '/' . trim($this->endPoint, '/');
    }
    /**
     * Returns array of http headers
     *
     * @return array
     */
    public function getHeaders()
    {
        return array();
    }
    /**
     * Return unserialized request body as array
     *
     * @return array
     */
    public function getBody()
    {
        return $this->data;
    }
}
