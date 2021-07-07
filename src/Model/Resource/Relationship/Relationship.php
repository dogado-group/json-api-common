<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Model\Resource\Relationship;

use Dogado\JsonApi\Model\Resource\ResourceInterface;
use Dogado\JsonApi\Support\Collection\KeyValueCollection;
use Dogado\JsonApi\Support\Collection\KeyValueCollectionInterface;
use Dogado\JsonApi\Support\Resource\Link\LinkCollection;
use Dogado\JsonApi\Support\Resource\Link\LinkCollectionInterface;
use Dogado\JsonApi\Support\Resource\ResourceCollection;
use Dogado\JsonApi\Support\Resource\ResourceCollectionInterface;
use Dogado\JsonApi\Support\Resource\SingleResourceCollection;
use InvalidArgumentException;

class Relationship implements RelationshipInterface
{
    private string $name;
    private ResourceCollectionInterface $related;
    private bool $handleAsCollection = true;
    private LinkCollection $links;
    private KeyValueCollection $metaInformation;

    /**
     * @param string $name
     * @param ResourceInterface|ResourceInterface[]|ResourceCollectionInterface|null $related
     *
     * @throws InvalidArgumentException
     */
    public function __construct(string $name, mixed $related = null)
    {
        if ('' === $name) {
            throw new InvalidArgumentException('Invalid relationship');
        }
        $this->name = $name;

        if (null === $related || $related instanceof ResourceInterface) {
            $this->related = new SingleResourceCollection($related !== null ? [$related] : []);
            $this->handleAsCollection = false;
        } elseif ($related instanceof ResourceCollectionInterface) {
            $this->related = $related;
        } elseif (is_array($related)) {
            $this->related = new ResourceCollection($related);
        } else {
            throw new InvalidArgumentException('Invalid relationship!');
        }

        $this->links = new LinkCollection();
        $this->metaInformation = new KeyValueCollection();
    }

    public function name(): string
    {
        return $this->name;
    }

    public function shouldBeHandledAsCollection(): bool
    {
        return $this->handleAsCollection;
    }

    public function links(): LinkCollectionInterface
    {
        return $this->links;
    }

    public function metaInformation(): KeyValueCollectionInterface
    {
        return $this->metaInformation;
    }

    public function related(): ResourceCollectionInterface
    {
        return $this->related;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function duplicate(string $name = null): RelationshipInterface
    {
        if ($this->shouldBeHandledAsCollection()) {
            $related = [];
            foreach ($this->related()->all() as $resource) {
                $related[] = $resource->duplicate();
            }
        } else {
            $related = !$this->related()->isEmpty() ? $this->related()->first()->duplicate() : null;
        }

        $relationship = new self($name ?? $this->name(), $related);

        $relationship->metaInformation()->mergeCollection($this->metaInformation());
        foreach ($this->links()->all() as $link) {
            $relationship->links()->set($link->duplicate());
        }

        return $relationship;
    }
}
