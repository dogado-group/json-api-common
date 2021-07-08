<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Support\Model;

interface CustomAttributeGetterInterface
{
    /**
     * @return mixed Shall return null if the given property is not supported
     */
    public function __getAttribute(string $property): mixed;
}
