<?php
namespace Dogado\JsonApi\Tests\Support\Model\DataModelAnalyserTest;

use Dogado\JsonApi\Attribute\Attribute;

class DummySubValueObjectModel
{
    #[Attribute('name')]
    private ?string $name = 'sub-sub-name';
}
