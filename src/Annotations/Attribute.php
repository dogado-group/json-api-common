<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Annotations;

use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 * @NamedArgumentConstructor()
 * @deprecated Use the php 8 attribute Dogado\JsonApi\Attribute\Attribute instead.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Attribute
{
    /**
     * @param string|null $value The path map within the resource attributes separated by "/".
     * @param bool $ignoreOnNull Ignore the attribute for the model conversion if it's value is `null`.
     */
    public function __construct(
        public ?string $value = null,
        public bool $ignoreOnNull = false
    ) {
    }
}
