<?php

namespace FlexibleShippingUspsVendor\WPDesk\ApiClient\Client;

use FlexibleShippingUspsVendor\WPDesk\Cache\CacheInfoResolver;
use FlexibleShippingUspsVendor\WPDesk\Cache\HowToCache;
use FlexibleShippingUspsVendor\WPDesk\SaasPlatformClient\Request\AuthRequest;
use FlexibleShippingUspsVendor\WPDesk\SaasPlatformClient\Request\BasicRequest;
use FlexibleShippingUspsVendor\WPDesk\SaasPlatformClient\Request\Request;
use FlexibleShippingUspsVendor\WPDesk\SaasPlatformClient\Request\ShippingServicesSettings\PutSettingsRequest;
use FlexibleShippingUspsVendor\WPDesk\SaasPlatformClient\Request\Status\GetStatusRequest;
use FlexibleShippingUspsVendor\WPDesk\SaasPlatformClient\Response\ApiResponse;
use FlexibleShippingUspsVendor\WPDesk\SaasPlatformClient\Response\RawResponse;
class RequestCacheInfoResolver implements CacheInfoResolver
{
    const DEFAULT_CACHE_TTL = 86400;
    //24 hours
    const CACHE_TTL_ONE_MINUTE = 60;
    const OPTION_FS_SAAS_PLATFORM_VERSION_HASH = 'fs-saas-platform-version-hash';
    /**
     *
     * @param Request $request
     *
     * @return bool
     */
    private function prepareCacheKey($request)
    {
        return md5($request->getEndpoint());
    }
    /**
     *
     * @param Request $request
     *
     * @return bool
     */
    public function isSupported($request)
    {
        if ($request instanceof BasicRequest) {
            return \true;
        }
        return \false;
    }
    /**
     *
     * @param Request $request
     *
     * @return bool
     */
    public function shouldCache($request)
    {
        if ($request instanceof ConnectKeyInfoRequest) {
            return \false;
        }
        if ($request instanceof GetStatusRequest) {
            return \false;
        }
        if ($request instanceof BasicRequest) {
            if ('GET' === $request->getMethod()) {
                return \true;
            }
        }
        return \false;
    }
    /**
     *
     * @param Request $request
     *
     * @return HowToCache
     */
    public function prepareHowToCache($request)
    {
        $howToCache = new HowToCache($this->prepareCacheKey($request), self::DEFAULT_CACHE_TTL);
        return $howToCache;
    }
    /**
     * @param ApiResponse $response
     *
     * @return bool
     */
    private function isPlatformVersionFromResponseChanged(ApiResponse $response)
    {
        $stored_hash = get_option(self::OPTION_FS_SAAS_PLATFORM_VERSION_HASH, '');
        if ($stored_hash !== $response->getPlatformVersionHash()) {
            return \true;
        }
        return \false;
    }
    /**
     * @param ApiResponse $response
     */
    private function storePlatformVersionHashFromResponse(ApiResponse $response)
    {
        update_option(self::OPTION_FS_SAAS_PLATFORM_VERSION_HASH, $response->getPlatformVersionHash());
    }
    /**
     *
     * @param Request $request
     * @param mixed $item
     *
     * @return bool
     */
    public function shouldClearCache($request, $item)
    {
        if ($request instanceof PutSettingsRequest) {
            return \true;
        }
        if ($item instanceof ApiResponse && $this->isPlatformVersionFromResponseChanged($item)) {
            $this->storePlatformVersionHashFromResponse($item);
            return \true;
        }
        return \false;
    }
    /**
     *
     * @param Request $request
     * @param mixed $item
     *
     * @return string[]
     */
    public function shouldClearKeys($request, $item)
    {
        if ('GET' !== $request->getMethod()) {
            return [$this->prepareCacheKey($request)];
        }
        return [];
    }
}
