<?php

namespace Neat\Cache\Test;

use DateInterval;
use Neat\Cache\InvalidArgumentException;
use Psr\SimpleCache\CacheInterface;

/**
 * Trait ValidationTests
 *
 * @method void expectException($class)
 */
trait ValidationTests
{
    /**
     * Create cache
     *
     * @param DateInterval|int|null $ttl
     * @return CacheInterface
     */
    abstract public function cache($ttl = null): CacheInterface;

    /**
     * @return array
     */
    public function invalidKeys(): array
    {
        return [
            [true],
            [1],
            [''],
            ['{'],
            ['}'],
            ['('],
            [')'],
            ['/'],
            ['\\'],
            ['@'],
            [':'],
        ];
    }

    /**
     * @return array
     */
    public function multipleInvalidKeys(): array
    {
        return [
            [null],
            [true],
            [1],
            ['x'],
            [['x', true, 'z']],
            [['x', 1, 'z']],
            [['x', '', 'z']],
            [['x', '{', 'z']],
            [['x', '}', 'z']],
            [['x', '(', 'z']],
            [['x', ')', 'z']],
            [['x', '/', 'z']],
            [['x', '\\', 'z']],
            [['x', '@', 'z']],
            [['x', ':', 'z']],
        ];
    }

    /**
     * @return array
     */
    public function setMultipleInvalidKeys(): array
    {
        return [
            [[0 => 'value']],
            [[1 => 'value']],
            [['' => 'value']],
            [['{' => 'value']],
            [['}' => 'value']],
            [['(' => 'value']],
            [[')' => 'value']],
            [['/' => 'value']],
            [['\\' => 'value']],
            [['@' => 'value']],
            [[':' => 'value']],
        ];
    }

    /**
     * Test has invalid key
     *
     * @param mixed $key
     * @dataProvider invalidKeys
     */
    public function testHasInvalidKey($key)
    {
        $this->expectException(InvalidArgumentException::class);

        $this->cache()->has($key);
    }

    /**
     * Test get invalid key
     *
     * @param mixed $key
     * @dataProvider invalidKeys
     */
    public function testGetInvalidKey($key)
    {
        $this->expectException(InvalidArgumentException::class);

        $this->cache()->get($key);
    }

    /**
     * Test set invalid key
     *
     * @param mixed $key
     * @dataProvider invalidKeys
     */
    public function testSetInvalidKey($key)
    {
        $this->expectException(InvalidArgumentException::class);

        $this->cache()->set($key, true);
    }

    /**
     * Test delete invalid key
     *
     * @param mixed $key
     * @dataProvider invalidKeys
     */
    public function testDeleteInvalidKey($key)
    {
        $this->expectException(InvalidArgumentException::class);

        $this->cache()->delete($key);
    }

    /**
     * Test get multiple invalid keys
     *
     * @param array|mixed $keys
     * @dataProvider multipleInvalidKeys
     */
    public function testGetMultipleInvalidKeys($keys)
    {
        $this->expectException(InvalidArgumentException::class);

        $iterator = $this->cache()->getMultiple($keys);
        foreach ($iterator as $value) {
            $this->assertNull($value);
        }
    }

    /**
     * Test set multiple invalid keys
     *
     * @param array|mixed $values
     * @dataProvider setMultipleInvalidKeys
     */
    public function testSetMultipleInvalidKeys($values)
    {
        $this->expectException(InvalidArgumentException::class);

        $this->cache()->setMultiple($values);
    }

    /**
     * Test delete multiple invalid keys
     *
     * @param array|mixed $keys
     * @dataProvider multipleInvalidKeys
     */
    public function testDeleteMultipleInvalidKeys($keys)
    {
        $this->expectException(InvalidArgumentException::class);

        $this->cache()->deleteMultiple($keys);
    }
}
