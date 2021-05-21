<?php
namespace Dogado\JsonApi\Tests\Model\Document;

use Dogado\JsonApi\Model\Document\Document;
use Dogado\JsonApi\Model\Resource\ResourceInterface;
use Dogado\JsonApi\Support\Resource\ResourceCollectionInterface;
use Dogado\JsonApi\Tests\TestCase;
use InvalidArgumentException;

class DocumentTest extends TestCase
{
    public function testResourceDocument(): void
    {
        $document = new Document($this->createMock(ResourceInterface::class));
        self::assertEquals(1, $document->data()->count());
        self::assertFalse($document->shouldBeHandledAsCollection());
    }

    public function testEmptyResourceDocument(): void
    {
        $document = new Document();
        self::assertEquals(0, $document->data()->count());
        self::assertFalse($document->shouldBeHandledAsCollection());
    }

    public function testCollectionDocument(): void
    {
        $document = new Document([$this->createMock(ResourceInterface::class)]);
        self::assertEquals(1, $document->data()->count());
        self::assertTrue($document->shouldBeHandledAsCollection());
    }

    public function testEmptyCollectionDocument(): void
    {
        $document = new Document([]);
        self::assertEquals(0, $document->data()->count());
        self::assertTrue($document->shouldBeHandledAsCollection());
    }

    public function testResourceCollectionDocument(): void
    {
        $document = new Document($this->createMock(ResourceCollectionInterface::class));
        self::assertEquals(0, $document->data()->count());
        self::assertTrue($document->shouldBeHandledAsCollection());
    }

    public function testEmptyErrorDocument(): void
    {
        $document = new Document();
        self::assertEquals(0, $document->errors()->count());
        self::assertFalse($document->shouldBeHandledAsCollection());
    }

    public function testInvalidDocumentData(): void
    {
        $this->expectException(InvalidArgumentException::class);
        /** @phpstan-ignore-next-line */
        new Document(1);
    }
}
