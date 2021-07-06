<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Annotations;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 * @deprecated Use the php 8 attribute Dogado\JsonApi\Attribute\Id instead.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Id
{

}
