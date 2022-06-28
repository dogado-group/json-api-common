<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Model\Request;

use Dogado\JsonApi\Exception\JsonApi\BadRequestException;
use Dogado\JsonApi\Model\Document\DocumentInterface;
use Dogado\JsonApi\Model\JsonApiInterface;
use Dogado\JsonApi\Model\Resource\ResourceInterface;
use Dogado\JsonApi\Support\Collection\KeyValueCollection;
use Dogado\JsonApi\Support\Collection\KeyValueCollectionInterface;
use Psr\Http\Message\UriInterface;

class Request implements RequestInterface
{
    private string $method;
    private UriInterface $uri;
    private ?string $apiPrefix;
    private ?string $fileInPath;
    private KeyValueCollectionInterface $headers;
    private string $type;
    private ?string $id;
    private ?string $relationship = null;
    private bool $requestsAttributes = true;
    private bool $requestsMetaInformation = true;
    private array $fields = [];
    private array $includes = [];
    private KeyValueCollectionInterface $filter;
    private KeyValueCollectionInterface $sorting;
    private KeyValueCollectionInterface $pagination;
    private ?DocumentInterface $document;
    private KeyValueCollectionInterface $customQueryParameters;

    /** @var RequestInterface[] */
    private array $subRequests = [];

    /**
     * @throws BadRequestException
     */
    public static function createFromHttpRequest(
        \Psr\Http\Message\RequestInterface $request,
        ?DocumentInterface $document,
        ?string $apiPrefix
    ): self {
        $apiRequest = new self($request->getMethod(), $request->getUri(), $document, $apiPrefix);

        foreach ($request->getHeaders() as $header => $values) {
            $apiRequest->headers()->set((string) $header, count($values) !== 1 ? $values : $values[0]);
        }

        return $apiRequest;
    }

    /**
     * @throws BadRequestException
     */
    public function __construct(
        string $method,
        UriInterface $uri,
        ?DocumentInterface $document = null,
        ?string $apiPrefix = null
    ) {
        $this->method = strtoupper($method);
        if (!in_array($this->method, ['GET', 'POST', 'PATCH', 'DELETE'], true)) {
            throw new BadRequestException('Invalid http method');
        }
        $this->uri = $uri;
        $this->document = $document;
        $this->apiPrefix = $apiPrefix ? preg_quote(trim($apiPrefix, '/'), '/') : null;

        $this->parseUriPath($this->uri->getPath());
        $this->parseUriQuery($this->uri->getQuery());

        $this->headers = new KeyValueCollection();
        $this->headers->set('Content-Type', JsonApiInterface::CONTENT_TYPE);
    }

    /**
     * @throws BadRequestException
     */
    private function parseUriPath(string $path): void
    {
        preg_match(
            '/^(([a-zA-Z0-9_\-.\/]+.php)(\/)|)(' . $this->apiPrefix . ')([\/a-zA-Z0-9_\-*.]+)$/',
            trim($path, '/'),
            $matches
        );

        if (array_key_exists(3, $matches)) {
            $this->fileInPath = $matches[3];
        }

        if (!array_key_exists(5, $matches)) {
            $matches[5] = '';
        }

        $segments = explode('/', trim($matches[5], '/'));
        // fill missing segments
        while (count($segments) < 4) {
            $segments[] = null;
        }

        if (empty($segments[0])) {
            throw new BadRequestException('Resource type missing.');
        }
        $this->type = $segments[0];
        $this->id = $segments[1];
        if ($this->id) {
            // parse relationship/related request
            if ($segments[3]) {
                if ($segments[2] !== 'relationships') {
                    throw new BadRequestException('Invalid relationship request!');
                }
                $this->requestsAttributes = false;
                $this->requestsMetaInformation = false;
                $this->relationship = (string)$segments[3];
            } elseif ($segments[2]) {
                $this->relationship = (string)$segments[2];
            }
        }
    }

    /**
     * @throws BadRequestException
     */
    private function parseUriQuery(string $uriQuery): void
    {
        parse_str($uriQuery, $query);
        $query = new KeyValueCollection($query);

        $this->includes = [];
        if ($query->has('include')) {
            if (!is_string($query->getRequired('include'))) {
                throw new BadRequestException('Invalid include parameter given!');
            }

            $this->includes = explode(',', $query->pull('include'));
        }

        $this->fields = [];
        if ($query->has('fields')) {
            if (!is_array($query->getRequired('fields'))) {
                throw new BadRequestException('Invalid fields parameter given!');
            }
            foreach ((array)$query->pull('fields') as $type => $fields) {
                foreach (explode(',', $fields) as $field) {
                    $this->fields[$type][] = $field;
                }
            }
        }

        $filter = [];
        if ($query->has('filter')) {
            $filter = $query->pull('filter');
            if (is_string($filter)) {
                $filter = json_decode($filter, true);
            }
            if (!is_array($filter)) {
                throw new BadRequestException('Invalid filter parameter given!');
            }
        }

        $this->filter = new KeyValueCollection($filter);

        $pagination = [];
        if ($query->has('page')) {
            if (!is_array($query->getRequired('page'))) {
                throw new BadRequestException('Invalid page parameter given!');
            }
            $pagination = (array) $query->pull('page');
        }

        $this->pagination = new KeyValueCollection($pagination);

        $sorting = [];
        if ($query->has('sort')) {
            if (!is_string($query->getRequired('sort'))) {
                throw new BadRequestException('Invalid sort parameter given!');
            }
            foreach (explode(',', $query->pull('sort')) as $field) {
                $direction = self::ORDER_ASC;
                if (str_starts_with($field, '-')) {
                    $field = substr($field, 1);
                    $direction = self::ORDER_DESC;
                }
                $sorting[$field] = $direction;
            }
        }

        $this->sorting = new KeyValueCollection($sorting);
        $this->customQueryParameters = $query;
    }

