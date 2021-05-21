<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Model\Resource\Relationship;

use Dogado\JsonApi\Support\Collection\KeyValueCollectionInterface;
use Dogado\JsonApi\Support\Resource\Link\LinkCollectionInterface;
use Dogado\JsonApi\Support\Resource\ResourceCollectionInterface;

interface RelationshipInterface
{
    /**
     * Indicates if the contained data should be handled as object collection or single object
     */
    public function shouldBeHandledAsCollection(): bool;

    public function name(): string;

    public function related(): ResourceCollectionInterface;

    public function links(): LinkCollectionInterface;

    public function metaInformation(): KeyValueCollectionInterface;

    /**
     * Creates a new relationship containing all data from the current one.
     * If set, the new relationship will have the given name.
     */
    public function duplicate(?string $name = null): RelationshipInterface;
}
