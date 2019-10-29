<?php

namespace Neat\Cache\Test;

use DateInterval;
use Neat\Cache\Memory;
use PHPUnit\Framework\TestCase;

class MemoryTest extends TestCase
{
    use HitTests;
    use MissTests;
    use ExpirationTests;
    use ValidationTests;

    /**
     * Create cache
     *
     * @param DateInterval|int|null $ttl
     * @return Memory
     */
    public function cache($ttl = null)
    {
        return new Memory([], $ttl);
    }
}
