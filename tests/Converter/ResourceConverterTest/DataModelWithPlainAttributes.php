<?php

namespace Dogado\JsonApi\Tests\Converter\ResourceConverterTest;

use Dogado\JsonApi\Attribute\Type;
use Dogado\JsonApi\Support\Model\PlainAttributesInterface;
use Dogado\JsonApi\Support\Model\PlainAttributesTrait;

#[Type('dummy-deserializer-model-with-plain-attributes')]
class DataModelWithPlainAttributes implements PlainAttributesInterface
{
    use PlainAttributesTrait;
}
