<?php

namespace Neat\Cache\Test;

use PHPUnit\Framework\TestCase;
use Psr\SimpleCache\CacheInterface;

abstract class Abstraction extends TestCase
{
    abstract public function cache(): CacheInterface;

    public function testEmpty()
    {
        $cache = $this->cache();

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->assertFalse($cache->has('x'));
        /** @noinspection PhpUnhandledExceptionInspection */
        $this->assertNull($cache->get('x'));
    }
}
