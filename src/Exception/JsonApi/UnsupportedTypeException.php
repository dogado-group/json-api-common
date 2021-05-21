<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Exception\JsonApi;

use Dogado\JsonApi\Exception\JsonApiException;

class UnsupportedTypeException extends JsonApiException
{
    protected int $httpStatus = 404;

    public function __construct(string $type)
    {
        parent::__construct('Resource type "' . $type . '" not found');
    }
}
