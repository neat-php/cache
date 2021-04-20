<?php

namespace Neat\Cache;

use DateInterval;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class File extends Abstraction implements Tags
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

        $this->expiration();
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

        $expiration = $meta->expiration ?? null;
        if (isset($expiration) && $expiration <= time()) {
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

        return unserialize(file_get_contents($file));
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

        $expiration = $this->expiration($ttl);

        $path = $this->createPath($this->itemPath($key));

        file_put_contents($path . '/value', serialize($value));
        file_put_contents($path . '/meta', json_encode(compact('path', 'tags', 'expiration')));

        foreach ($tags as $tag) {
            $this->createPath(dirname($file = $this->tagFile($tag, $key)));
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
    private function meta($key)
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
    public function keys(string $tag): array
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
     * Get tags
     *
     * @param string $key
     * @return string[]
     */
    public function tags(string $key): array
    {
        return $this->meta($key)->tags ?? [];
    }

    /**
     * Tag entry
     *
     * @param string $key
     * @param string $tag
     */
    public function tag(string $key, string $tag)
    {
        if ($meta = $this->meta($key)) {
            $meta->tags[] = $tag;
            file_put_contents($this->itemPath($key) . '/meta', json_encode($meta));
        }

        $this->createPath(dirname($file = $this->tagFile($tag, $key)));

        file_put_contents($file, $key);
    }

    /**
     * Untag entry
     *
     * @param string $key
     * @param string $tag
     */
    public function untag(string $key, string $tag)
    {
        if ($meta = $this->meta($key)) {
            $meta->tags = array_values(array_diff($meta->tags ?? [], [$tag]));
            file_put_contents($this->itemPath($key) . '/meta', json_encode($meta));
        }

        if (file_exists($file = $this->tagFile($tag, $key))) {
            unlink($file);
        }
    }

    /**
     * Clear path
     *
     * @param string $path
     */
    protected function clearPath(string $path)
    {
        if (!file_exists($path)) {
            return;
        }

        $directory = new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS);
        $files     = new RecursiveIteratorIterator($directory, RecursiveIteratorIterator::CHILD_FIRST);

        foreach ($files as $file) {
            if ($file->isDir()) {
                rmdir($file->getPathname());
            } else {
                unlink($file->getPathname());
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

    /**
     * Get hash-like path
     * 
     * @param string $key
     * @return string
     */
    protected function hashPath(string $key): string
    {
        $hash = md5($key);

        return substr($hash, 0, 2) . '/' . substr($hash, 2);
    }

    /**
     * Get item path
     * 
     * @param string $key
     * @return string
     */
    protected function itemPath(string $key): string
    {
        return $this->path . '/item/' . $this->hashPath($key);
    }

    /**
     * Get tag path
     * 
     * @param string $tag
     * @return string
     */
    protected function tagPath(string $tag): string
    {
        return $this->path . '/tag/' . $this->hashPath($tag);
    }

    /**
     * Get tag file
     * 
     * @param string $tag
     * @param string $key
     * @return string
     */
    protected function tagFile(string $tag, string $key): string
    {
        return $this->tagPath($tag) . '/' . $this->hashPath($key);
    }
}
