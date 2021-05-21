<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Exception\JsonApi;

use Dogado\JsonApi\Exception\JsonApiException;

class ValidationException extends JsonApiException
{
    protected int $httpStatus = 422;
}
