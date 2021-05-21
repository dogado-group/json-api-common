<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Serializer;

use Dogado\JsonApi\Model\Document\DocumentInterface;
use Dogado\JsonApi\Model\Error\ErrorInterface;
use Dogado\JsonApi\Model\Resource\Link\LinkInterface;
use Dogado\JsonApi\Model\Resource\Relationship\RelationshipInterface;
use Dogado\JsonApi\Model\Resource\ResourceInterface;
use Dogado\JsonApi\Support\Resource\ResourceCollectionInterface;

class Serializer implements DocumentSerializerInterface
{
    private bool $keepEmptyData;

    public function __construct(bool $keepEmptyData = false)
    {
        $this->keepEmptyData = $keepEmptyData;
    }

    protected function shouldKeepEmptyData(): bool
    {
        return $this->keepEmptyData;
    }

    public function serializeDocument(DocumentInterface $document, bool $identifiersOnly = false): array
    {
        $result = [];

        if ($this->shouldContainData($document)) {
            $result['data'] = (!$document->shouldBeHandledAsCollection() && $document->data()->isEmpty()) ?
                null : $this->createData($document, $identifiersOnly);
        }

        if (!$document->metaInformation()->isEmpty()) {
            $result['meta'] = $document->metaInformation()->all();
        }

        if (!$document->links()->isEmpty()) {
            foreach ($document->links()->all() as $link) {
                $result['links'][$link->name()] = $this->serializeLink($link);
            }
        }

        if (!$document->included()->isEmpty()) {
            foreach ($document->included()->all() as $resource) {
                $result['included'][] = $this->serializeResource(
                    $resource,
                    false
                );
            }
        }

        if (!$document->errors()->isEmpty()) {
            foreach ($document->errors()->all() as $error) {
                $result['errors'][] = $this->serializeError($error);
            }
        }

        // information about json api
        $result['jsonapi'] = [
            'version' => '1.0'
        ];

        return $result;
    }

    protected function serializeResource(ResourceInterface $resource, bool $identifierOnly = true): array
    {
        $data = [
            'type' => $resource->type(),
        ];

        if ($resource->id() !== null) {
            $data['id'] = $resource->id();
        }

        if (!$resource->metaInformation()->isEmpty()) {
            $data['meta'] = $resource->metaInformation()->all();
        }

        if ($identifierOnly) {
            return $data;
        }

        if (!$resource->attributes()->isEmpty()) {
            $data['attributes'] = $resource->attributes()->all();
        }

        foreach ($resource->relationships()->all() as $relationship) {
            $data['relationships'][$relationship->name()] = $this->serializeRelationship($relationship);
        }

        foreach ($resource->links()->all() as $link) {
            $data['links'][$link->name()] = $this->serializeLink($link);
        }

        return $data;
    }

    protected function serializeRelationship(RelationshipInterface $relationship): array
    {
        $data = [];

        foreach ($relationship->links()->all() as $link) {
            $data['links'][$link->name()] = $this->serializeLink($link);
        }

        if (!$relationship->metaInformation()->isEmpty()) {
            $data['meta'] = $relationship->metaInformation()->all();
        }

        if (!$relationship->related()->isEmpty()) {
            if ($relationship->shouldBeHandledAsCollection()) {
                $data['data'] = $this->createCollectionData($relationship->related());
            } else {
                $data['data'] = $this->serializeResource($relationship->related()->first());
            }
        } elseif (count($data) === 0 || $this->shouldKeepEmptyData()) {
            // only add empty data if links or meta are not defined
            if ($relationship->shouldBeHandledAsCollection()) {
                $data['data'] = [];
            } else {
                $data['data'] = null;
            }
        }

        return $data;
    }

    /**
     * @return array|string
     */
    protected function serializeLink(LinkInterface $link)
    {
        if (!$link->metaInformation()->isEmpty()) {
            return [
                'href' => $link->href(),
                'meta' => $link->metaInformation()->all(),
            ];
        }

        return $link->href();
    }

    protected function serializeError(ErrorInterface $error): array
    {
        $data = [
            'status' => $error->status(),
        ];

        if ($error->code() !== '') {
            $data['code'] = $error->code();
        }
        if ($error->title() !== '') {
            $data['title'] = $error->title();
        }
        if ($error->detail() !== '') {
            $data['detail'] = $error->detail();
        }

        if (!$error->metaInformation()->isEmpty()) {
            $data['meta'] = $error->metaInformation()->all();
        }

        if (!$error->source()->isEmpty()) {
            $data['source'] = $error->source()->all();
        }

        return $data;
    }

    protected function createData(DocumentInterface $document, bool $identifiersOnly): array
    {
        if ($document->shouldBeHandledAsCollection()) {
            return $this->createCollectionData($document->data(), $identifiersOnly);
        }

        return $this->serializeResource($document->data()->first(), $identifiersOnly);
    }

    protected function createCollectionData(ResourceCollectionInterface $collection, bool $identifierOnly = true): array
    {
        $data = [];
        foreach ($collection->all() as $resource) {
            $data[] = $this->serializeResource($resource, $identifierOnly);
        }

        return $data;
    }

    protected function shouldContainData(DocumentInterface $document): bool
    {
        if (!$document->data()->isEmpty()) {
            return true;
        }

        return
            ($document->errors()->isEmpty() && $document->metaInformation()->isEmpty())
            ||
            $this->shouldKeepEmptyData();
    }
}
