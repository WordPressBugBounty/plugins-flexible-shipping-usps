<?php

namespace FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\Usps\Api\Rest;

use FlexibleShippingUspsVendor\Octolize\Usps\OAuth\Keys;
use FlexibleShippingUspsVendor\Octolize\Usps\OAuthApi;
class OauthApiCached extends OAuthApi
{
    const TOKEN_OPTION = 'fs_usps_token';
    private OAuthApi $oauth_api;
    public function __construct(OAuthApi $oauth_api)
    {
        $this->oauth_api = $oauth_api;
    }
    public function get_token(): array
    {
        $token = get_option(self::TOKEN_OPTION, []);
        if (!is_array($token)) {
            $token = [];
        }
        if (empty($token) || !isset($token['expires_at']) || $token['expires_at'] < time() || !isset($token['keys_md5']) || $token['keys_md5'] !== $this->get_keys()->get_md5()) {
            $token = $this->oauth_api->get_token();
            $token['expires_at'] = time() + $token['expires_in'] - 60;
            $token['keys_md5'] = $this->get_keys()->get_md5();
            update_option(self::TOKEN_OPTION, $token);
        }
        return $token;
    }
    protected function get_keys(): Keys
    {
        return $this->oauth_api->get_keys();
    }
    public function delete_token(): void
    {
        delete_option(self::TOKEN_OPTION);
    }
}
