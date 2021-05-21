<?php

namespace Dogado\JsonApi\Tests\Converter\ResourceConverterTest;

use Dogado\JsonApi\Annotations\Id;

class DataModelWithoutTypeAnnotation
{
    /**
     * @Id()
     */
    private ?int $id = null;
}
