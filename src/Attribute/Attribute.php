<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Attribute;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Attribute
{
    /**
     * @param string|null $name The path map within the resource attributes separated by "/".
     * @param bool $ignoreOnNull Ignore the attribute for the model conversion if it's value is `null`.
     */
    public function __construct(
        public ?string $name = null,
        public bool $ignoreOnNull = false
    ) {
    }
}
