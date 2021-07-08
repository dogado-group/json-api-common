<?php

namespace Dogado\JsonApi\Tests\Converter\ResourceConverterTest;

use Dogado\JsonApi\Attribute\Id;

class DataModelWithoutTypeAnnotation
{
    #[Id]
    private ?int $id = null;
}
