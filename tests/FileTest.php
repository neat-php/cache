<?php

namespace Neat\Cache\Test;

use Neat\Cache\File;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Psr\SimpleCache\CacheInterface;

class FileTest extends TestCase
{
    use HitTests;
    use MissTests;
    use ExpirationTests;
    use ValidationTests;

    public function cache(int $ttl = null): CacheInterface
    {
        $root = vfsStream::setup();

        return new File($root->url(), $ttl);
    }
}
