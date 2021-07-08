<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Converter;

use Dogado\JsonApi\Exception\DataModelSerializerException;
use Dogado\JsonApi\Model\Resource\ResourceInterface;
use Dogado\JsonApi\Support\Model\CustomAttributeSetterInterface;
use Dogado\JsonApi\Support\Model\DataModelAnalyser;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;

class ResourceConverter
{
    protected ?ResourceInterface $resource = null;

    /**
     * @throws DataModelSerializerException
     * @throws ReflectionException
     */
    public function toModel(?ResourceInterface $resource, object $model): object
    {
        $this->resource = $resource;
        $analyser = DataModelAnalyser::process($model);
        if (empty($analyser->getType())) {
            throw DataModelSerializerException::typeAnnotationMissing(get_class($model));
        }

        // allow null for convenience
        if (null === $resource) {
            return $model;
        }

        if ($resource->type() !== $analyser->getType()) {
            throw DataModelSerializerException::modelTypeDoesNotMatchResourceType(
                $analyser->getType(),
                $resource->type()
            );
        }

        $reflection = new ReflectionClass($model);

        if (null !== $analyser->getIdPropertyName()) {
            $this->setValue($reflection, $model, [$analyser->getIdPropertyName()], $resource->id());
        }

        $attributeValues = $resource->attributes()->all();
        foreach ($analyser->getAttributesPropertyMap() as $attributeMap => $propertyMap) {
            $this->setValue(
                $reflection,
                $model,
                explode('/', $propertyMap),
                $this->getNestedAttributeValue($attributeValues, $attributeMap)
            );
        }

        return $model;
    }

    /**
     * @throws DataModelSerializerException
     * @throws ReflectionException
     */
    protected function setValue(ReflectionClass $reflection, object $model, array $propertyMap, mixed $value): void
    {
        $propertyName = array_shift($propertyMap);
        if (!$reflection->hasProperty($propertyName)) {
            return;
        }

        if (
            $model instanceof CustomAttributeSetterInterface &&
            true === $model->__setAttribute($propertyName, $value)
        ) {
            return;
        }

        $property = $reflection->getProperty($propertyName);
        if (null !== $property->getType() && !$property->getType() instanceof ReflectionNamedType) {
            // other reflection types, like union types, are not supported
            return;
        }

        if (null === $property->getType() || $property->getType()->getName() === gettype($value)) {
            $property->setAccessible(true);
            $property->setValue($model, $value);
            return;
        }

        if (class_exists($property->getType()->getName()) && 0 < count($propertyMap)) {
            $property->setAccessible(true);
            $valueObject = $property->getValue($model);
            if (null === $valueObject) {
                return;
            }

            $this->setValue(new ReflectionClass($valueObject), $valueObject, $propertyMap, $value);
            return;
        }

        if (null === $value) {
            if (!$property->getType()->allowsNull()) {
                throw DataModelSerializerException::propertyIsNotNullable(
                    $this->resource ? $this->resource->type() : 'unknown',
                    get_class($model),
                    $propertyName
                );
            }

            $property->setAccessible(true);
            $property->setValue($model, $value);
            return;
        }

        if (in_array(gettype($value), ['resource', 'array', 'object'])) {
            return;
        }

        switch ($property->getType()->getName()) {
            case 'boolean':
            case 'bool':
                $property->setAccessible(true);
                $property->setValue($model, (bool) $value);
                break;
            case 'integer':
            case 'int':
                $property->setAccessible(true);
                $property->setValue($model, (int) $value);
                break;
            case 'float':
                $property->setAccessible(true);
                $property->setValue($model, (float) $value);
                break;
            case 'string':
                $property->setAccessible(true);
                $property->setValue($model, (string) $value);
                break;
            case 'array':
                $property->setAccessible(true);
                $property->setValue($model, (array) $value);
                break;
        }
    }

    private function getNestedAttributeValue(array $attributeValues, string $attributeMap): mixed
    {
        foreach (explode('/', $attributeMap) as $attribute) {
            if (is_array($attributeValues) && array_key_exists($attribute, $attributeValues)) {
                $attributeValues = $attributeValues[$attribute];
            } else {
                return null;
            }
        }

        return $attributeValues;
    }
}
