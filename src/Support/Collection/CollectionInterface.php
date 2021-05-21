<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Support\Collection;

use Countable;

interface CollectionInterface extends Countable
{
    public function all(): array;

    public function isEmpty(): bool;

    public function count(): int;
}
