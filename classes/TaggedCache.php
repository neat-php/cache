<?php

namespace Neat\Cache;

use Psr\SimpleCache\CacheInterface;

interface TaggedCache extends CacheInterface
{
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
