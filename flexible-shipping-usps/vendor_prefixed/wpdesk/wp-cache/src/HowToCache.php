<?php

namespace FlexibleShippingUspsVendor\WPDesk\Cache;

/**
 * How to cache item.
 *
 * Class HowToCache
 * @package WPDesk\Cache
 */
class HowToCache
{
    /**
     * Cache key.
     *
     * @var string
     */
    public $cacheKey;
    /**
     * Cache TTL.
     *
     * @var int
     */
    public $cacheTtl;
    /**
     * HowToCache constructor.
     *
     * @param string $cacheKey Cache key.
     * @param int    $cacheTtl Cache TTL.
     */
    public function __construct($cacheKey, $cacheTtl = 3600)
    {
        $this->cacheKey = $cacheKey;
        $this->cacheTtl = $cacheTtl;
    }
    /**
     * @return string
     */
    public function getCacheKey()
    {
        return $this->cacheKey;
    }
    /**
     * @param string $cacheKey
     */
    public function setCacheKey($cacheKey)
    {
        $this->cacheKey = $cacheKey;
    }
    /**
     * @return int
     */
    public function getCacheTtl()
    {
        return $this->cacheTtl;
    }
    /**
     * @param int $cacheTtl
     */
    public function setCacheTtl($cacheTtl)
    {
        $this->cacheTtl = $cacheTtl;
    }
}
