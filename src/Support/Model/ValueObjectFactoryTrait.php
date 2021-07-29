<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Support\Model;

trait ValueObjectFactoryTrait
{
    public static function create(): self
    {
        return new self();
    }
}
