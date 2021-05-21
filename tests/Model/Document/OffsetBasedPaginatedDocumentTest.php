<?php
namespace Dogado\JsonApi\Tests\Model\Document;

use Dogado\JsonApi\Model\Document\OffsetBasedPaginatedDocument;
use Dogado\JsonApi\Model\Resource\ResourceInterface;
use Dogado\JsonApi\Tests\TestCase;
use Psr\Http\Message\UriInterface;

class OffsetBasedPaginatedDocumentTest extends TestCase
{
    /** @dataProvider provideTestScenarios */
    public function testInstance(int $offset): void
    {
        $resultCount = rand(22, 5999);
        $defaultLimit = 10;
        $uri = $this->createMock(UriInterface::class);
        $uri->expects(self::once())->method('__toString')->willReturn('/api/example/1');
        $uri->expects(self::atLeastOnce())->method('getQuery')
            ->willReturn('page[offset]='.$offset.'&page[limit]=10');
        $uri->expects(self::atLeastOnce())->method('withQuery')->willReturn('/api/example/1?test=1');
        $document = new OffsetBasedPaginatedDocument(
            $this->createMock(ResourceInterface::class),
            $uri,
            $resultCount,
            $defaultLimit
        );
        self::assertTrue($document->links()->has('self'));
        self::assertEquals(0 === $offset ? false : true, $document->links()->has('previous'));
        self::assertTrue($document->links()->has('next'));
        self::assertTrue($document->links()->has('last'));
    }

    public function provideTestScenarios(): array
    {
        return [
            [0],
            [10],
            [20],
        ];
    }
}