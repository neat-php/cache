<?php

namespace Neat\Cache\Test;

use Neat\Cache\Memory;
use PHPUnit\Framework\TestCase;
use Psr\SimpleCache\CacheInterface;

class MemoryTest extends TestCase
{
    use HitTests;
    use MissTests;
    use ExpirationTests;
    use ValidationTests;

    public function cache($ttl = null): CacheInterface
    {
        return new Memory([], $ttl);
    }
}
