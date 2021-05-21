<?php
namespace Dogado\JsonApi\Tests\Support\Resource;

use Dogado\JsonApi\Model\Resource\ResourceInterface;
use Dogado\JsonApi\Support\Resource\ImmutableResourceCollection;
use Dogado\JsonApi\Tests\TestCase;
use LogicException;

class ImmutableResourceCollectionTest extends TestCase
{
    public function testSet(): void
    {
        $this->expectException(LogicException::class);
        $collection = new ImmutableResourceCollection();
        /** @var ResourceInterface $resource */
        $resource = $this->createMock(ResourceInterface::class);
        $collection->set($resource);
    }

    public function testRemove(): void
    {
        $this->expectException(LogicException::class);
        $collection = new ImmutableResourceCollection();
        $collection->remove('test', '1');
    }

    public function testRemoveElement(): void
    {
        $this->expectException(LogicException::class);
        $collection = new ImmutableResourceCollection();
        /** @var ResourceInterface $resource */
        $resource = $this->createMock(ResourceInterface::class);
        $collection->removeElement($resource);
    }
}
