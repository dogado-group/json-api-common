<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Support\Model;

interface CustomAttributeSetterInterface
{
    public function __setAttribute(string $property, mixed $value): bool;
}
