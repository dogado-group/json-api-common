<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Converter;

use Dogado\JsonApi\Exception\DataModelSerializerException;
use Dogado\JsonApi\Model\Resource\Resource as JsonApiResource;
use Dogado\JsonApi\Model\Resource\ResourceInterface;
use Dogado\JsonApi\Support\Model\DataModelAnalyser;
use Illuminate\Support\Arr;
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
        foreach ($analyser->getAttributeValues() as $key => $value) {
            Arr::set($attributes, str_replace('/', '.', $key), $value);
        }

        return new JsonApiResource($analyser->getType(), $analyser->getIdValue(), $attributes);
    }
}
