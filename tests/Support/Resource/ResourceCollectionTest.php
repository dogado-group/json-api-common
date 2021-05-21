<?php
namespace Dogado\JsonApi\Tests\Support\Resource;

use Dogado\JsonApi\Exception\JsonApi\ResourceNotFoundException;
use Dogado\JsonApi\Model\Resource\ResourceInterface;
use Dogado\JsonApi\Support\Resource\ResourceCollection;
use Dogado\JsonApi\Tests\TestCase;
use LogicException;

class ResourceCollectionTest extends TestCase
{
    public function testHas(): void
    {
        $collection = new ResourceCollection($this->getResources());
        self::assertTrue($collection->has('test', '1'));
        self::assertTrue($collection->has('test', '2'));
        self::assertFalse($collection->has('test', '3'));
    }

    public function testGet(): void
    {
        $collection = new ResourceCollection($this->getResources());
        try {
            self::assertInstanceOf(
                ResourceInterface::class,
                $collection->get('test', '1')
            );
        } catch (ResourceNotFoundException $e) {
            $this->fail($e->getMessage());
        }
    }

    public function testFirst(): void
    {
        $collection = new ResourceCollection($this->getResources());
        self::assertInstanceOf(
            ResourceInterface::class,
            $collection->first('test')
        );
    }

    public function testFirstEmptyCollection(): void
    {
        $this->expectException(LogicException::class);
        $collection = new ResourceCollection();
        self::assertInstanceOf(
            ResourceInterface::class,
            $collection->first('test')
        );
    }

    public function testFirstMissingType(): void
    {
        $this->expectException(LogicException::class);
        $collection = new ResourceCollection($this->getResources());
        self::assertInstanceOf(
            ResourceInterface::class,
            $collection->first('invalid')
        );
    }

    public function testGetInvalid(): void
    {
        $this->expectException(ResourceNotFoundException::class);
        $collection = new ResourceCollection($this->getResources());
        $collection->get('test', '3');
    }

    public function testSet(): void
    {
        $collection = new ResourceCollection($this->getResources());
        self::assertFalse($collection->has('test', '3'));
        /** @var ResourceInterface $resource */
        $resource = $this->createConfiguredMock(
            ResourceInterface::class,
            ['type' => 'test', 'id' => '3']
        );
        $collection->set($resource);

        self::assertTrue($collection->has('test', '3'));
    }

    public function testRemove(): void
    {
        $collection = new ResourceCollection($this->getResources());
        self::assertTrue($collection->has('test', '2'));

        $collection->remove('test', '2');

        self::assertFalse($collection->has('test', '2'));
    }


    public function testRemoveElement(): void
    {
        $collection = new ResourceCollection($this->getResources());
        self::assertTrue($collection->has('test', '2'));

        /** @var ResourceInterface $resource */
        $resource = $this->createConfiguredMock(
            ResourceInterface::class,
            ['type' => 'test', 'id' => '2']
        );

        $collection->removeElement($resource);

        self::assertFalse($collection->has('test', '2'));
    }

    public function testResourcesWithoutId(): void
    {
        $resource = $this->createConfiguredMock(
            ResourceInterface::class,
            ['type' => 'test']
        );

        $collection = new ResourceCollection();
        $collection->set($resource);

        $resourcesWithId = $this->getResources();
        foreach ($resourcesWithId as $resourceWithId) {
            $collection->set($resourceWithId);
        }
        self::assertEquals($resource, $collection->first());
        self::assertNull($collection->first()->id());

        $collection->merge($resource);
        self::assertEquals(count($resourcesWithId) + 1, $collection->count());

        $collection->removeElement($resource);
        self::assertEquals(count($resourcesWithId), $collection->count());
        self::assertNotNull($collection->first()->id());
    }

    /**
     * @return ResourceInterface[]
     */
    private function getResources(): array
    {
        return [
            $this->createConfiguredMock(
                ResourceInterface::class,
                ['type' => 'test', 'id' => '1']
            ),
            $this->createConfiguredMock(
                ResourceInterface::class,
                ['type' => 'test', 'id' => '2']
            ),
        ];
    }
}
