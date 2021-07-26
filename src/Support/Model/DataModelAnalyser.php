<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Support\Model;

use Dogado\JsonApi\Attribute\Attribute;
use Dogado\JsonApi\Attribute\Id;
use Dogado\JsonApi\Attribute\Type;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionProperty;

class DataModelAnalyser
{
    protected string $className;
    protected ?object $model;
    protected ?string $type = null;
    protected array $resourceValueMap = [];
    protected array $propertyMap = [];

    /**
     * @param object|string $class Either object or class name
     * @throws ReflectionException
     */
    public static function process(object|string $class): self
    {
        if (is_object($class)) {
            $model = $class;
            $className = get_class($class);
        } else {
            $model = null;
            $className = $class;
        }

        return new self($className, $model);
    }

    /**
     * @throws ReflectionException
     */
    private function __construct(
        string $className,
        ?object $model,
        string $propertyNamePrefix = '',
        string $attributeNamePrefix = ''
    ) {
        $this->className = $className;
        $this->model = $model;
        $this->analyse($propertyNamePrefix, $attributeNamePrefix);
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getIdPropertyName(): ?string
    {
        return $this->propertyMap['id'] ?? null;
    }

    public function getIdValue(): ?string
    {
        return $this->resourceValueMap['id'] ?? null;
    }

    /**
     * @return array<string, mixed> All attribute values indexed by a slash separated key.
     */
    public function getAttributeValues(): array
    {
        return $this->resourceValueMap['attributes'] ?? [];
    }

    public function getAttributesPropertyMap(): array
    {
        return $this->propertyMap['attributes'] ?? [];
    }

    /**
     * @throws ReflectionException
     */
    protected function analyse(string $propertyNamePrefix = '', string $attributeNamePrefix = ''): void
    {
        if (!class_exists($this->className)) {
            throw new InvalidArgumentException('The class "' . $this->className . '" does not exist');
        }

        $reflection = new ReflectionClass($this->className);

        foreach ($reflection->getAttributes() as $reflectionAttribute) {
            $attribute = $reflectionAttribute->newInstance();
            if ($attribute instanceof Type) {
                $this->type = $attribute->name;
            }
        }

        foreach ($reflection->getProperties() as $property) {
            $property->setAccessible(true);

            foreach ($property->getAttributes() as $reflectionAttribute) {
                $attribute = $reflectionAttribute->newInstance();
                $this->parseId($property, $attribute);
                $this->parseAttribute($property, $attribute, $propertyNamePrefix, $attributeNamePrefix);
            }
        }
    }

    protected function parseId(ReflectionProperty $property, object $annotation): void
    {
        if (!$annotation instanceof Id) {
            return;
        }

        $this->propertyMap['id'] = $property->getName();
        if (null === $this->model) {
            $this->resourceValueMap['id'] = null;
            return;
        }

        $value = $property->getValue($this->model);
        $this->resourceValueMap['id'] = null !== $value ? (string) $value : null;
    }

    /**
     * @throws ReflectionException
     */
    protected function parseAttribute(
        ReflectionProperty $property,
        object $attribute,
        string $propertyNamePrefix = '',
        string $attributeNamePrefix = ''
    ): void {
        if (!$attribute instanceof Attribute) {
            return;
        }

        $attributeName = trim($attributeNamePrefix . '/' . ($attribute->name ?? $property->getName()));
        $attributeName = preg_replace('/\/+/', '/', $attributeName) ?? $attributeName;
        $attributeName = trim($attributeName, '/');
        if (empty($attributeName)) {
            return;
        }

        $propertyMapName = trim($propertyNamePrefix . '/' . $property->getName(), '/');
        if (!isset($this->propertyMap['attributes'])) {
            $this->propertyMap['attributes'] = [];
            $this->resourceValueMap['attributes'] = [];
        }

        if (null !== $this->model && $this->model instanceof CustomAttributeGetterInterface) {
            $value = $this->model->__getAttribute($property->getName());
            if (null !== $value) {
                $this->registerAttributeValue($attribute, $attributeName, $propertyMapName, $value);
                return;
            }
        }

        $value = $this->model ? $property->getValue($this->model) : null;

        $type = $property->getType();
        if (
            $type instanceof ReflectionNamedType &&
            !$type->isBuiltin() &&
            class_exists($type->getName())
        ) {
            $this->parseValueObject($type->getName(), $value, $propertyMapName, $attributeName);
            return;
        }

        $this->registerAttributeValue($attribute, $attributeName, $propertyMapName, $value);
    }

    /**
     * @throws ReflectionException
     */
    protected function parseValueObject(
        string $className,
        ?object $valueObject,
        string $propertyPrefix,
        string $attributePrefix
    ): void {
        $self = new self($className, $valueObject, $propertyPrefix, $attributePrefix);

        // In case the value object has no attributes, we must register the attribute prefix with a null value.
        $this->propertyMap['attributes'] = array_merge(
            $this->propertyMap['attributes'],
            [$attributePrefix => $propertyPrefix],
            $self->getAttributesPropertyMap(),
        );
        $this->resourceValueMap['attributes'] = array_merge(
            $this->resourceValueMap['attributes'],
            $self->getAttributeValues() ?: [$attributePrefix => null !== $valueObject ? [] : null]
        );
    }

    private function registerAttributeValue(
        Attribute $attribute,
        string $attributeName,
        string $propertyMapName,
        mixed $value
    ): void {
        $this->propertyMap['attributes'][$attributeName] = $propertyMapName;

        if (null === $value && $attribute->ignoreOnNull) {
            return;
        }

        $this->resourceValueMap['attributes'][$attributeName] = $value;
    }
}
