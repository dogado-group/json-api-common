<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Model\Error;

use Dogado\JsonApi\Exception\JsonApiException;
use Dogado\JsonApi\Support\Collection\KeyValueCollection;
use Dogado\JsonApi\Support\Collection\KeyValueCollectionInterface;
use Throwable;

class Error implements ErrorInterface
{
    private KeyValueCollection $metaCollection;
    private KeyValueCollection $source;

    public function __construct(
        private int $status,
        private string $title,
        private string $detail = '',
        private string $code = ''
    ) {
        $this->metaCollection = new KeyValueCollection();
        $this->source = new KeyValueCollection();
    }

    public static function createFrom(Throwable $throwable, bool $debug = false): ErrorInterface
    {
        $status = 500;
        if ($throwable instanceof JsonApiException) {
            $status = $throwable->getHttpStatus();
        }

        $code = '';
        if ($throwable->getCode() !== 0) {
            $code = (string)$throwable->getCode();
        }

        $error = new self(
            $status,
            $throwable->getMessage(),
            ($debug ? $throwable->getTraceAsString() : ''),
            $code
        );

        if ($debug) {
            $error->metaInformation()->set('file', $throwable->getFile());
            $error->metaInformation()->set('line', $throwable->getLine());
        }

        return $error;
    }

    public function status(): int
    {
        return $this->status;
    }

    public function code(): string
    {
        return $this->code;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function detail(): string
    {
        return $this->detail;
    }

    public function metaInformation(): KeyValueCollectionInterface
    {
        return $this->metaCollection;
    }

    public function source(): KeyValueCollectionInterface
    {
        return $this->source;
    }
}
