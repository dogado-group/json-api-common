<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Model\Resource\Link;

use Dogado\JsonApi\Support\Collection\KeyValueCollection;
use Dogado\JsonApi\Support\Collection\KeyValueCollectionInterface;
use InvalidArgumentException;

class Link implements LinkInterface
{
    private string $name;
    private string $href;
    private KeyValueCollection $metaInformation;

    /**
     * @throws InvalidArgumentException
     */
    public function __construct(string $name, string $href)
    {
        if ('' === $name) {
            throw new InvalidArgumentException('Invalid link name');
        }

        if ('' === $href) {
            throw new InvalidArgumentException('Invalid link');
        }

        $validateUrl = $href;
        if ('/' === $validateUrl[0]) {
            $validateUrl = 'http://www.example.com' . $href;
        }
        if (filter_var($validateUrl, FILTER_VALIDATE_URL) === false) {
            throw new InvalidArgumentException('Invalid link target');
        }

        $this->name = $name;
        $this->href = $href;
        $this->metaInformation = new KeyValueCollection();
    }

    public function name(): string
    {
        return $this->name;
    }

    public function href(): string
    {
        return $this->href;
    }

    public function metaInformation(): KeyValueCollectionInterface
    {
        return $this->metaInformation;
    }

    /**
     * Creates a new link containing all data from the current one.
     * If set, the new link will have the given name.
     *
     * @throws InvalidArgumentException
     */
    public function duplicate(string $name = null): LinkInterface
    {
        $link = new self($name ?? $this->name(), $this->href());
        $link->metaInformation()->mergeCollection($this->metaInformation());

        return $link;
    }
}
