<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Tests\Support\Model\DataModelAnalyserTest;

use Dogado\JsonApi\Annotations\Id;
use Dogado\JsonApi\Annotations\Type;

/**
 * @Type("modelWithNoId")
 */
class ModelWithNoId
{
    /**
     * @Id()
     */
    private ?string $id = null;
}
