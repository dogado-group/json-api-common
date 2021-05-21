<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Serializer;

use Dogado\JsonApi\Model\Document\DocumentInterface;

interface DocumentSerializerInterface
{
    public function serializeDocument(DocumentInterface $document, bool $identifiersOnly = false): array;
}
