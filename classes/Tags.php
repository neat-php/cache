<?php

namespace Neat\Cache;

use DateInterval;
use Psr\SimpleCache\CacheInterface;

interface Tags extends CacheInterface
{
    /**
     * Set value with tags
     *
     * @param string                $key
     * @param mixed                 $value
     * @param DateInterval|int|null $ttl (optional)
     * @param array                 $tags
     * @return bool
     */
    public function set($key, $value, $ttl = null, array $tags = []);

    /**
     * Tag entry
     *
     * @param string $key
     * @param string $tag
     */
    public function tag(string $key, string $tag);

    /**
     * Untag entry
     *
     * @param string $key
     * @param string $tag
     */
    public function untag(string $key, string $tag);

    /**
     * Get tags
     *
     * @param string $key
     * @return string[]
     */
    public function tags(string $key): array;

    /**
     * Get keys
     *
     * @param string $tag
     * @return string[]
     */
    public function keys(string $tag): array;
}
