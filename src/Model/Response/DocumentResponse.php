<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Model\Response;

use Dogado\JsonApi\Model\Document\DocumentInterface;
use Dogado\JsonApi\Support\Collection\KeyValueCollection;
use Dogado\JsonApi\Support\Collection\KeyValueCollectionInterface;

class DocumentResponse extends AbstractResponse
{
    private DocumentInterface $document;

    public function __construct(
        DocumentInterface $document,
        ?KeyValueCollectionInterface $headers = null,
        int $status = 200
    ) {
        parent::__construct($status, $headers ?? new KeyValueCollection());
        $this->document = $document;
    }

    public function document(): ?DocumentInterface
    {
        return $this->document;
    }
}
