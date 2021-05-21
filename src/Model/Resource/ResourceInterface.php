<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Model\Resource;

use Dogado\JsonApi\Support\Collection\KeyValueCollectionInterface;
use Dogado\JsonApi\Support\Resource\Link\LinkCollectionInterface;
use Dogado\JsonApi\Support\Resource\Relationship\RelationshipCollectionInterface;

interface ResourceInterface
{
    public function type(): string;

    public function id(): ?string;

    public function attributes(): KeyValueCollectionInterface;

    public function relationships(): RelationshipCollectionInterface;

    public function links(): LinkCollectionInterface;

    public function metaInformation(): KeyValueCollectionInterface;

    /**
     * Creates a new resource containing all data from the current one.
     * If set, the new resource will have the given id.
     */
    public function duplicate(?string $id = null): ResourceInterface;
}
