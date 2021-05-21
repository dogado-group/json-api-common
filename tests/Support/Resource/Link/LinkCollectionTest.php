<?php
namespace Dogado\JsonApi\Tests\Support\Resource\Link;

use Dogado\JsonApi\Model\Resource\Link\Link;
use Dogado\JsonApi\Model\Resource\Link\LinkInterface;
use Dogado\JsonApi\Support\Resource\Link\LinkCollection;
use Dogado\JsonApi\Tests\TestCase;
use InvalidArgumentException;

class LinkCollectionTest extends TestCase
{
    public function testHas(): void
    {
        $collection = new LinkCollection($this->getLinks());
        self::assertTrue($collection->has('a'));
    }

    public function testGet(): void
    {
        $links = $this->getLinks();
        $collection = new LinkCollection($links);
        /** @noinspection UnnecessaryAssertionInspection */
        self::assertInstanceOf(LinkInterface::class, $collection->get('a'));
        self::assertEquals($links, $collection->all());
    }

    public function testGetInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $collection = new LinkCollection($this->getLinks());
        $collection->get('c');
    }

    public function testSet(): void
    {
        $collection = new LinkCollection($this->getLinks());
        self::assertFalse($collection->has('c'));
        /** @var LinkInterface $link */
        $link = $this->createConfiguredMock(
            LinkInterface::class, ['name' => 'c']
        );
        $collection->set(
            $link
        );
        self::assertTrue($collection->has('c'));
    }

    public function testRemove(): void
    {
        $collection = new LinkCollection($this->getLinks());
        self::assertTrue($collection->has('a'));
        $collection->remove('a');
        self::assertFalse($collection->has('a'));
    }

    public function testRemoveElement(): void
    {
        $collection = new LinkCollection($this->getLinks());
        self::assertTrue($collection->has('a'));
        /** @var LinkInterface $link */
        $link = $this->createConfiguredMock(
            LinkInterface::class, ['name' => 'a']
        );
        $collection->removeElement($link);
        self::assertFalse($collection->has('a'));
    }

    public function testCreateLink(): void
    {
        $collection = new LinkCollection();
        $collection->createLink('test', 'http://example.com');

        self::assertEquals('http://example.com', $collection->get('test')->href());
    }

    public function testMergeNonExisting(): void
    {
        $link = new Link('test', '/test');
        self::assertEquals($link, (new LinkCollection())->merge($link)->get('test'));
    }

    public function testMergeExistingAndOverwrite(): void
    {
        $link = new Link('test', '/test');
        $link2 = new Link('test', '/new-test');
        self::assertEquals($link2, (new LinkCollection([$link]))->merge($link2, true)->get('test'));
    }

    public function testMergeExisting(): void
    {
        $link = new Link('test', '/test');
        $link2 = new Link('test', '/new-test');
        self::assertEquals($link, (new LinkCollection([$link]))->merge($link2, false)->get('test'));
    }

    /**
     * @return LinkInterface[]
     */
    private function getLinks(): array
    {
        return [
            $this->createConfiguredMock(LinkInterface::class, ['name' => 'a']),
            $this->createConfiguredMock(LinkInterface::class, ['name' => 'b']),
        ];
    }
}
