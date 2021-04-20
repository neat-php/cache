<?php

namespace Neat\Cache;

use DateInterval;

class Memory extends Abstraction
{
    /** @var array */
    protected $data;

    /** @var int[] */
    protected $expiration;

    /**
     * Constructor
     *
     * @param array                 $data (optional)
     * @param DateInterval|int|null $ttl  (optional)
     */
    public function __construct(array $data = [], $ttl = null)
    {
        $this->data = $data;
        $this->ttl  = $ttl;

        $this->expiration = array_fill_keys(array_keys($data), $this->expiration());
    }

    /**
     * Has value?
     *
     * @param string $key
     * @return bool
     * @throws InvalidArgumentException
     */
    public function has($key): bool
    {
        $this->validate($key);

        if (!isset($this->data[$key])) {
            return false;
        }

        $expiration = $this->expiration[$key] ?? null;
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
        if ($this->has($key)) {
            return $this->data[$key];
        }

        return $default;
    }

    /**
     * Set value
     *
     * @param string                $key
     * @param mixed                 $value
     * @param DateInterval|int|null $ttl   (optional)
     * @return bool
     * @throws InvalidArgumentException
     */
    public function set($key, $value, $ttl = null): bool
    {
        $this->validate($key);

        $this->data[$key] = $value;
        $this->expiration[$key] = $this->expiration($ttl);

        return true;
    }

    /**
     * Delete value
     *
     * @param string $key
     * @return bool
     * @throws InvalidArgumentException
     */
    public function delete($key): bool
    {
        $this->validate($key);

        unset($this->data[$key]);
        unset($this->expiration[$key]);

        return true;
    }

    /**
     * Clear values
     *
     * @return bool
     */
    public function clear(): bool
    {
        $this->data = [];
        $this->expiration = [];

        return true;
    }
}
