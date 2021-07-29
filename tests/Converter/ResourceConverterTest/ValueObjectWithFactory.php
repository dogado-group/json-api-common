<?php

namespace Dogado\JsonApi\Tests\Converter\ResourceConverterTest;

use Dogado\JsonApi\Attribute\Attribute;
use Dogado\JsonApi\Support\Model\ValueObjectFactoryInterface;
use Dogado\JsonApi\Support\Model\ValueObjectFactoryTrait;

class ValueObjectWithFactory implements ValueObjectFactoryInterface
{
    use ValueObjectFactoryTrait;

    #[Attribute]
    private ?int $item = null;

    public function getItem(): ?int
    {
        return $this->item;
    }
}
