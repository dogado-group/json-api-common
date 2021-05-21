<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Exception;

use Exception;

class DataModelSerializerException extends Exception
{
    public const CODE_TYPE_ANNOTATION_MISSING = 100;
    public const CODE_MODEL_TYPE_DOES_NOT_MATCH_RESOURCE_TYPE = 101;
    public const CODE_PROPERTY_IS_NOT_NULLABLE = 102;

    public static function typeAnnotationMissing(string $modelClass): self
    {
        return new self(
            sprintf('The class "%s" does not contain the required type annotation', $modelClass),
            self::CODE_TYPE_ANNOTATION_MISSING
        );
    }

    public static function modelTypeDoesNotMatchResourceType(string $modelType, string $resourceType): self
    {
        return new self(
            sprintf('The model type "%s" does not match the resource type "%s"', $modelType, $resourceType),
            self::CODE_MODEL_TYPE_DOES_NOT_MATCH_RESOURCE_TYPE
        );
    }

    public static function propertyIsNotNullable(string $resourceType, string $modelClass, string $propertyName): self
    {
        return new self(
            sprintf(
                'The class property %s::%s is not nullable, but the %s resource attribute value is null or does not' .
                    ' exist',
                $modelClass,
                $propertyName,
                $resourceType
            ),
            self::CODE_PROPERTY_IS_NOT_NULLABLE
        );
    }
}
