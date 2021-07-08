<?php

namespace Dogado\JsonApi\Tests\Converter\ModelConverterTest;

use Dogado\JsonApi\Attribute\Id;

class DataModelWithoutTypeAnnotation
{
    #[Id]
    private ?int $id = null;
}
