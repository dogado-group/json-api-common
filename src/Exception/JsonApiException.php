<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Exception;

use Dogado\JsonApi\Model\Error\Error;
use Dogado\JsonApi\Model\Error\ErrorInterface;
use Dogado\JsonApi\Support\Error\ErrorCollection;
use Dogado\JsonApi\Support\Error\ErrorCollectionInterface;
use Exception;
use Throwable;

class JsonApiException extends Exception
{
    protected ErrorCollectionInterface $errorCollection;
    protected int $httpStatus = 500;

    public function __construct(
        string $message = '',
        int $code = 0,
        Throwable $previous = null,
        ErrorCollectionInterface $errors = null
    ) {
        parent::__construct($message, $code, $previous);

        $this->errorCollection = $errors ?? new ErrorCollection();
        if ($this->errorCollection->isEmpty()) {
            $this->errorCollection->add(
                Error::createFrom($this)
            );
        }

        $error = $this->errorCollection->first();
        assert($error instanceof ErrorInterface);
        $this->setHttpStatus($error->status());
    }

    public function getHttpStatus(): int
    {
        return $this->httpStatus;
    }

    public function setHttpStatus(int $httpStatus): self
    {
        $this->httpStatus = $httpStatus;
        return $this;
    }

    public function errors(): ErrorCollectionInterface
    {
        return $this->errorCollection;
    }
}
