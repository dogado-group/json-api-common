<?php

namespace Dogado\JsonApi\Tests\Converter\ResourceConverterTest;

use Dogado\JsonApi\Attribute\Attribute;
use Dogado\JsonApi\Support\Model\ValueObjectFactoryInterface;
use Dogado\JsonApi\Support\Model\ValueObjectFactoryTrait;

class ValueObjectWithFactoryWrapper implements ValueObjectFactoryInterface
{
    use ValueObjectFactoryTrait;

    #[Attribute]
    private ?ValueObjectWithFactory $nullableValueObject = null;

    public function getNullableValueObject(): ?ValueObjectWithFactory
    {
        return $this->nullableValueObject;
    }
}
