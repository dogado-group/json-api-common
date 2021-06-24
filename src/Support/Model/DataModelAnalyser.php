<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Support\Model;

use Doctrine\Common\Annotations\AnnotationReader;
use Dogado\JsonApi\Annotations\Attribute;
use Dogado\JsonApi\Annotations\Id;
use Dogado\JsonApi\Annotations\Type;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionProperty;

class DataModelAnalyser
{
    protected AnnotationReader $annotationReader;
    protected string $className;
    protected ?object $model;
    protected ?string $type = null;
    protected array $resourceValueMap = [];
    protected array $propertyMap = [];

    /**
     * @param object|string $class Either object or class name
     * @throws ReflectionException
     * @throws InvalidArgumentException
     */
    public static function process($class): self
    {
        if (!is_object($class) && !is_string($class)) {
            throw new InvalidArgumentException(
                sprintf('$class must either be a class name or an object, %s given', gettype($class))
            );
        }

        if (is_object($class)) {
            $model = $class;
            $className = get_class($class);
        } else {
            $model = null;
            $className = $class;
        }

        return new self(new AnnotationReader(), $className, $model);
    }

    /**
     * @throws ReflectionException
     */
    private function __construct(
        AnnotationReader $annotationReader,
        string $className,
        ?object $model,
        string $propertyNamePrefix = '',
        string $attributeNamePrefix = ''
    ) {
        $this->annotationReader = $annotationReader;
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
     * @return mixed[]
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

        foreach ($this->annotationReader->getClassAnnotations($reflection) as $annotation) {
            if ($annotation instanceof Type) {
                $this->type = $annotation->value;
            }
        }
        // php 8 attribute support
        foreach ($reflection->getAttributes() as $reflectionAttribute) {
            $attribute = $reflectionAttribute->newInstance();
            if ($attribute instanceof Type) {
                $this->type = $attribute->value;
            }
        }

        foreach ($reflection->getProperties() as $property) {
            $property->setAccessible(true);

            foreach ($this->annotationReader->getPropertyAnnotations($property) as $annotation) {
                $this->parseId($property, $annotation);
                $this->parseAttribute($property, $annotation, $propertyNamePrefix, $attributeNamePrefix);
            }
            // php 8 attribute support
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
        object $annotation,
        string $propertyNamePrefix = '',
        string $attributeNamePrefix = ''
    ): void {
        if (!$annotation instanceof Attribute) {
            return;
        }

        $attributeName = trim($attributeNamePrefix . '/' . ($annotation->value ?? $property->getName()));
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
                $this->registerAttributeValue($annotation, $attributeName, $propertyMapName, $value);
                return;
            }
        }

        $value = $this->model ? $property->getValue($this->model) : null;

        if (
            $property->getType() instanceof ReflectionNamedType &&
            !$property->getType()->isBuiltin() &&
            class_exists($property->getType()->getName())
        ) {
            $this->parseValueObject($property->getType()->getName(), $value, $propertyMapName, $attributeName);
            return;
        }

        $this->registerAttributeValue($annotation, $attributeName, $propertyMapName, $value);
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
        $self = new self($this->annotationReader, $className, $valueObject, $propertyPrefix, $attributePrefix);

        // In case the value object has no attributes, we must register the attribute prefix with a null value.
        $this->propertyMap['attributes'] = array_merge(
            $this->propertyMap['attributes'],
            $self->getAttributesPropertyMap() ?: [$attributePrefix => $propertyPrefix]
        );
        $this->resourceValueMap['attributes'] = array_merge(
            $this->resourceValueMap['attributes'],
            $self->getAttributeValues() ?: [$attributePrefix => null]
        );
    }

    /**
     * @param mixed $value
     */
    private function registerAttributeValue(
        Attribute $attribute,
        string $attributeName,
        string $propertyMapName,
        $value
    ): void {
        $this->propertyMap['attributes'][$attributeName] = $propertyMapName;

        if (null === $value && $attribute->ignoreOnNull) {
            return;
        }

        $this->resourceValueMap['attributes'][$attributeName] = $value;
    }
}
