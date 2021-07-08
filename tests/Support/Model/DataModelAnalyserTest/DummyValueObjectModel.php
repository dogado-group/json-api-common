<?php
namespace Dogado\JsonApi\Tests\Support\Model\DataModelAnalyserTest;

use Dogado\JsonApi\Attribute\Attribute;

class DummyValueObjectModel
{
    #[Attribute('name')]
    private ?string $name = 'sub-name';

    #[Attribute('sub-model2')]
    protected DummySubValueObjectModel $subModel;

    public function __construct()
    {
        $this->subModel = new DummySubValueObjectModel();
    }
}