<?php

namespace FlexibleShippingUspsVendor\Octolize\Usps;

class RestApi
{
    private OAuthApi $oauth_api;
    private DomesticPricesApi $domestic_prices_api;
    private InternationalPricesApi $international_prices_api;
    public function __construct(OAuthApi $oauth_api, DomesticPricesApi $domestic_prices_api, InternationalPricesApi $international_prices_api)
    {
        $this->oauth_api = $oauth_api;
        $this->domestic_prices_api = $domestic_prices_api;
        $this->international_prices_api = $international_prices_api;
    }
    public function get_domestic_prices_api(): DomesticPricesApi
    {
        return $this->domestic_prices_api;
    }
    public function get_international_prices_api(): InternationalPricesApi
    {
        return $this->international_prices_api;
    }
    public function get_oauth_api(): OAuthApi
    {
        return $this->oauth_api;
    }
}
