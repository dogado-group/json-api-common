<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Support\Resource;

use Dogado\JsonApi\Model\Resource\ResourceInterface;
use LogicException;

class ImmutableResourceCollection extends ResourceCollection
{
    /**
     * @throws LogicException
     */
    public function set(ResourceInterface $resource): ResourceCollectionInterface
    {
        throw new LogicException('Tried to change an immutable collection');
    }

    /**
     * @throws LogicException
     */
    public function remove(string $type, string $id): ResourceCollectionInterface
    {
        throw new LogicException('Tried to change an immutable collection');
    }

    /**
     * @throws LogicException
     */
    public function removeElement(ResourceInterface $resource): ResourceCollectionInterface
    {
        throw new LogicException('Tried to change an immutable collection');
    }
}
