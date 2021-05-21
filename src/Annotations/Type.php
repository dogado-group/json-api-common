<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Annotations;

use Doctrine\Common\Annotations\Annotation\Required;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
class Type
{
    /**
     * @Required
     */
    public ?string $value = null;
}
