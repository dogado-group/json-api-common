<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Support\Resource;

use Dogado\JsonApi\Exception\JsonApi\ResourceNotFoundException;
use Dogado\JsonApi\Model\Resource\ResourceInterface;
use Dogado\JsonApi\Support\Collection\AbstractCollection;
use Exception;
use LogicException;

class ResourceCollection extends AbstractCollection implements ResourceCollectionInterface
{
    /**
     * @param ResourceInterface[] $resources
     */
    public function __construct(array $resources = [])
    {
        parent::__construct();
        foreach ($resources as $resource) {
            $this->set($resource);
        }
    }

    /**
     * @return ResourceInterface[]
     */
    public function all(): array
    {
        return array_values(parent::all());
    }

    public function has(string $type, string $id): bool
    {
        return array_key_exists($this->buildArrayKey($type, $id), $this->collection);
    }

    /**
     * @throws ResourceNotFoundException
     */
    public function get(string $type, string $id): ResourceInterface
    {
        if (!$this->has($type, $id)) {
            throw new ResourceNotFoundException($type, $id);
        }

        return $this->collection[$this->buildArrayKey($type, $id)];
    }

    /**
     * @throws LogicException
     */
    public function first(string $type = null): ResourceInterface
    {
        if ($this->isEmpty()) {
            throw new LogicException('Collection does not contain any resources!');
        }

        foreach ($this->all() as $resource) {
            if ($type === null || $resource->type() === $type) {
                return $resource;
            }
        }

        throw new LogicException('Collection does not contain any resources of type ' . $type . '!');
    }

    public function set(ResourceInterface $resource): ResourceCollectionInterface
    {
        $this->collection[
            $this->buildArrayKey($resource->type(), $resource->id() ?? spl_object_hash($resource))
        ] = $resource;

        return $this;
    }

    public function merge(ResourceInterface $resource, bool $replaceExistingValues = false): ResourceCollectionInterface
    {
        try {
            $existing = $this->get($resource->type(), $resource->id() ?? spl_object_hash($resource));
        } catch (Exception) {
            $this->set($resource);
            return $this;
        }

        $existing->metaInformation()->merge($resource->metaInformation()->all(), $replaceExistingValues);
        $existing->attributes()->merge($resource->attributes()->all(), $replaceExistingValues);

        foreach ($resource->links()->all() as $link) {
            $existing->links()->merge($link, $replaceExistingValues);
        }

        foreach ($resource->relationships()->all() as $relationship) {
            $existing->relationships()->merge($relationship, $replaceExistingValues);
        }

        return $this;
    }

    public function remove(string $type, string $id): ResourceCollectionInterface
    {
        if ($this->has($type, $id)) {
            unset($this->collection[$this->buildArrayKey($type, $id)]);
        }

        return $this;
    }

    public function removeElement(ResourceInterface $resource): ResourceCollectionInterface
    {
        $this->remove($resource->type(), $resource->id() ?? spl_object_hash($resource));

        return $this;
    }

    private function buildArrayKey(string $type, string $id): string
    {
        return $type . '::' . $id;
    }
}
