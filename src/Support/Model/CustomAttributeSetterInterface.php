<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Support\Model;

interface CustomAttributeSetterInterface
{
    /**
     * @param mixed $value
     */
    public function __setAttribute(string $property, $value): bool;
}
