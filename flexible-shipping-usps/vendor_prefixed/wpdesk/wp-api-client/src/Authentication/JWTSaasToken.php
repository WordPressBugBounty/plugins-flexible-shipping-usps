<?php

namespace FlexibleShippingUspsVendor\WPDesk\ApiClient\Authentication;

class JWTSaasToken implements Token
{
    const SHOP_ID_PARAM = 'shop';
    const ROLE_PARAM = 'ROLE_SHOP';
    /** @var JWTToken */
    private $token;
    /**
     * JWTToken constructor.
     * @param string $token
     */
    public function __construct(JWTToken $token)
    {
        $this->token = $token;
    }
    public function getAuthString()
    {
        return $this->token->getAuthString();
    }
    public function isExpired()
    {
        return $this->token->isExpired();
    }
    public function isSignatureValid()
    {
        return $this->token->isSignatureValid();
    }
    public function __toString()
    {
        return $this->token->__toString();
    }
    /**
     * If there is shop id in the token
     *
     * @return bool
     */
    public function hasShopId()
    {
        $info = $this->token->getDecodedPublicTokenInfo();
        return !empty($info[self::SHOP_ID_PARAM]) && in_array(self::ROLE_PARAM, $info['roles']);
    }
    /**
     * Get shop id from token
     *
     * @return int
     */
    public function getShopId()
    {
        $info = $this->token->getDecodedPublicTokenInfo();
        return (int) $info[self::SHOP_ID_PARAM];
    }
}
