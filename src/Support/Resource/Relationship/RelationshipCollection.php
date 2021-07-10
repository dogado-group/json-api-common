<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Support\Resource\Relationship;

use Dogado\JsonApi\Model\Resource\Relationship\RelationshipInterface;
use Dogado\JsonApi\Support\Collection\AbstractCollection;
use Exception;
use InvalidArgumentException;

class RelationshipCollection extends AbstractCollection implements RelationshipCollectionInterface
{
    /**
     * @param RelationshipInterface[] $data
     */
    public function __construct(array $data = [])
    {
        parent::__construct();
        foreach ($data as $relationship) {
            $this->set($relationship);
        }
    }

    /**
     * @return RelationshipInterface[]
     */
    public function all(): array
    {
        return array_values(parent::all());
    }

    public function has(string $name): bool
    {
        return array_key_exists($name, $this->collection);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function get(string $name): RelationshipInterface
    {
        if (!$this->has($name)) {
            throw new InvalidArgumentException('Relationship ' . $name . ' not available');
        }

        return $this->collection[$name];
    }

    public function set(RelationshipInterface $relationship): RelationshipCollectionInterface
    {
        $this->collection[$relationship->name()] = $relationship;

        return $this;
    }

    public function merge(
        RelationshipInterface $relationship,
        bool $replaceExistingValues = false
    ): RelationshipCollectionInterface {
        try {
            $existing = $this->get($relationship->name());
        } catch (Exception) {
            $this->set($relationship);
            return $this;
        }

        $existing->metaInformation()->merge($relationship->metaInformation()->all(), $replaceExistingValues);

        foreach ($relationship->links()->all() as $link) {
            $existing->links()->merge($link, $replaceExistingValues);
        }

        foreach ($relationship->related()->all() as $related) {
            $existing->related()->merge($related, $replaceExistingValues);
        }

        return $this;
    }

    public function remove(string $name): RelationshipCollectionInterface
    {
        if ($this->has($name)) {
            unset($this->collection[$name]);
        }

        return $this;
    }

    public function removeElement(RelationshipInterface $relationship): RelationshipCollectionInterface
    {
        $this->remove($relationship->name());

        return $this;
    }
}
