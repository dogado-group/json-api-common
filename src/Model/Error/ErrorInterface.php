<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Model\Error;

use Dogado\JsonApi\Support\Collection\KeyValueCollectionInterface;

interface ErrorInterface
{
    public function status(): int;

    public function code(): string;

    public function title(): string;

    public function detail(): string;

    public function metaInformation(): KeyValueCollectionInterface;

    public function source(): KeyValueCollectionInterface;
}
