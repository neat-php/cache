<?php

namespace Neat\Cache\Test;

use Psr\SimpleCache\CacheInterface;

/**
 * Miss tests
 *
 * @method void assertFalse($argument)
 * @method void assertNull($argument)
 */
trait MissTests
{
    abstract public function cache(int $ttl = null): CacheInterface;

    public function testMiss()
    {
        $cache = $this->cache();

        $this->assertFalse($cache->has('x'));
        $this->assertNull($cache->get('x'));
        $this->assertSame('default', $cache->get('x', 'default'));
    }

    public function testMissAfterDelete()
    {
        $cache = $this->cache();
        $cache->set('key', 'value');
        $cache->delete('key');

        $this->assertFalse($cache->has('key'));
        $this->assertNull($cache->get('key'));
        $this->assertSame('default', $cache->get('key', 'default'));
    }

    public function testMissAfterClear()
    {
        $cache = $this->cache();
        $cache->set('key', 'value');
        $cache->clear();

        $this->assertFalse($cache->has('key'));
        $this->assertNull($cache->get('key'));
        $this->assertSame('default', $cache->get('key', 'default'));
    }

    public function testMissDueToCaseSensitivity()
    {
        $cache = $this->cache();
        $cache->set('key', 'value');

        $this->assertFalse($cache->has('KeY'));
        $this->assertNull($cache->get('KeY'));
        $this->assertSame('default', $cache->get('KeY', 'default'));
    }
}
