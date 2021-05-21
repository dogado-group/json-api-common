<?php

namespace Dogado\JsonApi\Tests\Factory;

use Dogado\JsonApi\Factory\ResponseFactory;
use Dogado\JsonApi\Model\Document\DocumentInterface;
use Dogado\JsonApi\Model\Response\AcceptedResponse;
use Dogado\JsonApi\Model\Response\CreatedResponse;
use Dogado\JsonApi\Model\Response\DocumentResponse;
use Dogado\JsonApi\Model\Response\EmptyResponse;
use Dogado\JsonApi\Support\Collection\KeyValueCollectionInterface;
use Dogado\JsonApi\Tests\TestCase;

class ResponseFactoryTest extends TestCase
{
    private ResponseFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new ResponseFactory();
    }

    public function testAccepted(): void
    {
        $headers = $this->createMock(KeyValueCollectionInterface::class);
        $this->assertEquals(new AcceptedResponse($headers), $this->factory->accepted($headers));
    }

    public function testCreated(): void
    {
        $location = $this->faker()->url;
        $headers = $this->createMock(KeyValueCollectionInterface::class);
        $document = $this->createMock(DocumentInterface::class);
        $this->assertEquals(
            new CreatedResponse($location, $headers, $document),
            $this->factory->created($location, $headers, $document)
        );
    }

    public function testDocument(): void
    {
        $headers = $this->createMock(KeyValueCollectionInterface::class);
        $document = $this->createMock(DocumentInterface::class);
        $status = $this->faker()->numberBetween(200, 599);
        $this->assertEquals(
            new DocumentResponse($document, $headers, $status),
            $this->factory->document($document, $headers, $status)
        );
    }

    public function testEmpty(): void
    {
        $headers = $this->createMock(KeyValueCollectionInterface::class);
        $this->assertEquals(
            new EmptyResponse($headers),
            $this->factory->empty($headers)
        );
    }
}
