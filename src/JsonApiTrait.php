<?php

declare(strict_types=1);

namespace Dogado\JsonApi;

use Dogado\JsonApi\Model\Document\Document;
use Dogado\JsonApi\Model\Document\DocumentInterface;
use Dogado\JsonApi\Model\Resource\Relationship\Relationship;
use Dogado\JsonApi\Model\Resource\Relationship\RelationshipInterface;
use Dogado\JsonApi\Model\Resource\Resource;
use Dogado\JsonApi\Model\Resource\ResourceInterface;
use InvalidArgumentException;

trait JsonApiTrait
{
    /**
     * @throws InvalidArgumentException
     */
    protected function resource(string $type, ?string $id = null, array $attributes = []): ResourceInterface
    {
        return new Resource($type, $id, $attributes);
    }

    /**
     * @throws InvalidArgumentException
     */
    protected function singleResourceDocument(ResourceInterface $resource = null): DocumentInterface
    {
        return new Document($resource);
    }

    /**
     * @param ResourceInterface[] $resource
     * @return DocumentInterface
     * @throws InvalidArgumentException
     */
    protected function multiResourceDocument(array $resource = []): DocumentInterface
    {
        return new Document($resource);
    }

    /**
     * @throws InvalidArgumentException
     */
    protected function toOneRelationship(string $name, ResourceInterface $related = null): RelationshipInterface
    {
        return new Relationship($name, $related);
    }

    /**
     * @param string $name
     * @param ResourceInterface[] $related
     * @return RelationshipInterface
     * @throws InvalidArgumentException
     */
    protected function toManyRelationship(string $name, array $related = []): RelationshipInterface
    {
        return new Relationship($name, $related);
    }
}
