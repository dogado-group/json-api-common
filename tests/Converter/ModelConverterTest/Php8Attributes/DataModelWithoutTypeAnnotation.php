<?php

namespace Dogado\JsonApi\Tests\Converter\ModelConverterTest\Php8Attributes;

use Dogado\JsonApi\Attribute\Id;

class DataModelWithoutTypeAnnotation
{
    #[Id]
    private ?int $id = null;
}
