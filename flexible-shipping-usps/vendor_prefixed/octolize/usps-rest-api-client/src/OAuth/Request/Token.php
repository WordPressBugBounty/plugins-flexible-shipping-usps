<?php

namespace FlexibleShippingUspsVendor\Octolize\Usps\OAuth\Request;

use FlexibleShippingUspsVendor\Octolize\Usps\OAuth\Keys;
use FlexibleShippingUspsVendor\WPDesk\ApiClient\Request\BasicRequest;
class Token extends BasicRequest
{
    /**
     * Endpoint.
     *
     * @var string
     */
    protected $endPoint = 'oauth2/v3/token';
    // phpcs:ignore.
    /**
     * Method.
     *
     * @var string
     */
    protected $method = 'POST';
    private Keys $keys;
    public function __construct(Keys $keys, string $scope)
    {
        $this->keys = $keys;
        $this->scope = $scope;
    }
    public function getBody(): array
    {
        return ['grant_type' => 'client_credentials', 'client_id' => $this->keys->get_client_id(), 'client_secret' => $this->keys->get_client_secret(), 'scope' => $this->scope];
    }
    public function getHeaders(): array
    {
        return ['Content-Type' => 'application/x-www-form-urlencoded'];
    }
}
