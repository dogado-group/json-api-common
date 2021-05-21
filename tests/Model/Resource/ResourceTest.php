<?php
namespace Dogado\JsonApi\Tests\Model\Resource;

use Dogado\JsonApi\Model\Resource\Link\Link;
use Dogado\JsonApi\Model\Resource\Relationship\Relationship;
use Dogado\JsonApi\Model\Resource\Resource;
use Dogado\JsonApi\Tests\TestCase;
use InvalidArgumentException;

class ResourceTest extends TestCase
{
    public function testResource(): void
    {
        $resource = new Resource('test', '1', ['attr' => 'test']);
        self::assertEquals('test', $resource->attributes()->getRequired('attr'));
        self::assertEquals('test', $resource->type());
        self::assertEquals('1', $resource->id());
        self::assertEquals(0, $resource->relationships()->count());
        self::assertEquals(0, $resource->links()->count());
        self::assertEquals(0, $resource->metaInformation()->count());
    }

    public function testResourceWithoutId(): void
    {
        $resource = new Resource('test', null, ['attrNull' => 'testNull']);
        self::assertNull($resource->id());
        self::assertEquals('testNull', $resource->attributes()->getRequired('attrNull'));
        self::assertEquals('test', $resource->type());
    }

    public function testDuplicateResource(): void
    {
        $resource = new Resource('test', '1', ['attr' => 'test']);
        $resource->links()->set(new Link('test', 'http://test.de'));
        $resource->relationships()->set(new Relationship('test'));

        $duplicate = $resource->duplicate();


        self::assertNotSame($resource, $duplicate);
        self::assertNotSame($resource->attributes(), $duplicate->attributes());
        self::assertNotSame($resource->metaInformation(), $duplicate->metaInformation());
        self::assertNotSame($resource->links(), $duplicate->links());
        self::assertNotSame($resource->links()->get('test'), $duplicate->links()->get('test'));
        self::assertNotSame($resource->relationships(), $duplicate->relationships());
        self::assertNotSame($resource->relationships()->get('test'), $duplicate->relationships()->get('test'));
    }

    public function testResourceEmptyType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Resource('', '1');
    }
}
