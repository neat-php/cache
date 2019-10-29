<?php

namespace Neat\Cache\Test;

use DateInterval;
use Psr\SimpleCache\CacheInterface;

/**
 * Hit tests
 *
 * @method void assertFalse($argument)
 * @method void assertNull($argument)
 */
trait HitTests
{
    abstract public function cache($ttl = null): CacheInterface;

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
     * Test hit with TTL
     *
     * @dataProvider hitData
     * @param mixed $value
     */
    public function testHitDateIntervalWithTtl($value)
    {
        $cache = $this->cache();
        $cache->set('key', $value, new DateInterval('PT10S'));

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

    /**
     * Test hit with default TTL
     *
     * @dataProvider hitData
     * @param mixed $value
     */
    public function testHitWithDefaultDateIntervalTtl($value)
    {
        $cache = $this->cache(new DateInterval('PT10S'));
        $cache->set('key', $value);

        $this->assertTrue($cache->has('key'));
        $this->assertSame(serialize($value), serialize($cache->get('key')));
    }

    /**
     * Test hit multiple
     */
    public function testHitMultiple()
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

        /** @noinspection PhpParamsInspection */
        $this->assertEquals(
            $data,
            iterator_to_array($cache->getMultiple(array_keys($data)))
        );
        /** @noinspection PhpParamsInspection */
        $this->assertEquals(
            ['a' => 1, 'c' => null, 'r' => []],
            iterator_to_array($cache->getMultiple(['a', 'c', 'r']))
        );
        /** @noinspection PhpParamsInspection */
        $this->assertEquals(
            ['a' => 1, 'c' => 'default', 'r' => []],
            iterator_to_array($cache->getMultiple(['a', 'c', 'r'], 'default'))
        );
    }
}
