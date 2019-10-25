<?php

namespace Neat\Cache;

use DateInterval;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class File extends Abstraction
{
    /** @var string */
    private $path;

    /**
     * Constructor
     *
     * @param string                $path
     * @param DateInterval|int|null $ttl (optional)
     */
    public function __construct(string $path, $ttl = null)
    {
        $this->path = rtrim($path, DIRECTORY_SEPARATOR);
        $this->ttl  = $ttl;
    }

    /**
     * Has value?
     *
     * @param string $key
     * @return bool
     * @throws InvalidArgumentException
     */
    public function has($key)
    {
        $this->validate($key);

        $meta = $this->meta($key);
        if (!$meta) {
            return false;
        }

        if (isset($meta->expires) && time() > $meta->expires) {
            $this->delete($key);

            return false;
        }

        return true;
    }

    /**
     * Get value
     *
     * @param string $key
     * @param mixed  $default (optional)
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function get($key, $default = null)
    {
        if (!$this->has($key)) {
            return $default;
        }

        $file = $this->itemPath($key) . '/value';
        if (!file_exists($file)) {
            return $default;
        }

        $value = file_get_contents($file);
        if ($value === false) {
            return $default;
        }

        return unserialize($value);
    }

    /**
     * Set value
     *
     * @param string                $key
     * @param mixed                 $value
     * @param DateInterval|int|null $ttl (optional)
     * @param array                 $tags
     * @return bool
     */
    public function set($key, $value, $ttl = null, array $tags = [])
    {
        $this->validate($key);

        $path = $this->createPath($this->itemPath($key));

        file_put_contents($path . '/value', serialize($value));
        file_put_contents($path . '/meta', json_encode([
            'path'    => $path,
            'tags'    => $tags,
            'expires' => $this->expiration($ttl),
        ]));

        foreach ($tags as $tag) {
            $this->createPath(dirname($file = $this->tagFile($key, $tag)));
            file_put_contents($file, $key);
        }

        return true;
    }

    /**
     * Delete value
     *
     * @param string $key
     * @return bool
     * @throws InvalidArgumentException
     */
    public function delete($key)
    {
        $this->validate($key);

        $meta = $this->meta($key);

        foreach ($meta->tags ?? [] as $tag) {
            $file = $this->tagFile($tag, $key);
            if (file_exists($file)) {
                unlink($file);
            }
        }

        if (isset($meta->path)) {
            $this->clearPath($meta->path);
        }

        return true;
    }

    /**
     * Clear values
     *
     * @return bool
     */
    public function clear()
    {
        $this->clearPath($this->path);

        return true;
    }

    /**
     * Get item meta information
     *
     * @param string $key
     * @return object|null
     */
    public function meta($key)
    {
        $filename = $this->itemPath($key) . '/meta';
        if (!file_exists($filename)) {
            return null;
        }

        return json_decode(file_get_contents($filename)) ?: null;
    }

    /**
     * Get cache ids by tag
     *
     * @param string $tag
     * @return string[]
     */
    public function keysByTag($tag)
    {
        $path = $this->tagPath($tag);
        if (!file_exists($path)) {
            return [];
        }

        $directory = new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS);
        $files     = new RecursiveIteratorIterator($directory, RecursiveIteratorIterator::CHILD_FIRST);

        $keys = [];
        foreach ($files as $file) {
            if ($file->isFile()) {
                $keys[] = file_get_contents($file);
            }
        }

        return $keys;
    }

    /**
     * Clear path
     *
     * @param string $path
     */
    protected function clearPath(string $path)
    {
        $directory = new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS);
        $files     = new RecursiveIteratorIterator($directory, RecursiveIteratorIterator::CHILD_FIRST);

        foreach ($files as $file) {
            if ($file->isDir) {
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }
    }

    /**
     * Create path
     *
     * @param string $path
     * @return string
     */
    protected function createPath(string $path): string
    {
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        return $path;
    }

    protected function hashPath(string $key): string
    {
        $hash = md5($key);

        return substr($hash, 0, 2) . '/' . substr($hash, 2);
    }

    protected function itemPath(string $key): string
    {
        return $this->path . '/item/' . $this->hashPath($key);
    }

    protected function tagPath(string $tag): string
    {
        return $this->path . '/tag/' . $this->hashPath($tag);
    }

    protected function tagFile(string $tag, string $key): string
    {
        return $this->tagPath($tag) . '/' . $this->hashPath($key);
    }
}
