<?php

namespace Dogado\JsonApi\Tests\Support\Model;

use DateTimeInterface;
use Dogado\JsonApi\Support\Model\DataModelAnalyser;
use Dogado\JsonApi\Tests\Support\Model\DataModelAnalyserTest\DummyModel;
use Dogado\JsonApi\Tests\Support\Model\DataModelAnalyserTest\ModelWithNoId;
use Dogado\JsonApi\Tests\TestCase;
use ReflectionException;

class DataModelAnalyserTest extends TestCase
{
    /**
     * @throws ReflectionException
     */
    public function testProcessWithObject(): void
    {
        $date = $this->faker()->dateTime();
        $testModel = new DummyModel($date);
        $converter = DataModelAnalyser::process($testModel);
        $this->assertEquals('dummy-model', $converter->getType());
        $this->assertEquals('12345', $converter->getIdValue());
        $this->assertEquals('modelId', $converter->getIdPropertyName());
        $this->assertEquals([
            'name' => 'newName',
            'sub-object/test/property' => 'lorem',
            'sub-object/test/second-property' => 'ipsum',
            'sub-object/createdAt' => $date->format(DateTimeInterface::ATOM),
            'sub-object/updatedAt' => null,
            'sub-model/name' => 'sub-name',
            'sub-model/sub-model2/name' => 'sub-sub-name',
            'filled-model-without-attributes' => [],
            'sub-model-null' => null,
        ], $converter->getAttributeValues());
        $this->assertEquals([
            'name' => 'newName',
            'sub-object/test/property' => 'propertyWithinObject',
            'sub-object/test/second-property' => 'secondPropertyWithinObject',
            'sub-object/createdAt' => 'createdAt',
            'sub-object/updatedAt' => 'updatedAt',
            'sub-model' => 'aggregationModel',
            'sub-model/name' => 'aggregationModel/name',
            'sub-model/sub-model2' => 'aggregationModel/subModel',
            'sub-model/sub-model2/name' => 'aggregationModel/subModel/name',
            'sub-model-null' => 'aggregationModelNull',
            'sub-model-null/name' => 'aggregationModelNull/name',
            'sub-model-null/sub-model2' => 'aggregationModelNull/subModel',
            'sub-model-null/sub-model2/name' => 'aggregationModelNull/subModel/name',
            'ignoreOnNull' => 'ignoreOnNull',
            'filled-model-without-attributes' => 'filledModelWithoutAttributes',
        ], $converter->getAttributesPropertyMap());
    }

    /**
     * @throws ReflectionException
     */
    public function testProcessWithClassName(): void
    {
        $converter = DataModelAnalyser::process(DummyModel::class);
        $this->assertEquals('dummy-model', $converter->getType());
        $this->assertEquals(null, $converter->getIdValue());
        $this->assertEquals('modelId', $converter->getIdPropertyName());
        $this->assertEquals([
            'name' => null,
            'sub-object/test/property' => null,
            'sub-object/test/second-property' => null,
            'sub-object/createdAt' => null,
            'sub-object/updatedAt' => null,
            'sub-model' => null,
            'sub-model-null' => null,
            'filled-model-without-attributes' => null,
        ], $converter->getAttributeValues());
        $this->assertEquals([
            'name' => 'newName',
            'sub-object/test/property' => 'propertyWithinObject',
            'sub-object/test/second-property' => 'secondPropertyWithinObject',
            'sub-object/createdAt' => 'createdAt',
            'sub-object/updatedAt' => 'updatedAt',
            'sub-model' => 'aggregationModel',
            'sub-model/name' => 'aggregationModel/name',
            'sub-model/sub-model2' => 'aggregationModel/subModel',
            'sub-model/sub-model2/name' => 'aggregationModel/subModel/name',
            'sub-model-null' => 'aggregationModelNull',
            'sub-model-null/name' => 'aggregationModelNull/name',
            'sub-model-null/sub-model2' => 'aggregationModelNull/subModel',
            'sub-model-null/sub-model2/name' => 'aggregationModelNull/subModel/name',
            'ignoreOnNull' => 'ignoreOnNull',
            'filled-model-without-attributes' => 'filledModelWithoutAttributes',
        ], $converter->getAttributesPropertyMap());
    }

    public function testModelWithNoId(): void
    {
        $model = new ModelWithNoId();
        $converter = DataModelAnalyser::process($model);
        $this->assertNull($converter->getIdValue());
    }
}
