<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Tests\Support\Model\DataModelAnalyserTest;

use Dogado\JsonApi\Attribute\Id;
use Dogado\JsonApi\Attribute\Type;

#[Type('modelWithNoId')]
class ModelWithNoId
{
    #[Id]
    private ?string $id = null;
}
