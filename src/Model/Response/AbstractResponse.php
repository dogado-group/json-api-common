<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Model\Response;

use Dogado\JsonApi\Model\JsonApiInterface;
use Dogado\JsonApi\Support\Collection\KeyValueCollectionInterface;

abstract class AbstractResponse implements ResponseInterface
{
    private int $status;
    private KeyValueCollectionInterface $headers;

    public function __construct(int $status, KeyValueCollectionInterface $headers)
    {
        $this->status = $status;
        $this->headers = $headers;
        $this->headers->set('Content-Type', JsonApiInterface::CONTENT_TYPE);
    }

    public function status(): int
    {
        return $this->status;
    }

    public function headers(): KeyValueCollectionInterface
    {
        return $this->headers;
    }
}
