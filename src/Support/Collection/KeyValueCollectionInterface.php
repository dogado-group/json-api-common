<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Support\Collection;

use InvalidArgumentException;

interface KeyValueCollectionInterface extends CollectionInterface
{
    public function has(string $key): bool;

    /**
     * @throws InvalidArgumentException If the requested key does not exist.
     */
    public function getRequired(string $key): mixed;

    public function get(string $key, mixed $defaultValue = null): mixed;

    public function getSubCollection(string $key, bool $required = true): KeyValueCollectionInterface;

    public function merge(array $data, bool $overwrite = true): KeyValueCollectionInterface;

    public function mergeCollection(
        KeyValueCollectionInterface $collection,
        bool $overwrite = true
    ): KeyValueCollectionInterface;

    public function set(string $key, mixed $value): KeyValueCollectionInterface;

    public function remove(string $key): KeyValueCollectionInterface;
}
