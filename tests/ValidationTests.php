<?php

namespace Neat\Cache\Test;

use Neat\Cache\InvalidArgumentException;
use Psr\SimpleCache\CacheInterface;

/**
 * Trait ValidationTests
 *
 * @method void expectException($class)
 */
trait ValidationTests
{
    abstract public function cache(int $ttl = null): CacheInterface;

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
}
