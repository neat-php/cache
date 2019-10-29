<?php

namespace Neat\Cache\Test;

use DateInterval;
use Neat\Cache\File;
use Neat\Cache\Tags;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

class FileTest extends TestCase
{
    use HitTests;
    use MissTests;
    use TagTests;
    use ExpirationTests;
    use ValidationTests;

    /**
     * Create cache
     *
     * @param DateInterval|int|null $ttl
     * @param string                $path
     * @return Tags
     */
    public function cache($ttl = null, string $path = null)
    {
        $path = $path ?? vfsStream::setup()->url();

        return new File($path, $ttl);
    }

    /**
     * Test concurrent get and delete
     */
    public function testDeletedValue()
    {
        $root = vfsStream::setup();

        $cache = $this->cache(null, $root->url());
        $cache->set('key', 'value');

        // Delete just the value file, not the meta file
        unlink($root->url() . '/item/3c/6e0b8a9c15224a8228b9a98ca1531d/value');

        $this->assertTrue($cache->has('key'));
        $this->assertNull($cache->get('key'));
    }
}
