<?php

namespace FlexibleShippingUspsVendor\WPDesk\HttpClient\Curl;

use FlexibleShippingUspsVendor\WPDesk\HttpClient\HttpClient;
use FlexibleShippingUspsVendor\WPDesk\HttpClient\HttpClientResponse;
use FlexibleShippingUspsVendor\WPDesk\HttpClient\HttpClientRequestException;
class CurlClient implements HttpClient
{
    /** @var resource */
    private $curlResource;
    /** @var  string|null|boolean */
    private $rawResponse;
    /** @var int */
    private $httpResponseCode;
    /** @var array */
    private $headers = array();
    /**
     * @param string $url
     * @param string $body
     * @param array  $headers
     * @param int    $timeOut
     *
     * @return HttpClientResponse
     * @throws HttpClientRequestException
     */
    public function get($url, $body, array $headers, $timeOut)
    {
        return $this->send($url, 'GET', $body, $headers, $timeOut);
    }
    /**
     * @param string $url
     * @param string $method
     * @param string $body
     * @param array  $headers
     * @param int    $timeOut
     *
     * @return HttpClientResponse
     * @throws HttpClientRequestException
     */
    public function send($url, $method, $body, array $headers, $timeOut)
    {
        $this->initResource();
        try {
            $this->prepareConnection($url, $method, $body, $headers, $timeOut);
            $this->sendRequest();
            $this->throwExceptionIfError();
            $this->closeConnection();
            return $this->prepareResponse();
        } catch (\Exception $e) {
            $this->closeConnection();
            if ($e instanceof HttpClientRequestException) {
                throw $e;
            }
            throw CurlExceptionFactory::createDefaultException($e->getMessage(), $e->getCode(), $e);
        }
    }
    private function initResource()
    {
        $this->curlResource = \curl_init();
    }
    /**
     * Opens a new curl connection.
     *
     * @param string $url     The endpoint to send the request to.
     * @param string $method  The request method.
     * @param string $body    The body of the request.
     * @param array  $headers The request headers.
     * @param int    $timeOut The timeout in seconds for the request.
     */
    private function prepareConnection($url, $method, $body, array $headers, $timeOut)
    {
        $options = [
            \CURLOPT_CUSTOMREQUEST => $method,
            \CURLOPT_HTTPHEADER => $this->compileRequestHeaders($headers),
            \CURLOPT_URL => $url,
            \CURLOPT_CONNECTTIMEOUT => 25,
            \CURLOPT_TIMEOUT => $timeOut,
            \CURLOPT_RETURNTRANSFER => \true,
            // Return response as string
            \CURLOPT_HEADER => \false,
            // Enable header processing
            \CURLOPT_SSL_VERIFYHOST => 2,
            \CURLOPT_SSL_VERIFYPEER => \true,
            \CURLOPT_HEADERFUNCTION => function ($curl, $header) {
                $len = strlen($header);
                $this->headers[] = trim($header);
                return $len;
            },
        ];
        if (!empty($body)) {
            $options[\CURLOPT_POSTFIELDS] = $body;
        }
        \curl_setopt_array($this->curlResource, $options);
    }
    /**
     * Compiles the request headers into a curl-friendly format.
     *
     * @param array $headers The request headers.
     *
     * @return array
     */
    private function compileRequestHeaders(array $headers)
    {
        $return = [];
        foreach ($headers as $key => $value) {
            $return[] = $key . ': ' . $value;
        }
        return $return;
    }
    /**
     * Send the request and get the raw response from curl
     */
    private function sendRequest()
    {
        $this->rawResponse = \curl_exec($this->curlResource);
        $this->httpResponseCode = $this->getHttpResponseCode();
    }
    /** @return int */
    private function getHttpResponseCode()
    {
        return \intval(\curl_getinfo($this->curlResource, \CURLINFO_HTTP_CODE));
    }
    private function throwExceptionIfError()
    {
        $errorNumber = \curl_errno($this->curlResource);
        if ($errorNumber === 0) {
            return;
        }
        $errorMessage = \curl_error($this->curlResource);
        throw CurlExceptionFactory::createCurlException($errorMessage, $errorNumber);
    }
    /**
     * Closes an existing curl connection
     */
    private function closeConnection()
    {
        \curl_close($this->curlResource);
        $this->curlResource = null;
    }
    private function prepareResponse()
    {
        list($rawHeaders, $rawBody) = $this->extractResponseHeadersAndBody();
        return new HttpClientResponse($rawHeaders, $rawBody, $this->httpResponseCode);
    }
    /**
     * Extracts the headers and the body into a two-part array
     *
     * @return array
     */
    private function extractResponseHeadersAndBody()
    {
        $rawBody = $this->rawResponse;
        $rawHeaders = \trim(implode("\r\n", $this->headers));
        return [$rawHeaders, $rawBody];
    }
    /**
     * @param string $url
     * @param string $body
     * @param array  $headers
     * @param int    $timeOut
     *
     * @return HttpClientResponse
     * @throws HttpClientRequestException
     */
    public function post($url, $body, array $headers, $timeOut)
    {
        return $this->send($url, 'POST', $body, $headers, $timeOut);
    }
    /**
     * @param string $url
     * @param string $body
     * @param array  $headers
     * @param int    $timeOut
     *
     * @return HttpClientResponse
     * @throws HttpClientRequestException
     */
    public function delete($url, $body, array $headers, $timeOut)
    {
        return $this->send($url, 'DELETE', $body, $headers, $timeOut);
    }
    /**
     * @param string $url
     * @param string $body
     * @param array  $headers
     * @param int    $timeOut
     *
     * @return HttpClientResponse
     * @throws HttpClientRequestException
     */
    public function put($url, $body, array $headers, $timeOut)
    {
        return $this->send($url, 'PUT', $body, $headers, $timeOut);
    }
}
