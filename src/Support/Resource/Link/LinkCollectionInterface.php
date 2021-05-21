<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Support\Resource\Link;

use Dogado\JsonApi\Model\Resource\Link\LinkInterface;
use Dogado\JsonApi\Support\Collection\CollectionInterface;

interface LinkCollectionInterface extends CollectionInterface
{
    /** @return LinkInterface[] */
    public function all(): array;

    public function has(string $name): bool;

    public function get(string $name): LinkInterface;

    public function set(LinkInterface $link): LinkCollectionInterface;

    public function merge(LinkInterface $link, bool $replaceExistingValues = false): LinkCollectionInterface;

    public function remove(string $name): LinkCollectionInterface;

    public function removeElement(LinkInterface $link): LinkCollectionInterface;

    public function createLink(string $name, string $href): LinkCollectionInterface;
}
