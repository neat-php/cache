<?php

namespace Neat\Cache\Test;

use DateInterval;
use Psr\SimpleCache\CacheInterface;

/**
 * Miss tests
 *
 * @method void assertFalse($argument)
 * @method void assertNull($argument)
 */
trait MissTests
{
    /**
     * Create cache
     *
     * @param DateInterval|int|null $ttl
     * @return CacheInterface
     */
    abstract public function cache($ttl = null): CacheInterface;

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

    /**
     * Test hit multiple
     */
    public function testMissMultiple()
    {
        $cache = $this->cache();
        $cache->setMultiple($data = [
            'a' => 1,
            'b' => 2,
            'i' => true,
            'j' => false,
            'r' => [],
            's' => [1, 2, 3],
            't' => ['a' => 1, 'b' => 2, 'c' => 3],
            'x' => (object) [],
            'y' => (object) ['property' => 'value'],
        ]);
        $cache->deleteMultiple(['i', 'r', 't']);
        $data = array_merge($data, ['i' => null, 'r' => null, 't' => null]);

        $this->assertEquals(
            $data,
            iterator_to_array($cache->getMultiple(array_keys($data)))
        );
        $this->assertEquals(
            ['a' => 1, 'c' => null, 'r' => null],
            iterator_to_array($cache->getMultiple(['a', 'c', 'r']))
        );
        $this->assertEquals(
            ['a' => 1, 'c' => 'default', 'r' => 'default'],
            iterator_to_array($cache->getMultiple(['a', 'c', 'r'], 'default'))
        );
    }

}
