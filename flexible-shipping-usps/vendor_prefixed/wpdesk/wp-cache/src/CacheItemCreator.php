<?php

namespace FlexibleShippingUspsVendor\WPDesk\Cache;

/**
 * Cache item creator.
 *
 * Interface CacheItemCreator
 * @package WPDesk\Cache
 */
interface CacheItemCreator
{
    /**
     * Create item to cache.
     *
     * @param object $object
     * @return mixed
     */
    public function createCacheItem($object);
}
