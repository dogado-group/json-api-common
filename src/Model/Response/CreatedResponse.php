<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Model\Response;

use Dogado\JsonApi\Model\Document\DocumentInterface;
use Dogado\JsonApi\Support\Collection\KeyValueCollection;
use Dogado\JsonApi\Support\Collection\KeyValueCollectionInterface;

class CreatedResponse extends AbstractResponse
{
    private ?DocumentInterface $document;

    public function __construct(
        string $location,
        ?KeyValueCollectionInterface $headers = null,
        ?DocumentInterface $document = null
    ) {
        parent::__construct(201, $headers ?? new KeyValueCollection());
        $this->headers()->set('Location', $location);
        $this->document = $document;
    }

    public function document(): ?DocumentInterface
    {
        return $this->document;
    }
}
