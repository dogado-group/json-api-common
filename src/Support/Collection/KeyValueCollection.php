<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Support\Collection;

use InvalidArgumentException;

class KeyValueCollection extends AbstractCollection implements KeyValueCollectionInterface
{
    private array $keyMap = [];

    public function __construct(array $data = [])
    {
        parent::__construct();
        foreach ($data as $key => $value) {
            $this->set((string) $key, $value);
        }
    }

    public function has(string $key): bool
    {
        return array_key_exists(strtolower($key), $this->keyMap) &&
            array_key_exists($this->keyMap[strtolower($key)], $this->collection);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getRequired(string $key): mixed
    {
        if (!$this->has($key)) {
            throw new InvalidArgumentException('Element ' . $key . ' does not exist');
        }

        return $this->collection[$this->keyMap[strtolower($key)]];
    }

    public function get(string $key, mixed $defaultValue = null): mixed
    {
        return $this->has($key) ? $this->collection[$this->keyMap[strtolower($key)]] : $defaultValue;
    }

    /**
     * Returns a (sub) collection for an array value from the current collection.
     * If the same sub collection is requested multiple times, each time the same object must be returned
     *
     * @throws InvalidArgumentException
     */
    public function getSubCollection(string $key, bool $required = true): KeyValueCollectionInterface
    {
        $data = $required ? $this->getRequired($key) : $this->get($key, []);
        if (!is_array($data)) {
            throw new InvalidArgumentException('Element ' . $key . ' have to be an array to use it as collection.');
        }

        return new self($data);
    }

    public function merge(array $data, bool $overwrite = true): KeyValueCollectionInterface
    {
        foreach ($data as $key => $value) {
            $key = (string) $key;
            if ($overwrite || $this->get($key) === null) {
                $this->set($key, $value);
            }
        }

        return $this;
    }

    public function mergeCollection(
        KeyValueCollectionInterface $collection,
        bool $overwrite = true
    ): KeyValueCollectionInterface {
        $this->merge($collection->all(), $overwrite);

        return $this;
    }

    public function set(string $key, mixed $value): KeyValueCollectionInterface
    {
        $this->keyMap[strtolower($key)] = $key;
        if ($value instanceof KeyValueCollectionInterface) {
            $value = $value->all();
        }
        $this->collection[$key] = $value;

        return $this;
    }

    public function remove(string $key): KeyValueCollectionInterface
    {
        if ($this->has($key)) {
            unset($this->collection[$this->keyMap[strtolower($key)]], $this->keyMap[strtolower($key)]);
        }

        return $this;
    }

    public function pull(string $key, mixed $defaultValue = null): mixed
    {
        $value = $this->get($key, $defaultValue);
        $this->remove($key);
        return $value;
    }
}
