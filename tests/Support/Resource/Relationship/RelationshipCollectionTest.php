<?php
namespace Dogado\JsonApi\Tests\Support\Resource\Relationship;

use Dogado\JsonApi\Model\Resource\Relationship\RelationshipInterface;
use Dogado\JsonApi\Support\Resource\Relationship\RelationshipCollection;
use Dogado\JsonApi\Tests\TestCase;
use InvalidArgumentException;

class RelationshipCollectionTest extends TestCase
{
    public function testHas(): void
    {
        $collection = new RelationshipCollection($this->getResources());
        self::assertTrue($collection->has('a'));
        self::assertTrue($collection->has('b'));
        self::assertFalse($collection->has('test'));
    }

    public function testGet(): void
    {
        $collection = new RelationshipCollection($this->getResources());
        /** @noinspection UnnecessaryAssertionInspection */
        self::assertInstanceOf(
            RelationshipInterface::class,
            $collection->get('a')
        );
    }

    public function testGetInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $collection = new RelationshipCollection($this->getResources());
        $collection->get('test');
    }

    public function testSet(): void
    {
        $collection = new RelationshipCollection($this->getResources());
        self::assertFalse($collection->has('test'));
        /** @var RelationshipInterface $relationship */
        $relationship = $this->createConfiguredMock(
            RelationshipInterface::class,
            ['name' => 'test']
        );
        $collection->set(
            $relationship
        );

        self::assertTrue($collection->has('test'));
    }

    public function testRemove(): void
    {
        $collection = new RelationshipCollection($this->getResources());
        self::assertTrue($collection->has('a'));

        $collection->remove('a');

        self::assertFalse($collection->has('a'));
    }


    public function testRemoveElement(): void
    {
        $collection = new RelationshipCollection($this->getResources());
        self::assertTrue($collection->has('a'));
        /** @var RelationshipInterface $relationship */
        $relationship = $this->createConfiguredMock(
            RelationshipInterface::class,
            ['name' => 'a']
        );
        $collection->removeElement($relationship);

        self::assertFalse($collection->has('a'));
    }

    /**
     * @return RelationshipInterface[]
     */
    private function getResources(): array
    {
        return [
            $this->createConfiguredMock(
                RelationshipInterface::class,
                ['name' => 'a']
            ),
            $this->createConfiguredMock(
                RelationshipInterface::class,
                ['name' => 'b']
            ),
        ];
    }
}
