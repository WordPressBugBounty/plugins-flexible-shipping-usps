<?php

namespace FlexibleShippingUspsVendor\Octolize\Usps\OAuth;

class Keys
{
    private string $client_id;
    private string $client_secret;
    public function __construct(string $consumer_key, string $consumer_secret)
    {
        $this->client_id = $consumer_key;
        $this->client_secret = $consumer_secret;
    }
    public function get_client_id(): string
    {
        return $this->client_id;
    }
    public function get_client_secret(): string
    {
        return $this->client_secret;
    }
    public function get_md5(): string
    {
        return md5($this->client_id . $this->client_secret);
    }
}
