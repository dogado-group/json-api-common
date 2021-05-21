<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Support\Collection;

abstract class AbstractCollection implements CollectionInterface
{
    protected array $collection;

    public function __construct(array $data = [])
    {
        $this->collection = $data;
    }

    public function all(): array
    {
        return $this->collection;
    }

    public function isEmpty(): bool
    {
        return 0 === $this->count();
    }

    public function count(): int
    {
        return count($this->collection);
    }
}
