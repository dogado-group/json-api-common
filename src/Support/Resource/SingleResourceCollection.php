<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Support\Resource;

use Dogado\JsonApi\Model\Resource\ResourceInterface;
use LogicException;

class SingleResourceCollection extends ResourceCollection
{
    /**
     * @throws LogicException
     */
    public function set(ResourceInterface $resource): ResourceCollectionInterface
    {
        if (!$this->isEmpty()) {
            throw new LogicException('Tried to add a second resource to single resource collection');
        }

        return parent::set($resource);
    }
}
