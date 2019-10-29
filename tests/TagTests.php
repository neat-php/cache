<?php

namespace Neat\Cache\Test;

use DateInterval;
use Neat\Cache\Tags;

/**
 * Tag tests
 *
 * @method void assertFalse($argument)
 * @method void assertNull($argument)
 * @method void assertSame($expectation, $argument)
 */
trait TagTests
{
    /**
     * Create cache
     *
     * @param DateInterval|int|null $ttl
     * @return Tags
     */
    abstract public function cache($ttl = null);

    /**
     * Test without tags
     */
    public function testWithoutTags()
    {
        $cache = $this->cache();
        $cache->set('key', 'value');

        $this->assertSame([], $cache->tags('key'));
        $this->assertSame([], $cache->tags('unknown'));
    }

    /**
     * Test with tags
     */
    public function testWithTags()
    {
        $cache = $this->cache();
        $cache->set('key', 'value', null, ['tag1']);
        $cache->set('another', 'value', null, ['tag1', 'tag2']);

        $this->assertSame(['tag1'], $cache->tags('key'));
        $this->assertSame(['tag1', 'tag2'], $cache->tags('another'));
        $this->assertSame(['key', 'another'], $cache->keys('tag1'));
        $this->assertSame(['another'], $cache->keys('tag2'));
    }

    /**
     * Test tag
     */
    public function testTag()
    {
        $cache = $this->cache();
        $cache->set('key', 'value');

        $cache->tag('key', 'tag1');
        $this->assertSame(['tag1'], $cache->tags('key'));

        $cache->tag('key', 'tag2');
        $this->assertSame(['tag1', 'tag2'], $cache->tags('key'));
    }

    /**
     * Test untag
     */
    public function testUntag()
    {
        $cache = $this->cache();
        $cache->set('key', 'value', null, ['tag1', 'tag2']);

        $cache->untag('key', 'tag1');
        $this->assertSame(['tag2'], $cache->tags('key'));
        $this->assertSame([], $cache->keys('tag1'));
        $this->assertSame(['key'], $cache->keys('tag2'));

        $cache->untag('key', 'tag2');
        $this->assertSame([], $cache->tags('key'));
        $this->assertSame([], $cache->keys('tag1'));
        $this->assertSame([], $cache->keys('tag2'));
    }

    public function testUnknownTag()
    {
        $cache = $this->cache();

        $this->assertSame([], $cache->keys('unknown'));
    }

    /**
     * Test delete tagged
     */
    public function testDeleteTagged()
    {
        $cache = $this->cache();
        $cache->set('key', 'value', null, ['tag1']);
        $cache->set('expendable', 'value', null, ['tag1', 'tag2']);
        $cache->set('another', 'value', null, ['tag2']);
        $cache->delete('expendable');

        $this->assertSame(['tag1'], $cache->tags('key'));
        $this->assertSame([], $cache->tags('expendable'));
        $this->assertSame(['tag2'], $cache->tags('another'));

        $this->assertSame(['key'], $cache->keys('tag1'));
        $this->assertSame(['another'], $cache->keys('tag2'));

    }
}
