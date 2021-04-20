<?php

namespace Neat\Cache\Test;

use DateInterval;
use Neat\Cache\InvalidArgumentException;
use Psr\SimpleCache\CacheInterface;

/**
 * Expiration tests
 *
 * @method void assertFalse($argument)
 * @method void assertNull($argument)
 */
trait ExpirationTests
{
    /**
     * Create cache
     *
     * @param DateInterval|int|null $ttl
     * @return CacheInterface
     */
    abstract public function cache($ttl = null): CacheInterface;

    public function testExpirationWithZeroTtl(): void
    {
        $cache = $this->cache();

        $cache->set('key', 'value', 0);
        $this->assertFalse($cache->has('key'));
        $this->assertNull($cache->get('key'));
    }

    public function testExpirationWithNegativeTtl(): void
    {
        $cache = $this->cache();

        $cache->set('key', 'value', -10);
        $this->assertFalse($cache->has('key'));
        $this->assertNull($cache->get('key'));
    }

    public function testExpirationWithInvalidTtl(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $cache = $this->cache();
        $cache->set('key', 'value', true);
    }

    public function testExpirationWithInvalidDefaultTtl(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->cache(true);
    }
}
