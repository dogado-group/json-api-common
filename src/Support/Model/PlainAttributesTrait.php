<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Support\Model;

use Dogado\JsonApi\Support\Collection\KeyValueCollection;
use Dogado\JsonApi\Support\Collection\KeyValueCollectionInterface;

trait PlainAttributesTrait
{
    private ?KeyValueCollectionInterface $plainAttributes = null;

    public function setPlainAttributes(KeyValueCollectionInterface $attributes): self
    {
        $this->plainAttributes = $attributes;
        return $this;
    }

    public function getPlainAttributes(): KeyValueCollectionInterface
    {
        return $this->plainAttributes ?? new KeyValueCollection();
    }
}
