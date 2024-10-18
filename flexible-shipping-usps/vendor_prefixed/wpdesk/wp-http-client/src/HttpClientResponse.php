<?php

namespace FlexibleShippingUspsVendor\WPDesk\HttpClient;

class HttpClientResponse
{
    /** @var  string */
    private $headers;
    /** @var  string */
    private $body;
    /** @var int */
    private $code;
    /**
     * HttpClientResponse constructor.
     * @param string $headers
     * @param string $body
     * @param int $code
     */
    public function __construct($headers, $body, $code)
    {
        $this->headers = $headers;
        $this->body = $body;
        $this->code = $code;
    }
    /**
     * @return array
     */
    public function getHeaders()
    {
        $headers = array();
        $headers_rows = explode("\r\n", $this->headers);
        foreach ($headers_rows as $headers_row) {
            $header = explode(": ", $headers_row);
            $headers[$header[0]] = isset($header[1]) ? $header[1] : $header[0];
        }
        return $headers;
    }
    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }
    /**
     * @return int
     */
    public function getResponseCode()
    {
        return $this->code;
    }
}
