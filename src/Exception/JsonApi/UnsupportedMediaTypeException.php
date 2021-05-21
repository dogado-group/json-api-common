<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Exception\JsonApi;

use Dogado\JsonApi\Exception\JsonApiException;
use Dogado\JsonApi\Model\JsonApiInterface;

class UnsupportedMediaTypeException extends JsonApiException
{
    protected int $httpStatus = 415;

    public function __construct(string $contentType = '')
    {
        parent::__construct(sprintf(
            'Invalid content type "%s" given, "%s" expected',
            $contentType,
            JsonApiInterface::CONTENT_TYPE
        ));
    }
}
