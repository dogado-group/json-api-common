<?php

namespace Dogado\JsonApi\Tests\Converter;

use DateTime;
use Dogado\JsonApi\Converter\ModelConverter;
use Dogado\JsonApi\Exception\DataModelSerializerException;
use Dogado\JsonApi\Model\Resource\Resource;
use Dogado\JsonApi\Tests\Converter\ModelConverterTest\DataModel;
use Dogado\JsonApi\Tests\Converter\ModelConverterTest\DataModelWithoutTypeAnnotation;
use Dogado\JsonApi\Tests\TestCase;
use ReflectionException;

class ModelConverterTest extends TestCase
{
    /**
     * @throws DataModelSerializerException
     * @throws ReflectionException
     */
    public function testModelWithoutTypeAnnotation(): void
    {
        $model = new DataModelWithoutTypeAnnotation();
        $this->expectExceptionObject(
            DataModelSerializerException::typeAnnotationMissing(get_class($model))
        );
        (new ModelConverter())->toResource($model);
    }

    /**
     * @throws DataModelSerializerException
     * @throws ReflectionException
     */
    public function testModelToResource(): void
    {
        $date = $this->faker()->dateTime;
        $model = new DataModel($date);
        $expected = new Resource('dummy-serializer-model', (string) $model->getId(), [
            'name' => $model->getName(),
            'values' => [
                'number' => $model->getValueObject()->getTest(),
            ],
            'empty-values' => [
                'number' => null,
            ],
            'createdAt' => $date->format(DateTime::ATOM),
            'updatedAt' => null,
        ]);

        $this->assertEquals($expected, (new ModelConverter())->toResource($model));
    }
}
