<?php

namespace Neat\Cache\Test;

use DateInterval;
use Neat\Cache\Abstraction;
use Neat\Cache\InvalidArgumentException;

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
     * @return Abstraction
     */
    abstract public function cache($ttl = null);

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
