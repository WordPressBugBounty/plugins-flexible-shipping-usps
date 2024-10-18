<?php

namespace FlexibleShippingUspsVendor\WPDesk\Cache;

use FlexibleShippingUspsVendor\Psr\SimpleCache\CacheInterface;
/**
 * Class WordpressCache
 *
 * @package WPDesk\Cache
 */
class WordpressCache implements CacheInterface
{
    const TRANSIENT_NAME_PREFIX = 'wspc_';
    /**
     * Fetches a value from the cache.
     *
     * @param string $key     The unique key of this item in the cache.
     * @param mixed  $default Default value to return if the key does not exist.
     *
     * @return mixed The value of the item from the cache, or $default in case of cache miss.
     */
    public function get($key, $default = null)
    {
        $value = get_transient($this->prapareTransientNameForKey($key));
        if (\false === $value) {
            return $default;
        }
        return $value;
    }
    /**
     * Persists data in the cache, uniquely referenced by a key with an optional expiration TTL time.
     *
     * @param string   $key   The key of the item to store.
     * @param mixed    $value The value of the item to store, must be serializable.
     * @param null|int $ttl   Optional. Time until expiration in seconds. Default 0 (no expiration).
     *
     * @return bool True on success and false on failure.
     */
    public function set($key, $value, $ttl = 0)
    {
        return set_transient($this->prapareTransientNameForKey($key), $value, $ttl);
    }
    /**
     * Delete an item from the cache by its unique key.
     *
     * @param string $key The unique cache key of the item to delete.
     *
     * @return bool True if the item was successfully removed. False if there was an error.
     */
    public function delete($key)
    {
        return delete_transient($this->prapareTransientNameForKey($key));
    }
    /**
     * Wipes clean the entire cache's keys.
     *
     * @return bool True on success and false on failure.
     */
    public function clear()
    {
        global $wpdb;
        $transient_prefix = self::TRANSIENT_NAME_PREFIX;
        $transients = $wpdb->get_results("SELECT option_name AS name FROM {$wpdb->options} WHERE option_name LIKE '_transient_{$transient_prefix}%'");
        foreach ($transients as $transient) {
            $key = substr($transient->name, strlen("_transient_{$transient_prefix}"));
            delete_transient($this->prapareTransientNameForKey($key));
        }
        return \true;
    }
    /**
     * Obtains multiple cache items by their unique keys.
     *
     * @param iterable $keys    A list of keys that can obtained in a single operation.
     * @param mixed    $default Default value to return for keys that do not exist.
     *
     * @return iterable A list of key => value pairs. Cache keys that do not exist or are stale will have $default as
     * value.
     */
    public function getMultiple($keys, $default = null)
    {
        $multiple_values = array();
        foreach ($keys as $key) {
            $multiple_values[$key] = $this->get($key, $default);
        }
        return $multiple_values;
    }
    /**
     * Persists a set of key => value pairs in the cache, with an optional TTL.
     *
     * @param iterable $values A list of key => value pairs for a multiple-set operation.
     * @param null|int $ttl    Optional. The TTL value of this item. If no value is sent and
     *                                       the driver supports TTL then the library may set a default value
     *                                       for it or let the driver take care of that.
     *
     * @return bool True on success and false on failure.
     */
    public function setMultiple($values, $ttl = 0)
    {
        foreach ($values as $key => $value) {
            $set = $this->set($key, $value, $ttl);
            if (\false === $set) {
                return \false;
            }
        }
        return \true;
    }
    /**
     * Deletes multiple cache items in a single operation.
     *
     * @param iterable $keys A list of string-based keys to be deleted.
     *
     * @return bool True if the items were successfully removed. False if there was an error.
     */
    public function deleteMultiple($keys)
    {
        $delete_multiple = \true;
        foreach ($keys as $key) {
            $delete_multiple = $delete_multiple && $this->delete($key);
        }
        return $delete_multiple;
    }
    /**
     * Determines whether an item is present in the cache.
     *
     * NOTE: It is recommended that has() is only to be used for cache warming type purposes
     * and not to be used within your live applications operations for get/set, as this method
     * is subject to a race condition where your has() will return true and immediately after,
     * another script can remove it making the state of your app out of date.
     *
     * @param string $key The cache item key.
     *
     * @return bool
     *
     */
    public function has($key)
    {
        return !empty($this->get($key));
    }
    /**
     * Prepare transient name for given key.
     *
     * @param string $key Key.
     *
     * @return string
     */
    private function prapareTransientNameForKey($key)
    {
        return self::TRANSIENT_NAME_PREFIX . $key;
    }
}
