<?php

namespace FlexibleShippingUspsVendor\Octolize\Usps\DomesticPrices\Request;

use FlexibleShippingUspsVendor\Octolize\Usps\DomesticPrices\Model\DomesticPricesSearchParameters;
use FlexibleShippingUspsVendor\WPDesk\ApiClient\Request\BasicRequest;
class TotalRatesSearch extends BasicRequest
{
    /**
     * Endpoint.
     *
     * @var string
     */
    protected $endPoint = 'prices/v3/total-rates/search';
    // phpcs:ignore.
    /**
     * Method.
     *
     * @var string
     */
    protected $method = 'POST';
    private string $token;
    private DomesticPricesSearchParameters $search_parameters;
    public function __construct(string $token, DomesticPricesSearchParameters $search_parameters)
    {
        $this->token = $token;
        $this->search_parameters = $search_parameters;
    }
    public function getBody(): array
    {
        return $this->search_parameters->jsonSerialize();
    }
    public function getHeaders(): array
    {
        return ['Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . $this->token];
    }
}
