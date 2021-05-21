<?php
namespace Dogado\JsonApi\Tests\Model\Resource\Relationship;

use Dogado\JsonApi\Model\Resource\Link\Link;
use Dogado\JsonApi\Model\Resource\Link\LinkInterface;
use Dogado\JsonApi\Model\Resource\Relationship\Relationship;
use Dogado\JsonApi\Model\Resource\ResourceInterface;
use Dogado\JsonApi\Support\Resource\ResourceCollection;
use Dogado\JsonApi\Tests\TestCase;
use InvalidArgumentException;
use LogicException;

class RelationshipTest extends TestCase
{
    public function testToOne(): void
    {
        $relation = new Relationship(
            'test', $this->createMock(ResourceInterface::class)
        );
        /** @var LinkInterface $link */
        $link = $this->createMock(LinkInterface::class);
        $relation->links()->set($link);
        $relation->metaInformation()->set('test', 1);

        self::assertEquals(1, $relation->related()->count());
        self::assertEquals('test', $relation->name());
        self::assertEquals(1, $relation->links()->count());
        self::assertEquals(1, $relation->metaInformation()->count());
    }

    public function testEmptyToOne(): void
    {
        $relation = new Relationship('test');
        self::assertEquals(0, $relation->related()->count());
    }

    public function testToMany(): void
    {
        $relation = new Relationship(
            'test',
            [
                $this->createMock(ResourceInterface::class),
            ]
        );
        self::assertEquals(1, $relation->related()->count());
    }

    public function testEmptyToMany(): void
    {
        $relation = new Relationship('test');
        self::assertEquals(0, $relation->related()->count());
    }

    public function testInvalidNamedRelationship(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Relationship('');
    }

    public function testImmutableToOne(): void
    {
        $this->expectException(LogicException::class);
        $relation = new Relationship('test', $this->createMock(ResourceInterface::class));
        /** @var ResourceInterface $resource */
        $resource = $this->createConfiguredMock(
            ResourceInterface::class,
            ['type' => 'test', 'id' => '1']
        );
        $relation->related()->set($resource);
    }

    public function testDuplicateToOne(): void
    {
        $relation = new Relationship(
            'test',
            $this->createConfiguredMock(
                ResourceInterface::class,
                ['type' => 'test', 'id' => '1']
            )
        );
        $relation->links()->set(new Link('test', 'http://example.com'));

        self::assertNotSame($relation, $relation->duplicate());
        self::assertNotSame($relation->related(), $relation->duplicate()->related());
        self::assertNotSame($relation->related()->first(), $relation->duplicate()->related()->first());
        self::assertNotSame($relation->links(), $relation->duplicate()->links());
        self::assertNotSame($relation->links()->get('test'), $relation->duplicate()->links()->get('test'));
    }

    public function testDuplicateToMany(): void
    {
        $relation = new Relationship(
            'test',
            [
                $this->createConfiguredMock(
                    ResourceInterface::class,
                    ['type' => 'test', 'id' => '1']
                )
            ]
        );
        $relation->links()->set(new Link('test', 'http://example.com'));

        self::assertNotSame($relation, $relation->duplicate());
        self::assertNotSame($relation->related(), $relation->duplicate()->related());
        self::assertNotSame($relation->related()->first(), $relation->duplicate()->related()->first());
        self::assertNotSame($relation->links(), $relation->duplicate()->links());
        self::assertNotSame($relation->links()->get('test'), $relation->duplicate()->links()->get('test'));
    }

    public function testWithResourceCollection(): void
    {
        $collection = new ResourceCollection();

        $relation = new Relationship('test', $collection);

        self::assertSame($collection, $relation->related());
    }

    public function testInvalidRelationshipData(): void
    {
        $this->expectException(InvalidArgumentException::class);
        /** @phpstan-ignore-next-line */
        new Relationship('test', 'test');
    }
}
