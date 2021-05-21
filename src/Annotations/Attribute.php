<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Annotations;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
class Attribute
{
    /**
     * The path map within the resource attributes separated by "/".
     */
    public ?string $value = null;

    /**
     * Ignore the attribute for the model conversion if it's value is `null`.
     */
    public bool $ignoreOnNull = false;
}
