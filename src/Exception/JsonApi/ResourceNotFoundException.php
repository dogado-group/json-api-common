<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Exception\JsonApi;

use Dogado\JsonApi\Exception\JsonApiException;

class ResourceNotFoundException extends JsonApiException
{
    protected int $httpStatus = 404;

    public function __construct(string $type, string $id)
    {
        parent::__construct('Resource "' . $id . '" of type "' . $type . '" not found!');
    }
}
