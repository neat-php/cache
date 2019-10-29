<?php

namespace Neat\Cache\Test;

use Psr\SimpleCache\CacheInterface;

/**
 * Expiration tests
 *
 * @method void assertFalse($argument)
 * @method void assertNull($argument)
 */
trait ExpirationTests
{
    abstract public function cache(int $ttl = null): CacheInterface;

    public function testExpirationWithZeroTtl()
    {
        $cache = $this->cache();

        $cache->set('key', 'value', 0);
        $this->assertFalse($cache->has('key'));
        $this->assertNull($cache->get('key'));
    }

    public function testExpirationWithNegativeTtl()
    {
        $cache = $this->cache();

        $cache->set('key', 'value', -10);
        $this->assertFalse($cache->has('key'));
        $this->assertNull($cache->get('key'));
    }
}
