<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Model\Response;

use Dogado\JsonApi\Model\Document\DocumentInterface;
use Dogado\JsonApi\Support\Collection\KeyValueCollection;
use Dogado\JsonApi\Support\Collection\KeyValueCollectionInterface;

class AcceptedResponse extends AbstractResponse
{
    public function __construct(?KeyValueCollectionInterface $headers = null)
    {
        parent::__construct(202, $headers ?? new KeyValueCollection());
    }

    public function document(): ?DocumentInterface
    {
        return null;
    }
}
