<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Model\Document;

use Dogado\JsonApi\Support\Error\ErrorCollectionInterface;
use Dogado\JsonApi\Support\Collection\KeyValueCollectionInterface;
use Dogado\JsonApi\Support\Resource\Link\LinkCollectionInterface;
use Dogado\JsonApi\Support\Resource\ResourceCollectionInterface;

interface DocumentInterface
{
    /**
     * Indicates if the contained data should be handled as object collection or single object
     */
    public function shouldBeHandledAsCollection(): bool;

    public function links(): LinkCollectionInterface;

    public function data(): ResourceCollectionInterface;

    public function included(): ResourceCollectionInterface;

    public function metaInformation(): KeyValueCollectionInterface;

    public function errors(): ErrorCollectionInterface;
}
