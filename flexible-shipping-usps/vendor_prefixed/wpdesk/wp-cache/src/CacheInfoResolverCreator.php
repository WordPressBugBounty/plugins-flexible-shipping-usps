<?php

namespace FlexibleShippingUspsVendor\WPDesk\Cache;

/**
 * Cache item creator.
 *
 * Interface CacheItemCreator
 * @package WPDesk\Cache
 */
interface CacheInfoResolverCreator
{
    /**
     * Create resolvers.
     *
     * @return CacheInfoResolver[]
     */
    public function createResolvers();
}
