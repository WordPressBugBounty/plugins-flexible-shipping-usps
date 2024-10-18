<?php

namespace FlexibleShippingUspsVendor\WPDesk\HttpClient;

interface HttpClient
{
    /**
     * @param string $url
     * @param string $body
     * @param array $headers
     * @param int $timeOut
     * @throw HttpClientRequestException
     * @return HttpClientResponse
     */
    public function put($url, $body, array $headers, $timeOut);
    /**
     * @param string $url
     * @param string $body
     * @param array $headers
     * @param int $timeOut
     * @throw HttpClientRequestException
     * @return HttpClientResponse
     */
    public function get($url, $body, array $headers, $timeOut);
    /**
     * @param string $url
     * @param string $body
     * @param array $headers
     * @param int $timeOut
     * @throw HttpClientRequestException
     * @return HttpClientResponse
     */
    public function post($url, $body, array $headers, $timeOut);
    /**
     * @param string $url
     * @param string $body
     * @param array $headers
     * @param int $timeOut
     * @throw HttpClientRequestException
     * @return HttpClientResponse
     */
    public function delete($url, $body, array $headers, $timeOut);
    /**
     * @param string $url
     * @param string $method
     * @param string $body
     * @param array $headers
     * @param int $timeOut
     * @throw HttpClientRequestException
     * @return HttpClientResponse
     */
    public function send($url, $method, $body, array $headers, $timeOut);
}
