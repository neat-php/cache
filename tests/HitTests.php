<?php

namespace Neat\Cache\Test;

use Psr\SimpleCache\CacheInterface;

/**
 * Hit tests
 *
 * @method void assertFalse($argument)
 * @method void assertNull($argument)
 */
trait HitTests
{
    abstract public function cache(int $ttl = null): CacheInterface;

    public function hitData(): array
    {
        return [
            [true],
            [false],
            [1],
            [0],
            [-1],
            [0.5],
            [3.14],
            [[]],
            [[0, 1, 2]],
            [(object) []],
            [(object) ['property' => 'value']],
        ];
    }

    /**
     * Test hit without TTL
     *
     * @dataProvider hitData
     * @param mixed $value
     */
    public function testHitWithoutTtl($value)
    {
        $cache = $this->cache();
        $cache->set('key', $value);

        $this->assertTrue($cache->has('key'));
        $this->assertSame(serialize($value), serialize($cache->get('key')));
    }

    /**
     * Test hit with TTL
     *
     * @dataProvider hitData
     * @param mixed $value
     */
    public function testHitWithTtl($value)
    {
        $cache = $this->cache();
        $cache->set('key', $value, 10);

        $this->assertTrue($cache->has('key'));
        $this->assertSame(serialize($value), serialize($cache->get('key')));
    }

    /**
     * Test hit with default TTL
     *
     * @dataProvider hitData
     * @param mixed $value
     */
    public function testHitWithDefaultTtl($value)
    {
        $cache = $this->cache(10);
        $cache->set('key', $value);

        $this->assertTrue($cache->has('key'));
        $this->assertSame(serialize($value), serialize($cache->get('key')));
    }
}
