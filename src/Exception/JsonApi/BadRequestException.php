<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Exception\JsonApi;

use Dogado\JsonApi\Exception\JsonApiException;

class BadRequestException extends JsonApiException
{
    protected int $httpStatus = 400;

    public function __construct(string $message = '')
    {
        if ($message === '') {
            $message = 'Invalid Request!';
        }
        parent::__construct($message);
    }
}
