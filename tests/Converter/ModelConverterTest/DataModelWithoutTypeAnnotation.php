<?php

namespace Dogado\JsonApi\Tests\Converter\ModelConverterTest;

use Dogado\JsonApi\Annotations\Id;

class DataModelWithoutTypeAnnotation
{
    /**
     * @Id()
     */
    private ?int $id = null;
}
