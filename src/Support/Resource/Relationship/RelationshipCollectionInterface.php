<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Support\Resource\Relationship;

use Dogado\JsonApi\Model\Resource\Relationship\RelationshipInterface;
use Dogado\JsonApi\Support\Collection\CollectionInterface;

interface RelationshipCollectionInterface extends CollectionInterface
{
    /** @return RelationshipInterface[] */
    public function all(): array;

    public function has(string $name): bool;

    public function get(string $name): RelationshipInterface;

    public function set(RelationshipInterface $relationship): RelationshipCollectionInterface;

    public function merge(
        RelationshipInterface $relationship,
        bool $replaceExistingValues = false
    ): RelationshipCollectionInterface;

    public function remove(string $name): RelationshipCollectionInterface;

    public function removeElement(RelationshipInterface $relationship): RelationshipCollectionInterface;
}
