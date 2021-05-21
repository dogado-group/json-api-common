<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Model\Response;

use Dogado\JsonApi\Model\Document\DocumentInterface;
use Dogado\JsonApi\Support\Collection\KeyValueCollectionInterface;

interface ResponseInterface
{
    public function status(): int;

    public function headers(): KeyValueCollectionInterface;

    public function document(): ?DocumentInterface;
}
