<?php
namespace Dogado\JsonApi\Tests\Model\Resource\Link;

use Dogado\JsonApi\Model\Resource\Link\Link;
use Dogado\JsonApi\Tests\TestCase;
use InvalidArgumentException;

class LinkTest extends TestCase
{
    public function testLink(): void
    {
        $link = new Link('about', 'http://jsonapi.org');
        $link->metaInformation()->set('test', 'test');

        self::assertEquals('about', $link->name());
        self::assertEquals('http://jsonapi.org', $link->href());
        self::assertArrayHasKey('test', $link->metaInformation()->all());
    }

    public function testDuplicateLink(): void
    {
        $link = new Link('about', 'http://jsonapi.org');
        $link->metaInformation()->set('test', 'test');

        self::assertNotSame($link, $link->duplicate());
        self::assertNotSame($link->metaInformation(), $link->duplicate()->metaInformation());
        self::assertNotSame($link->duplicate(), $link->duplicate());
        self::assertEquals('test', $link->duplicate('test')->name());
    }

    public function testExceptionOnInvalidName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Link('', 'http://jsonapi.org');
    }

    public function testExceptionOnUrlEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Link('about', '');
    }

    public function testExceptionOnInvalidUrl(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Link('about', 'jsonapi.org');
    }

    public function testRelativeLink(): void
    {
        $link = new Link('about', '/resource');
        $link->metaInformation()->set('test', 'test');

        self::assertEquals('about', $link->name());
        self::assertEquals('/resource', $link->href());
        self::assertArrayHasKey('test', $link->metaInformation()->all());
    }
}
