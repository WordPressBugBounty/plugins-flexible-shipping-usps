<?php

namespace FlexibleShippingUspsVendor\WPDesk\Cache;

interface CacheInfoResolver
{
    /**
     * Is supported.
     *
     * @param object $object
     *
     * @return bool
     */
    public function isSupported($object);
    /**
     * Should be cached.
     *
     * @param object $object
     *
     * @return bool
     */
    public function shouldCache($object);
    /**
     * Prepare how to cache.
     *
     * @param object $object
     *
     * @return HowToCache
     */
    public function prepareHowToCache($object);
    /**
     * Should clear cache.
     *
     * @param object $object
     * @param mixed $item
     *
     * @return bool
     */
    public function shouldClearCache($object, $item);
    /**
     * Should clear keys.
     *
     * @param object $object
     * @param mixed $item
     *
     * @return string[]
     */
    public function shouldClearKeys($object, $item);
}
