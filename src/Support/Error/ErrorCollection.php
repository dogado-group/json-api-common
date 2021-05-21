<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Support\Error;

use Dogado\JsonApi\Model\Error\ErrorInterface;
use Dogado\JsonApi\Support\Collection\AbstractCollection;

class ErrorCollection extends AbstractCollection implements ErrorCollectionInterface
{
    public function add(ErrorInterface $error): ErrorCollectionInterface
    {
        $this->collection[] = $error;

        return $this;
    }

    public function first(): ?ErrorInterface
    {
        return reset($this->collection) ?: null;
    }
}
