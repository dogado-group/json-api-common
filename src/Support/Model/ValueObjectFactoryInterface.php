<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Support\Model;

interface ValueObjectFactoryInterface
{
    public static function create(): self;
}
