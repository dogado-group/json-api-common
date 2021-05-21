<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Factory;

use Dogado\JsonApi\Model\Document\DocumentInterface;
use Dogado\JsonApi\Model\Response\AcceptedResponse;
use Dogado\JsonApi\Model\Response\CreatedResponse;
use Dogado\JsonApi\Model\Response\DocumentResponse;
use Dogado\JsonApi\Model\Response\EmptyResponse;
use Dogado\JsonApi\Support\Collection\KeyValueCollectionInterface;

class ResponseFactory
{
    public function accepted(?KeyValueCollectionInterface $headers = null): AcceptedResponse
    {
        return new AcceptedResponse($headers);
    }

    public function created(
        string $location,
        ?KeyValueCollectionInterface $headers = null,
        ?DocumentInterface $document = null
    ): CreatedResponse {
        return new CreatedResponse($location, $headers, $document);
    }

    public function document(
        DocumentInterface $document,
        ?KeyValueCollectionInterface $headers = null,
        int $status = 200
    ): DocumentResponse {
        return new DocumentResponse($document, $headers, $status);
    }

    public function empty(?KeyValueCollectionInterface $headers = null): EmptyResponse
    {
        return new EmptyResponse($headers);
    }
}
