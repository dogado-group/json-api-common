<?php

namespace Dogado\JsonApi\Tests\Converter;

use DateTimeInterface;
use Dogado\JsonApi\Converter\ResourceConverter;
use Dogado\JsonApi\Exception\DataModelSerializerException;
use Dogado\JsonApi\Model\Resource\Resource;
use Dogado\JsonApi\Support\Collection\KeyValueCollectionInterface;
use Dogado\JsonApi\Support\Model\PlainAttributesInterface;
use Dogado\JsonApi\Tests\Converter\ResourceConverterTest\DataModel;
use Dogado\JsonApi\Tests\Converter\ResourceConverterTest\DataModelWithoutTypeAnnotation;
use Dogado\JsonApi\Tests\Converter\ResourceConverterTest\DataModelWithPlainAttributes;
use Dogado\JsonApi\Tests\Converter\ResourceConverterTest\ValueObjectWithFactory;
use Dogado\JsonApi\Tests\Converter\ResourceConverterTest\ValueObjectWithFactoryWrapper;
use Dogado\JsonApi\Tests\TestCase;
use ReflectionException;
use stdClass;

class ResourceConverterTest extends TestCase
{
    /**
     * @throws DataModelSerializerException
     * @throws ReflectionException
     */
    public function testResourceToModel(): void
    {
        $type = 'dummy-deserializer-model';
        $faker = $this->faker();
        $date = $this->faker()->dateTime();
        $resource = new Resource(
            $type,
            (string) $this->faker()->numberBetween(),
            [
                'stringValue' => $this->faker()->userName(),
                'mixedValue' => $this->faker()->randomElement([
                    $faker->slug(),
                    $faker->boolean(),
                    $faker->numberBetween()
                ]),
                'doesNotExistInModel' => $this->faker()->userName(),
                'noTypeDeclaration' => $this->faker()->userName(),
                'notNullable' => $this->faker()->userName(),
                'notCastable' => [$this->faker()->userName()],
                'values' => [
                    'number' => (string) $this->faker()->numberBetween(),
                    'ignoreOnNull' => $this->faker()->text(),
                ],
                'nullableValueObject' => [
                    'item' => $this->faker()->numberBetween(),
                ],
                'arrayItems' => [
                    $this->faker->slug() => $this->faker->text(),
                    $this->faker->slug() => $this->faker->text(),
                    $this->faker->slug() => $this->faker->text(),
                ],
                'castBool' => (int) $this->faker()->boolean(),
                'castInt' => (string) $this->faker()->numberBetween(),
                'castFloat' => (string) $this->faker()->randomFloat(),
                'castString' => $this->faker()->randomNumber(),
                'named' => [
                    'sub' => [
                        'item' => $this->faker()->text(),
                        'item2' => $this->faker()->text(),
                    ],
                ],
                'willBeCastedToArray' => $this->faker()->userName(),
                'ignoreOnNull' => $this->faker()->text(),
                'createdAt' => $date->format(DateTimeInterface::ATOM),
                'updatedAt' => null,
            ],
        );

        $model = new DataModel();
        $attributes = $resource->attributes();
        $this->assertInstanceOf(get_class($model), (new ResourceConverter())->toModel($resource, $model));

        $this->assertEquals($resource->id(), $model->getId());
        $this->assertNull($model->getNullAttribute());
        $this->assertEquals($attributes->getRequired('stringValue'), $model->getStringValue());
        $this->assertEquals($attributes->getRequired('mixedValue'), $model->getMixedValue());
        $this->assertEquals($attributes->getRequired('noTypeDeclaration'), $model->getNoTypeDeclaration());
        $this->assertEquals($attributes->getRequired('notNullable'), $model->getNotNullable());
        $this->assertNull($model->getDoesNotExistInResource());
        $this->assertEquals(
            (int) $attributes->getSubCollection('values')->getRequired('number'),
            $model->getValueObject()->getSubItem()
        );
        $this->assertEquals(
            $attributes->getSubCollection('values')->getRequired('ignoreOnNull'),
            $model->getValueObject()->getIgnoreOnNull()
        );
        $this->assertNotNull($model->getNullableValueObject());
        $this->assertEquals(
            (int) $attributes->getSubCollection('nullableValueObject')->getRequired('item'),
            $model->getNullableValueObject()->getItem()
        );
        $this->assertEquals(
            $attributes->get('arrayItems'),
            $model->getArrayItems()
        );
        $this->assertEquals((bool) $attributes->getRequired('castBool'), $model->getCastBool());
        $this->assertEquals((int) $attributes->getRequired('castInt'), $model->getCastInt());
        $this->assertEquals((float) $attributes->getRequired('castFloat'), $model->getCastFloat());
        $this->assertEquals((string) $attributes->getRequired('castString'), $model->getCastString());
        $this->assertEquals(
            (string) $attributes
                ->getSubCollection('named', true)
                ->getSubCollection('sub', true)
                ->get('item'),
            $model->getNamedSubItem()
        );
        $this->assertEquals(
            (string) $attributes
                ->getSubCollection('named', true)
                ->getSubCollection('sub', true)
                ->get('item2'),
            $model->getNamedSubItem2()
        );
        $this->assertEquals((array) $attributes->getRequired('willBeCastedToArray'), $model->getWillBeCastedToArray());
        $this->assertEquals($attributes->getRequired('ignoreOnNull'), $model->getIgnoreOnNull());
        $this->assertEquals($date, $model->getCreatedAt());
        $this->assertNull($model->getUpdatedAt());
    }

