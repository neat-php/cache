<?php

namespace Neat\Cache;

use DateInterval;
use DateTime;
use Psr\SimpleCache\CacheInterface;
use Traversable;

abstract class Abstraction implements CacheInterface
{
    /** @var DateInterval|int|null */
    protected $ttl;

    /** @noinspection PhpDocMissingThrowsInspection */
    /**
     * Get expiration time
     *
     * @param DateInterval|int|null $ttl
     * @return int|null
     */
    protected function expiration($ttl = null)
    {
        $ttl = $ttl ?? $this->ttl;
        if (is_null($ttl)) {
            return null;
        } elseif (is_int($ttl)) {
            return time() + $ttl;
        } elseif ($ttl instanceof DateInterval) {
            return (new DateTime('now'))->add($ttl)->getTimestamp();
        }

        throw new InvalidArgumentException('TTL must be a DateInterval, integer or null');
    }

    /**
     * Has value?
     *
     * @param string $key
     * @return bool
     * @throws InvalidArgumentException
     */
    abstract public function has($key);

    /**
     * Get value
     *
     * @param string $key
     * @param mixed  $default (optional)
     * @return mixed
     * @throws InvalidArgumentException
     */
    abstract public function get($key, $default = null);

    /**
     * Set value
     *
     * @param string                $key
     * @param mixed                 $value
     * @param DateInterval|int|null $ttl (optional)
     * @return bool
     * @throws InvalidArgumentException
     */
    abstract public function set($key, $value, $ttl = null);

    /**
     * Delete value
     *
     * @param string $key
     * @return bool
     * @throws InvalidArgumentException
     */
    abstract public function delete($key);

    /**
     * Clear values
     *
     * @return bool
     */
    abstract public function clear();

    /**
     * Get multiple values
     *
     * @param iterable $keys
     * @param mixed    $default (optional)
     * @return iterable
     * @throws InvalidArgumentException
     */
    public function getMultiple($keys, $default = null)
    {
        $this->validateMultiple($keys);

        foreach ($keys as $key) {
            yield $key => $this->get($key, $default);
        }
    }

    /**
     * Set multiple values
     *
     * @param iterable              $values
     * @param null|int|DateInterval $ttl (optional)
     * @return bool
     * @throws InvalidArgumentException
     */
    public function setMultiple($values, $ttl = null)
    {
        $this->validateMultiple($values);

        $result = true;
        foreach ($values as $key => $value) {
            $result = $this->set($key, $value, $ttl) && $result;
        }

        return $result;
    }

    /**
     * Deletes multiple values
     *
     * @param iterable $keys
     * @return bool
     * @throws InvalidArgumentException
     */
    public function deleteMultiple($keys)
    {
        $this->validateMultiple($keys);

        $result = true;
        foreach ($keys as $key) {
            $result = $this->delete($key) && $result;
        }

        return $result;
    }

    /**
     * Validate that given keys can be iterated over
     *
     * @param iterable $keys
     * @throws InvalidArgumentException
     */
    protected function validateMultiple($keys)
    {
        if (!is_array($keys) && !$keys instanceof Traversable) {
            throw new InvalidArgumentException('Keys must be iterable');
        }
    }

    /**
     * Validate given key
     *
     * @param string $key
     * @throws InvalidArgumentException
     */
    protected function validate($key)
    {
        if (!is_string($key) || $key === '') {
            throw new InvalidArgumentException('Key must be a non empty string');
        }

        if (preg_match('#[{}()/\\\@:]#', $key)) {
            throw new InvalidArgumentException('Key contains invalid characters');
        }
    }
}
