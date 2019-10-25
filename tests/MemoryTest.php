<?php

namespace Neat\Cache\Test;

use Neat\Cache\Memory;
use Psr\SimpleCache\CacheInterface;

class MemoryTest extends Abstraction
{
    public function cache(): CacheInterface
    {
        return new Memory();
    }
}
