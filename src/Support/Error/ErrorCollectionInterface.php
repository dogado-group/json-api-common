<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Support\Error;

use Dogado\JsonApi\Model\Error\ErrorInterface;
use Dogado\JsonApi\Support\Collection\CollectionInterface;

interface ErrorCollectionInterface extends CollectionInterface
{
    /** @return ErrorInterface[] */
    public function all(): array;

    public function add(ErrorInterface $error): ErrorCollectionInterface;

    public function first(): ?ErrorInterface;
}
