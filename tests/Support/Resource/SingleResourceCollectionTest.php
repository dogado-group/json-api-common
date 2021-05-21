<?php
namespace Dogado\JsonApi\Tests\Support\Resource;

use Dogado\JsonApi\Model\Resource\ResourceInterface;
use Dogado\JsonApi\Support\Resource\SingleResourceCollection;
use Dogado\JsonApi\Tests\TestCase;
use LogicException;

class SingleResourceCollectionTest extends TestCase
{
    public function testSet(): void
    {
        $collection = new SingleResourceCollection();

        self::assertCount(0, $collection);

        /** @var ResourceInterface $resource */
        $resource = $this->createMock(ResourceInterface::class);
        $collection->set($resource);

        self::assertCount(1, $collection);
    }

    public function testConstructMultiple(): void
    {
        $this->expectException(LogicException::class);
        new SingleResourceCollection(
            [
                $this->createMock(ResourceInterface::class),
                $this->createMock(ResourceInterface::class)
            ]
        );
    }

    public function testSetMultiple(): void
    {
        $this->expectException(LogicException::class);
        $collection = new SingleResourceCollection();
        /** @var ResourceInterface $resource */
        $resource = $this->createMock(ResourceInterface::class);
        $collection->set($resource);
        $collection->set($resource);
    }
}
