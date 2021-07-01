<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Converter;

use Dogado\JsonApi\Exception\DataModelSerializerException;
use Dogado\JsonApi\Model\Resource\Resource as JsonApiResource;
use Dogado\JsonApi\Model\Resource\ResourceInterface;
use Dogado\JsonApi\Support\Model\DataModelAnalyser;
use ReflectionException;

class ModelConverter
{
    /**
     * @throws DataModelSerializerException
     * @throws ReflectionException
     */
    public function toResource(object $model): ResourceInterface
    {
        $analyser = DataModelAnalyser::process($model);
        if (empty($analyser->getType())) {
            throw DataModelSerializerException::typeAnnotationMissing(get_class($model));
        }

        $attributes = [];
        foreach ($analyser->getAttributeValues() as $keyMap => $value) {
            $this->setNestedAttributeValue($attributes, $keyMap, $value);
        }

        return new JsonApiResource($analyser->getType(), $analyser->getIdValue(), $attributes);
    }

    private function setNestedAttributeValue(array &$attributes, string $keyMap, mixed $value): void
    {
        $keys = explode('/', $keyMap);
        foreach ($keys as $i => $keyPart) {
            if (1 === count($keys)) {
                break;
            }

            unset($keys[$i]);

            if (!isset($attributes[$keyPart]) || !is_array($attributes[$keyPart])) {
                $attributes[$keyPart] = [];
            }
            $attributes = &$attributes[$keyPart];
        }

        $attributes[array_shift($keys)] = $value;
    }
}
