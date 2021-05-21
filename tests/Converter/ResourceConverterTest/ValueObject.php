<?php

namespace Dogado\JsonApi\Tests\Converter\ResourceConverterTest;

use Dogado\JsonApi\Annotations\Attribute;

class ValueObject
{
    /**
     * @Attribute("number")
     */
    private ?int $subItem = null;

    /**
     * @Attribute(ignoreOnNull=true)
     */
    private ?string $ignoreOnNull = null;

    public function getSubItem(): ?int
    {
        return $this->subItem;
    }

    public function getIgnoreOnNull(): ?string
    {
        return $this->ignoreOnNull;
    }
}
