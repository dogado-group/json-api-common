<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Model\Document;

use Dogado\JsonApi\Model\Resource\ResourceInterface;
use Dogado\JsonApi\Support\Collection\KeyValueCollection;
use Dogado\JsonApi\Support\Collection\KeyValueCollectionInterface;
use Dogado\JsonApi\Support\Error\ErrorCollection;
use Dogado\JsonApi\Support\Error\ErrorCollectionInterface;
use Dogado\JsonApi\Support\Resource\Link\LinkCollection;
use Dogado\JsonApi\Support\Resource\Link\LinkCollectionInterface;
use Dogado\JsonApi\Support\Resource\ResourceCollection;
use Dogado\JsonApi\Support\Resource\ResourceCollectionInterface;
use Dogado\JsonApi\Support\Resource\SingleResourceCollection;
use InvalidArgumentException;

class Document implements DocumentInterface
{
    private bool $handleAsCollection = true;
    private ResourceCollectionInterface $data;
    private LinkCollection $links;
    private ResourceCollection $included;
    private KeyValueCollection $metaInformation;
    private ErrorCollection $errors;

    /**
     * @param ResourceCollectionInterface|ResourceInterface|ResourceInterface[]|null $data
     * If data is not an array, "shouldBeHandledAsCollection" will return false
     *
     * @throws InvalidArgumentException
     */
    public function __construct(mixed $data = null)
    {
        if (null === $data || $data instanceof ResourceInterface) {
            $this->data = new SingleResourceCollection($data !== null ? [$data] : []);
            $this->handleAsCollection = false;
        } elseif ($data instanceof ResourceCollectionInterface) {
            $this->data = $data;
        } elseif (is_array($data)) {
            $this->data = new ResourceCollection($data);
        } else {
            throw new InvalidArgumentException('Invalid data given!');
        }

        $this->links = new LinkCollection();
        $this->included = new ResourceCollection();
        $this->metaInformation = new KeyValueCollection();
        $this->errors = new ErrorCollection();
    }

    public function shouldBeHandledAsCollection(): bool
    {
        return $this->handleAsCollection;
    }

    public function data(): ResourceCollectionInterface
    {
        return $this->data;
    }

    public function links(): LinkCollectionInterface
    {
        return $this->links;
    }

    public function included(): ResourceCollectionInterface
    {
        return $this->included;
    }

    public function metaInformation(): KeyValueCollectionInterface
    {
        return $this->metaInformation;
    }

    public function errors(): ErrorCollectionInterface
    {
        return $this->errors;
    }
}
