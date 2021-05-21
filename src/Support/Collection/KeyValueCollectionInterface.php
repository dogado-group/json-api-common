<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Support\Collection;

use InvalidArgumentException;

interface KeyValueCollectionInterface extends CollectionInterface
{
    public function has(string $key): bool;

    /**
     * @return mixed
     * @throws InvalidArgumentException If the requested key does not exist.
     */
    public function getRequired(string $key);

    /**
     * @param mixed $defaultValue
     * @return mixed
     */
    public function get(string $key, $defaultValue = null);

    public function getSubCollection(string $key, bool $required = true): KeyValueCollectionInterface;

    public function merge(array $data, bool $overwrite = true): KeyValueCollectionInterface;

    public function mergeCollection(
        KeyValueCollectionInterface $collection,
        bool $overwrite = true
    ): KeyValueCollectionInterface;

    /**
     * @param mixed $value
     */
    public function set(string $key, $value): KeyValueCollectionInterface;

    public function remove(string $key): KeyValueCollectionInterface;
}
