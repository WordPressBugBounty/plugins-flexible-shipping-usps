<?php

namespace FlexibleShippingUspsVendor\WPDesk\Cache;

/**
 * Cache item verifier.
 *
 * Interface CacheItemVerifier
 * @package WPDesk\Cache
 */
interface CacheItemVerifier
{
    /**
     * Get verified item or null.
     * Verifies item and returns it or null when item is not valid.
     *
     * @param $object
     * @return null|object
     */
    public function getVerifiedItemOrNull($object);
}