    public function testTypeAnnotationMissing(): void
    {
        $resource = new Resource($this->faker()->userName());
        $model = new DataModelWithoutTypeAnnotation();
        $this->expectExceptionObject(
            DataModelSerializerException::typeAnnotationMissing(get_class($model))
        );
        (new ResourceConverter())->toModel($resource, $model);
    }

    public function testTypeAnnotationMismatch(): void
    {
        $resource = new Resource($this->faker()->userName());
        $model = new DataModel();
        $this->expectExceptionObject(
            DataModelSerializerException::modelTypeDoesNotMatchResourceType(
                'dummy-deserializer-model',
                $resource->type()
            )
        );
        (new ResourceConverter())->toModel($resource, $model);
    }

    /**
     * @throws DataModelSerializerException
     * @throws ReflectionException
     */
    public function testDeserializeModelPropertyTypeDoesntAllowNull(): void
    {
        $type = 'dummy-deserializer-model';
        $resource = new Resource(
            $type,
            (string) $this->faker()->numberBetween(),
            [
                'notNullable' => null,
            ]
        );

        $model = new DataModel();
        $this->expectExceptionObject(DataModelSerializerException::propertyIsNotNullable(
            $type,
            get_class($model),
            'notNullable'
        ));
        (new ResourceConverter())->toModel($resource, $model);
    }

    public function testNullableResource(): void
    {
        $model = new DataModel();
        $this->assertEquals($model, (new ResourceConverter())->toModel(null, $model));
    }

    public function testNullableResourceWithInvalidModel(): void
    {
        $this->expectExceptionObject(DataModelSerializerException::typeAnnotationMissing(stdClass::class));
        (new ResourceConverter())->toModel(null, new stdClass());
    }

    public function testNullableValueObject(): void
    {
        $resource = new Resource('dummy-deserializer-model', (string) $this->faker()->numberBetween(), [
            'notNullable' => $this->faker()->slug(),
            'createdAt' => $this->faker()->dateTime()->format(DateTimeInterface::ATOM),
        ]);

        $model = new DataModel();
        (new ResourceConverter())->toModel($resource, $model);
        $this->assertNull($model->getNullableValueObject());
    }

    public function testEmptyArraysInitializeNullableValueObjects(): void
    {
        $resource = new Resource('dummy-deserializer-model', (string) $this->faker()->numberBetween(), [
            'notNullable' => $this->faker()->slug(),
            'createdAt' => $this->faker()->dateTime()->format(DateTimeInterface::ATOM),
            'nullableValueObject' => [],
        ]);

        $model = new DataModel();
        (new ResourceConverter())->toModel($resource, $model);
        $this->assertInstanceOf(ValueObjectWithFactory::class, $model->getNullableValueObject());
        $this->assertNull($model->getNested());
    }

    public function testNestedEmptyArraysInitializeNullableValueObjects(): void
    {
        $resource = new Resource('dummy-deserializer-model', (string) $this->faker()->numberBetween(), [
            'notNullable' => $this->faker()->slug(),
            'createdAt' => $this->faker()->dateTime()->format(DateTimeInterface::ATOM),
            'nested' => [
                'nullableValueObject' => [],
            ],
        ]);

        $model = new DataModel();
        (new ResourceConverter())->toModel($resource, $model);
        $this->assertInstanceOf(ValueObjectWithFactoryWrapper::class, $model->getNested());
        $this->assertInstanceOf(ValueObjectWithFactory::class, $model->getNested()->getNullableValueObject());
    }

    public function testNestedMissingValueDoesNotInitializeValueObjects(): void
    {
        $resource = new Resource('dummy-deserializer-model', (string) $this->faker()->numberBetween(), [
            'notNullable' => $this->faker()->slug(),
            'createdAt' => $this->faker()->dateTime()->format(DateTimeInterface::ATOM),
            'nested' => [
                'nullableValueObject' => null,
            ],
        ]);

        $model = new DataModel();
        (new ResourceConverter())->toModel($resource, $model);
        $this->assertInstanceOf(ValueObjectWithFactoryWrapper::class, $model->getNested());
        $this->assertNull($model->getNested()->getNullableValueObject());
    }

    public function testPlainAttributes(): void
    {
        $resource = new Resource(
            'dummy-deserializer-model-with-plain-attributes',
            (string) $this->faker()->numberBetween(),
            [
                'stringValue' => $this->faker()->userName(),
                'values' => [
                    'number' => (string) $this->faker()->numberBetween(),
                    'ignoreOnNull' => $this->faker()->text(),
                ],
            ],
        );

        $model = new DataModelWithPlainAttributes();
        $this->assertInstanceOf(PlainAttributesInterface::class, $model);
        $attributes = $resource->attributes();
        $this->assertInstanceOf(get_class($model), (new ResourceConverter())->toModel($resource, $model));
        $plainAttributes = $model->getPlainAttributes();
        $this->assertInstanceOf(KeyValueCollectionInterface::class, $plainAttributes);

        $this->assertEquals($attributes->getRequired('stringValue'), $plainAttributes->getRequired('stringValue'));
        $this->assertEquals($attributes->getRequired('values'), $plainAttributes->getRequired('values'));
    }
}
