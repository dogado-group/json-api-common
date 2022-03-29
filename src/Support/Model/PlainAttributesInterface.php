<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Support\Model;

use Dogado\JsonApi\Support\Collection\KeyValueCollectionInterface;

interface PlainAttributesInterface
{
    public function getPlainAttributes(): KeyValueCollectionInterface;

    public function setPlainAttributes(KeyValueCollectionInterface $attributes): self;
}
