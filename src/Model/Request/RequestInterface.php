<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Model\Request;

use Dogado\JsonApi\Model\Document\DocumentInterface;
use Dogado\JsonApi\Model\Resource\ResourceInterface;
use Dogado\JsonApi\Support\Collection\KeyValueCollectionInterface;
use Psr\Http\Message\UriInterface;

interface RequestInterface
{
    public const ORDER_ASC = 'asc';
    public const ORDER_DESC = 'desc';

    public function method(): string;

    public function uri(): UriInterface;

    /**
     * Contains all request headers.
     */
    public function headers(): KeyValueCollectionInterface;

    /**
     * Contains the requested resource type.
     */
    public function type(): string;

    public function id(): ?string;

    public function relationship(): ?string;

    /**
     * Indicates if the response for this request should contain attributes for a resource
     */
    public function requestsAttributes(): bool;

    /**
     * Indicates if the response for this request should contain meta information for a resource
     */
    public function requestsMetaInformation(): bool;

    /**
     * Indicates if the response for this request should contain relationships for a resource
     */
    public function requestsRelationships(): bool;

    /**
     * Indicates if the response should contain the given field for the given type.
     */
    public function requestsField(string $type, string $name): bool;

    /**
     * Indicates if a response should include the given relationship.
     */
    public function requestsInclude(string $relationship): bool;

    /**
     * Retrieve all filter items.
     */
    public function filter(): KeyValueCollectionInterface;

    /**
     * Retrieve a collection of sorting options.
     * The sort field is the key and the value contains either self::ORDER_ASC or self::ORDER_DESC.
     */
    public function sorting(): KeyValueCollectionInterface;

    /**
     * Retrieve all pagination options.
     */
    public function pagination(): KeyValueCollectionInterface;

    /**
     * Retrieve the request document if available.
     */
    public function document(): ?DocumentInterface;

    /**
     * Creates a request for the given relationship.
     * If called twice, the call will return the already created sub request.
     * A sub request does not contain pagination and sorting from its parent.
     */
    public function createSubRequest(
        string $relationship,
        ?ResourceInterface $resource = null,
        bool $keepFilters = false
    ): RequestInterface;
}
