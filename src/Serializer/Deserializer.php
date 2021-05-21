<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Serializer;

use Dogado\JsonApi\JsonApiTrait;
use Dogado\JsonApi\Model\Document\DocumentInterface;
use Dogado\JsonApi\Model\Error\Error;
use Dogado\JsonApi\Model\Error\ErrorInterface;
use Dogado\JsonApi\Model\Resource\ResourceInterface;
use Dogado\JsonApi\Support\Resource\Link\LinkCollectionInterface;
use Dogado\JsonApi\Support\Resource\ResourceCollectionInterface;
use InvalidArgumentException;
use RuntimeException;

class Deserializer implements DocumentDeserializerInterface
{
    use JsonApiTrait;

    /**
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function deserializeDocument(array $documentData): DocumentInterface
    {
        $data = $documentData['data'] ?? null;

        if (!is_array($data) || $this->isSingleResource($data)) {
            $document = $this->singleResourceDocument();
        } else {
            $document = $this->multiResourceDocument();
        }

        if (is_array($data)) {
            if ($this->isSingleResource($data)) {
                $this->buildResource($document->data(), $data);
            } else {
                foreach ($data as $resource) {
                    $this->buildResource($document->data(), $resource);
                }
            }
        }

        $errors = array_key_exists('errors', $documentData) ? (array)$documentData['errors'] : [];
        foreach ($errors as $error) {
            $document->errors()->add($this->buildError($error));
        }

        if (array_key_exists('meta', $documentData)) {
            $document->metaInformation()->merge((array)$documentData['meta']);
        }

        $links = array_key_exists('links', $documentData) ? (array)$documentData['links'] : [];
        foreach ($links as $name => $link) {
            $this->buildLink($document->links(), $name, is_array($link) ? $link : ['href' => $link]);
        }

        $included = array_key_exists('included', $documentData) ? (array)$documentData['included'] : [];
        foreach ($included as $related) {
            $this->buildResource($document->included(), $related);
        }

        return $document;
    }

    /**
     * @throws InvalidArgumentException|RuntimeException
     */
    protected function buildResource(ResourceCollectionInterface $collection, array $resourceData): ResourceInterface
    {
        if (!array_key_exists('type', $resourceData)) {
            throw new InvalidArgumentException('Invalid resource given!');
        }

        $type = (string)$resourceData['type'];
        $id = array_key_exists('id', $resourceData) ? (string)$resourceData['id'] : null;
        $resource = $this->resource($type, $id);
        $collection->set($resource);

        if (array_key_exists('attributes', $resourceData)) {
            $resource->attributes()->merge((array)$resourceData['attributes']);
        }

        $relationships = array_key_exists('relationships', $resourceData) ? (array)$resourceData['relationships'] : [];
        $this->buildResourceRelationships($relationships, $resource);

        $links = array_key_exists('links', $resourceData) ? (array)$resourceData['links'] : [];
        foreach ($links as $name => $link) {
            $this->buildLink($resource->links(), $name, is_array($link) ? $link : ['href' => $link]);
        }

        if (array_key_exists('meta', $resourceData)) {
            $resource->metaInformation()->merge((array)$resourceData['meta']);
        }

        return $resource;
    }

    protected function buildError(array $data): ErrorInterface
    {
        $error = new Error(
            array_key_exists('status', $data) ? (int)$data['status'] : 0,
            array_key_exists('title', $data) ? (string)$data['title'] : '',
            array_key_exists('detail', $data) ? (string)$data['detail'] : '',
            array_key_exists('code', $data) ? (string)$data['code'] : ''
        );

        if (array_key_exists('meta', $data)) {
            $error->metaInformation()->merge((array)$data['meta']);
        }

        if (array_key_exists('source', $data)) {
            $error->source()->merge((array)$data['source']);
        }

        return $error;
    }

    /**
     * @throws InvalidArgumentException
     */
    protected function buildLink(LinkCollectionInterface $collection, string $name, array $data): void
    {
        if (!array_key_exists('href', $data)) {
            throw new InvalidArgumentException('Invalid link given!');
        }

        if (!$data['href']) {
            return;
        }

        $collection->createLink($name, (string)$data['href']);
        if (array_key_exists('meta', $data)) {
            $collection->get($name)->metaInformation()->merge((array)$data['meta']);
        }
    }

    /**
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    protected function buildResourceRelationships(array $relationships, ResourceInterface $resource): void
    {
        foreach ($relationships as $name => $relationship) {
            $related = $relationship['data'] ?? null;

            if (!is_array($related)) {
                // empty to one relationship
                $relationshipObject = $this->toOneRelationship($name);
            } elseif (count($related) > 0 && array_keys($related) !== range(0, count($related) - 1)) {
                // to one relationship
                $relationshipObject = $this->toOneRelationship($name);
                $this->buildResource($relationshipObject->related(), $related);
            } else {
                // to many relationship
                $relationshipObject = $this->toManyRelationship($name);
                foreach ($related as $relatedResource) {
                    $this->buildResource($relationshipObject->related(), $relatedResource);
                }
            }

            $links = array_key_exists('links', $relationship) ? (array)$relationship['links'] : [];
            foreach ($links as $linkName => $link) {
                $this->buildLink(
                    $relationshipObject->links(),
                    $linkName,
                    is_array($link) ? $link : ['href' => $link]
                );
            }

            if (array_key_exists('meta', $relationship)) {
                $relationshipObject->metaInformation()->merge((array)$relationship['meta']);
            }

            $resource->relationships()->set($relationshipObject);
        }
    }

    protected function isSingleResource(array $data): bool
    {
        return count($data) > 0 && array_keys($data) !== range(0, count($data) - 1);
    }
}
