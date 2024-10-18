<?php

namespace FlexibleShippingUspsVendor\WPDesk\ApiClient\Authentication;

class JWTToken implements Token
{
    const CONSIDER_EXPIRED_WHEN_LESS = 2;
    const EXPIRED_IN_SECONDS_PARAM = 'exp';
    /** @var  string */
    private $token;
    /**
     * JWTToken constructor.
     * @param string $token
     */
    public function __construct($token)
    {
        $this->token = $token;
    }
    /**
     * @return string
     */
    public function __toString()
    {
        return $this->token;
    }
    /**
     * Get string to perform authentication
     *
     * @return string
     */
    public function getAuthString()
    {
        return 'Bearer ' . $this->__toString();
    }
    /**
     * Returns public data from token
     *
     * @return array
     */
    public function getDecodedPublicTokenInfo()
    {
        $tokenParts = explode('.', $this->__toString());
        if (!empty($tokenParts[1])) {
            $infoPart = base64_decode($tokenParts[1]);
            return json_decode($infoPart, \true);
        }
        return [];
    }
    /**
     * Is token expired or very soon to be expired?
     *
     * @return bool
     */
    public function isExpired()
    {
        $tokenInfo = $this->getDecodedPublicTokenInfo();
        if (!empty($tokenInfo[self::EXPIRED_IN_SECONDS_PARAM])) {
            return $tokenInfo[self::EXPIRED_IN_SECONDS_PARAM] - time() < self::CONSIDER_EXPIRED_WHEN_LESS;
        }
        return \true;
    }
    /**
     * Validates token signature
     *
     * @return bool
     */
    public function isSignatureValid()
    {
        // @TODO
        return \true;
    }
}
