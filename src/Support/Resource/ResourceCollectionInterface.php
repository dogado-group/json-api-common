<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Support\Resource;

use Dogado\JsonApi\Model\Resource\ResourceInterface;
use Dogado\JsonApi\Support\Collection\CollectionInterface;

interface ResourceCollectionInterface extends CollectionInterface
{
    /** @return ResourceInterface[] */
    public function all(): array;

    public function has(string $type, string $id): bool;

    public function get(string $type, string $id): ResourceInterface;

    public function first(string $type = null): ResourceInterface;

    public function set(ResourceInterface $resource): ResourceCollectionInterface;

    public function merge(
        ResourceInterface $resource,
        bool $replaceExistingValues = false
    ): ResourceCollectionInterface;

    public function remove(string $type, string $id): ResourceCollectionInterface;

    public function removeElement(ResourceInterface $resource): ResourceCollectionInterface;
}
