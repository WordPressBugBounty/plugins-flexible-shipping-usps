<?php

namespace FlexibleShippingUspsVendor\WPDesk\ApiClient\Client;

use FlexibleShippingUspsVendor\Psr\SimpleCache\CacheInterface;
use FlexibleShippingUspsVendor\WPDesk\Cache\CacheDispatcher;
use FlexibleShippingUspsVendor\WPDesk\Cache\CacheInfoResolverCreator;
use FlexibleShippingUspsVendor\WPDesk\Cache\CacheItemCreator;
use FlexibleShippingUspsVendor\WPDesk\Cache\CacheItemVerifier;
use FlexibleShippingUspsVendor\WPDesk\HttpClient\HttpClient;
use FlexibleShippingUspsVendor\WPDesk\ApiClient\Request\Request;
use FlexibleShippingUspsVendor\WPDesk\ApiClient\Response\Response;
use FlexibleShippingUspsVendor\WPDesk\ApiClient\Serializer\Serializer;
class CachedClient implements Client, CacheItemCreator, CacheItemVerifier
{
    /** @var Client */
    private $client;
    /** @var CacheInterface */
    private $cache;
    /**
     * @var CacheDispatcher
     */
    private $cacheDispatcher;
    /**
     * CachedClient constructor.
     *
     * @param Client $decorated Decorated client
     * @param CacheInterface $cache
     */
    public function __construct(Client $decorated, CacheInterface $cache)
    {
        $this->client = $decorated;
        $this->cache = $cache;
        $this->cacheDispatcher = new CacheDispatcher($cache, $this->getCacheInfoResolvers());
    }
    /**
     * Get cache info resolvers.
     *
     * @return RequestCacheInfoResolver[]
     */
    protected function getCacheInfoResolvers()
    {
        if ($this->client instanceof CacheInfoResolverCreator) {
            return $this->client->createResolvers();
        } else {
            return [new RequestCacheInfoResolver()];
        }
    }
    /**
     * Create item to cache.
     *
     * @param Request $request
     * @return Response
     */
    public function createCacheItem($request)
    {
        return $this->client->sendRequest($request);
    }
    /**
     * Verify cache item.
     *
     * @param $object
     * @return Response;
     */
    public function getVerifiedItemOrNull($object)
    {
        if ($object instanceof Response) {
            return $object;
        }
        return null;
    }
    /**
     * Send request.
     *
     * @param Request $request
     * @return mixed|Response
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function sendRequest(Request $request)
    {
        $response = $this->cacheDispatcher->dispatch($request, $this, $this);
        return $response;
    }
    /**
     * @return HttpClient
     */
    public function getHttpClient()
    {
        return $this->client->getHttpClient();
    }
    /**
     * @param HttpClient $client
     * @return mixed
     */
    public function setHttpClient(HttpClient $client)
    {
        return $this->client->setHttpClient($client);
    }
    /**
     * @return Serializer
     */
    public function getSerializer()
    {
        return $this->client->getSerializer();
    }
    /**
     * @return string
     */
    public function getApiUrl()
    {
        return $this->client->getApiUrl();
    }
}
