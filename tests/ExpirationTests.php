<?php

namespace Neat\Cache\Test;

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
    abstract public function cache($ttl = null): CacheInterface;

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

    public function testExpirationWithInvalidTtl()
    {
        $this->expectException(InvalidArgumentException::class);

        $cache = $this->cache();
        $cache->set('key', 'value', true);
    }

    public function testExpirationWithInvalidDefaultTtl()
    {
        $this->expectException(InvalidArgumentException::class);

        $this->cache(true);
    }
}
