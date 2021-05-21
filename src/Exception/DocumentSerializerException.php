<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Exception;

use Exception;

class DocumentSerializerException extends Exception
{
    public const CODE_JSON_DOCUMENT_GENERATION_FAILED = 100;

    public static function unableGenerateJsonDocument(?string $jsonError = null): self
    {
        return new self(
            sprintf('Unable to generate json response document%s', $jsonError ? ': ' . $jsonError : ''),
            self::CODE_JSON_DOCUMENT_GENERATION_FAILED
        );
    }
}