    public function method(): string
    {
        return $this->method;
    }

    public function uri(): UriInterface
    {
        $this->updateUriQuery();
        return $this->uri;
    }

    public function customQueryParameters(): KeyValueCollectionInterface
    {
        return $this->customQueryParameters;
    }

    /**
     * Contains all request headers
     */
    public function headers(): KeyValueCollectionInterface
    {
        return $this->headers;
    }

    /**
     * Contains the requested resource type
     */
    public function type(): string
    {
        return $this->type;
    }

    public function id(): ?string
    {
        return $this->id;
    }

    public function relationship(): ?string
    {
        return $this->relationship;
    }

    /**
     * Indicates if the response for this request should contain attributes for a resource
     */
    public function requestsAttributes(): bool
    {
        return $this->requestsAttributes;
    }

    /**
     * Indicates if the response for this request should contain meta information for a resource
     */
    public function requestsMetaInformation(): bool
    {
        return $this->requestsMetaInformation;
    }

    /**
     * Indicates if the response for this request should contain relationships for a resource
     */
    public function requestsRelationships(): bool
    {
        return ($this->requestsAttributes() || $this->requestsMetaInformation()) || count($this->includes) > 0;
    }

    /**
     * Define a field as requested.
     */
    public function field(string $type, string $name): self
    {
        if (!isset($this->fields[$type])) {
            $this->fields[$type] = [];
        }

        $this->fields[$type][] = $name;
        return $this;
    }

    /**
     * Indicates if the response should contain the given field for the given type.
     */
    public function requestsField(string $type, string $name): bool
    {
        if (!array_key_exists($type, $this->fields)) {
            return true;
        }

        return in_array($name, $this->fields[$type], true);
    }

    /**
     * Define a relationship as included.
     */
    public function include(string $relationship): self
    {
        $this->includes[] = $relationship;
        return $this;
    }

    /**
     * Indicates if a response should include the given relationship.
     */
    public function requestsInclude(string $relationship): bool
    {
        return in_array($relationship, $this->includes, true);
    }

    /**
     * Retrieve all filter items.
     */
    public function filter(): KeyValueCollectionInterface
    {
        return $this->filter;
    }

    /**
     * Retrieve a collection of sorting options. The sort field is the key and the value contains either
     * RequestInterface::ORDER_ASC or RequestInterface::ORDER_DESC.
     */
    public function sorting(): KeyValueCollectionInterface
    {
        return $this->sorting;
    }

    /**
     * Retrieve all pagination options.
     */
    public function pagination(): KeyValueCollectionInterface
    {
        return $this->pagination;
    }

    /**
     * Retrieve the request document if available.
     */
    public function document(): ?DocumentInterface
    {
        return $this->document;
    }

    /**
     * Creates a request for the given relationship.
     * If called twice, the call will return the already created sub request.
     * A sub request does not contain pagination and sorting from its parent.
     *
     * @throws BadRequestException
     */
    public function createSubRequest(
        string $relationship,
        ?ResourceInterface $resource = null,
        bool $keepFilters = false
    ): RequestInterface {
        $requestKey = $relationship . ($keepFilters ? '-filtered' : '-not-filtered');
        if (!array_key_exists($requestKey, $this->subRequests)) {
            $includes = [];
            foreach ($this->includes as $include) {
                if (str_contains($include, '.') && str_starts_with($include, $relationship . '.')) {
                    $includes[] = explode('.', $include, 2)[1];
                }
            }

            $queryFields = [];
            foreach ($this->fields as $type => $fields) {
                $queryFields[$type] = implode(',', $fields);
            }

            $type = $resource ? $resource->type() : $this->type();
            $id = $resource ? $resource->id() : $this->id();
            $relationshipPart = '/' . $relationship;
            if (!$this->requestsInclude($relationship)) {
                $relationshipPart = '/relationships' . $relationshipPart;
            }

            $subRequest = new self(
                $this->method(),
                $this->uri()
                    ->withPath(
                        ($this->fileInPath ? '/' . $this->fileInPath : '') .
                        ($this->apiPrefix ? '/' . $this->apiPrefix : '') .
                        '/' . $type . '/' . $id . $relationshipPart
                    )
                    ->withQuery(
                        http_build_query([
                            'fields' => $queryFields,
                            'filter' => $keepFilters ? $this->filter : [],
                            'include' => implode(',', $includes)
                        ])
                    ),
                null,
                $this->apiPrefix
            );
            $subRequest->headers = $this->headers;

            $this->subRequests[$requestKey] = $subRequest;
        }

        return $this->subRequests[$requestKey];
    }

    private function updateUriQuery(): void
    {
        $sort = [];
        foreach ($this->sorting->all() as $field => $direction) {
            if ($direction === self::ORDER_ASC) {
                $sort[] = $field;
            } elseif ($direction === self::ORDER_DESC) {
                $sort[] = '-' . $field;
            }
        }

        $fields = [];
        foreach ($this->fields as $type => $typedFields) {
            $fields[$type] = implode(',', $typedFields);
        }

        $query = [
            'sort' => implode(',', $sort),
            'page' => $this->pagination->all(),
            'filter' => $this->filter->all(),
            'include' => implode(',', $this->includes),
            'fields' => $fields
        ] + $this->customQueryParameters->all();

        // Remove empty query params.
        $query = array_filter($query);
        $this->uri = $this->uri->withQuery(http_build_query($query));
    }
}
