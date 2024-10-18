<?php

namespace FlexibleShippingUspsVendor\WPDesk\Cache;

use FlexibleShippingUspsVendor\Psr\SimpleCache\CacheInterface;
class CacheDispatcher
{
    /**
     * Cache info resolvers.
     *
     * @var CacheInfoResolver[]
     */
    private $resolvers;
    /**
     * Cache.
     *
     * @var CacheInterface
     */
    private $cache;
    /**
     * CacheDispatcher constructor.
     *
     * @param CacheInterface $cache
     * @param CacheInfoResolver[] $resolvers
     */
    public function __construct($cache, $resolvers)
    {
        $this->cache = $cache;
        $this->resolvers = $resolvers;
    }
    /**
     * Dispatch.
     *
     * @param object $object
     * @param CacheItemCreator $cacheItemCreator
     * @param CacheItemVerifier $cacheItemVerifier
     *
     * @return mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function dispatch($object, $cacheItemCreator, $cacheItemVerifier)
    {
        foreach ($this->resolvers as $resolver) {
            if ($resolver->isSupported($object)) {
                return $this->getOrCreateItem($resolver, $object, $cacheItemCreator, $cacheItemVerifier);
            }
        }
        return $cacheItemCreator->createCacheItem($object);
    }
    /**
     * Get item from cache.
     *
     * @param string $cacheKey Cache key.
     * @param CacheItemVerifier $cacheItemVerifier
     *
     * @return mixed|null
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    private function getItemFromCache($cacheKey, $cacheItemVerifier)
    {
        try {
            $responseSerialized = $this->cache->get($cacheKey);
        } catch (\Exception $e) {
            $responseSerialized = null;
        }
        if ($responseSerialized !== null) {
            $response = $cacheItemVerifier->getVerifiedItemOrNull(unserialize($responseSerialized));
            if (null !== $response) {
                return $response;
            }
        }
        return null;
    }
    /**
     * Get or create item.
     *
     * @param CacheInfoResolver $resolver
     * @param object $object
     * @param CacheItemCreator $cacheItemCreator
     * @param CacheItemVerifier $cacheItemVerifier
     *
     * @return mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    private function getOrCreateItem($resolver, $object, $cacheItemCreator, $cacheItemVerifier)
    {
        $howToCache = $resolver->prepareHowToCache($object);
        if ($resolver->shouldCache($object)) {
            $item = $this->getItemFromCache($howToCache->getCacheKey(), $cacheItemVerifier);
            if (null != $item) {
                return $item;
            }
        }
        $item = $cacheItemCreator->createCacheItem($object);
        if ($resolver->shouldCache($object)) {
            $this->cache->set($howToCache->getCacheKey(), serialize($item), $howToCache->getCacheTtl());
        }
        if ($resolver->shouldClearCache($object, $item)) {
            $this->cache->clear();
        } else {
            $this->cache->deleteMultiple($resolver->shouldClearKeys($object, $item));
        }
        return $item;
    }
}
