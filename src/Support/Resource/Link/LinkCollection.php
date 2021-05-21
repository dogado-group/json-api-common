<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Support\Resource\Link;

use Dogado\JsonApi\Model\Resource\Link\Link;
use Dogado\JsonApi\Model\Resource\Link\LinkInterface;
use Dogado\JsonApi\Support\Collection\AbstractCollection;
use Exception;
use InvalidArgumentException;

class LinkCollection extends AbstractCollection implements LinkCollectionInterface
{
    /**
     * @param LinkInterface[] $links
     */
    public function __construct(array $links = [])
    {
        parent::__construct();
        foreach ($links as $link) {
            $this->set($link);
        }
    }

    /**
     * @return LinkInterface[]
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
    public function get(string $name): LinkInterface
    {
        if (!$this->has($name)) {
            throw new InvalidArgumentException('Missing link ' . $name);
        }

        return $this->collection[$name];
    }

    public function set(LinkInterface $link): LinkCollectionInterface
    {
        $this->collection[$link->name()] = $link;

        return $this;
    }

    public function merge(LinkInterface $link, bool $replaceExistingValues = false): LinkCollectionInterface
    {
        try {
            $existing = $this->get($link->name());
        } catch (Exception $e) {
            $this->set($link);
            return $this;
        }

        if ($replaceExistingValues && $existing->href() !== $link->href()) {
            $link->metaInformation()->merge($existing->metaInformation()->all(), false);
            $this->set($link);
        } else {
            $existing->metaInformation()->merge($link->metaInformation()->all(), $replaceExistingValues);
        }

        return $this;
    }

    public function remove(string $name): LinkCollectionInterface
    {
        if ($this->has($name)) {
            unset($this->collection[$name]);
        }

        return $this;
    }

    public function removeElement(LinkInterface $link): LinkCollectionInterface
    {
        $this->remove($link->name());

        return $this;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function createLink(string $name, string $href): LinkCollectionInterface
    {
        $this->set(new Link($name, $href));

        return $this;
    }
}
