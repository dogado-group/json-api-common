<?php
namespace Dogado\JsonApi\Tests\Support\Model\DataModelAnalyserTest;

use Dogado\JsonApi\Annotations\Attribute;

class DummySubValueObjectModel
{
    /**
     * @Attribute("name")
     */
    private ?string $name = 'sub-sub-name';
}