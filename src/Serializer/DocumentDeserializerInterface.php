<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Serializer;

use Dogado\JsonApi\Model\Document\DocumentInterface;

interface DocumentDeserializerInterface
{
    public function deserializeDocument(array $documentData): DocumentInterface;
}
