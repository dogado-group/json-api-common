<?php

namespace Dogado\JsonApi\Tests\Converter\ModelConverterTest\Php8Attributes;

use Dogado\JsonApi\Attribute\Attribute;

class ValueObject
{
    #[Attribute('number')]
    private ?int $test = 1213435664;

    #[Attribute(ignoreOnNull: true)]
    private ?string $ignoreOnNull = null;

    public function getTest(): ?int
    {
        return $this->test;
    }
}
