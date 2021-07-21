<?php

namespace Dogado\JsonApi\Tests\Converter\ResourceConverterTest;

use Dogado\JsonApi\Attribute\Attribute;
use Dogado\JsonApi\Support\Model\ValueObjectFactoryInterface;

class ValueObject implements ValueObjectFactoryInterface
{
    public static function create(): self
    {
        return new self();
    }

    #[Attribute('number')]
    private ?int $subItem = null;

    #[Attribute(ignoreOnNull: true)]
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
