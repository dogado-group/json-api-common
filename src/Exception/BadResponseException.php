<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Exception;

use Exception;

class BadResponseException extends Exception
{
    public const CODE_INVALID_JSON_DOCUMENT = 100;

    public static function invalidJsonDocument(?string $jsonError): self
    {
        $jsonError = null !== $jsonError ? ': ' . $jsonError : '';
        return new self(
            'The response body is no valid json document' . $jsonError,
            self::CODE_INVALID_JSON_DOCUMENT
        );
    }
}
