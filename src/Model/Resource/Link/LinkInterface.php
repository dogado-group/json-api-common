<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Model\Resource\Link;

use Dogado\JsonApi\Support\Collection\KeyValueCollectionInterface;

interface LinkInterface
{
    public function name(): string;

    public function href(): string;

    public function metaInformation(): KeyValueCollectionInterface;

    /**
     * Creates a new link containing all data from the current one.
     * If set, the new link will have the given name.
     */
    public function duplicate(?string $name = null): LinkInterface;
}
