<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Annotations;

use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;

/**
 * @Annotation
 * @Target({"CLASS"})
 * @NamedArgumentConstructor()
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class Type
{
    public function __construct(
        public string $value
    ) {
    }
}
