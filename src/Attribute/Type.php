<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Type
{
    public function __construct(
        public string $name
    ) {
    }
}
