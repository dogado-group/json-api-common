<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Tests\Model\Request;

use Dogado\JsonApi\Exception\JsonApi\BadRequestException;
use Dogado\JsonApi\Model\Document\Document;
use Dogado\JsonApi\Model\JsonApiInterface;
use Dogado\JsonApi\Model\Request\Request;
use Dogado\JsonApi\Model\Request\RequestInterface;
use Dogado\JsonApi\Tests\TestCase;
use Exception;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\RequestInterface as PsrRequestInterface;
use Psr\Http\Message\UriInterface;

class RequestTest extends TestCase
{
    public function testRequest(): void
    {
        $allowedMethods = ['GET', 'POST', 'PATCH', 'DELETE'];
        $method = $allowedMethods[array_rand($allowedMethods)];
        /** @var Document $requestBody */
        $requestBody = $this->getMockBuilder(Document::class)->disableOriginalConstructor()->getMock();
        try {
            $request = new Request(
                $method,
                new Uri(
                    '/index.php/api/examples/example-1?include=tests,tests.user&fields[user]=username,birthday&' .
                    'page[offset]=0&page[limit]=10&sort=-createdAt&filter[test]=test,test2'
                ),
                $requestBody,
                'api'
            );
        } catch (Exception $e) {
            $this->fail($e->getMessage() . ' (' . $e->getFile() . ', ' . $e->getLine() . ')');
            return;
        }

        self::assertEquals($method, $request->method());
        self::assertInstanceOf(UriInterface::class, $request->uri());
        self::assertEquals($requestBody, $request->document());
        self::assertEquals('examples', $request->type());
        self::assertEquals('example-1', $request->id());
        self::assertTrue($request->filter()->has('test'));
        self::assertFalse($request->filter()->has('test2'));
        self::assertNull($request->relationship());
        self::assertTrue($request->requestsAttributes());
        self::assertTrue($request->requestsMetaInformation());
        self::assertTrue($request->requestsRelationships());
        self::assertTrue($request->requestsInclude('tests'));
        self::assertTrue($request->requestsInclude('tests.user'));
        self::assertFalse($request->requestsInclude('examples'));
        self::assertTrue($request->requestsField('examples', 'test'));
        self::assertTrue($request->requestsField('user', 'username'));
        self::assertTrue($request->requestsField('user', 'birthday'));
        self::assertFalse($request->requestsField('user', 'password'));
        self::assertEquals('test,test2', $request->filter()->get('test'));
        self::assertEquals(['createdAt' => RequestInterface::ORDER_DESC], $request->sorting()->all());
        self::assertEquals(RequestInterface::ORDER_DESC, $request->sorting()->get('createdAt'));
        self::assertTrue($request->pagination()->has('offset'));
        self::assertFalse($request->pagination()->has('offset2'));
        self::assertEquals('0', $request->pagination()->get('offset'));
        self::assertEquals('10', $request->pagination()->get('limit'));
        self::assertEquals(JsonApiInterface::CONTENT_TYPE, $request->headers()->getRequired('Content-Type'));
    }

    public function testCreateFromHttpRequest(): void
    {
        $faker = $this->faker;
        $requestInterface = $this->getMockBuilder(PsrRequestInterface::class)->getMock();

        $uri = $this->getMockBuilder(UriInterface::class)->getMock();
        $uri->expects(self::once())->method('getPath')->willReturn('/index.php/api/example');
        $uri->expects(self::once())->method('getQuery')->willReturn('');
        $uri->expects(self::once())->method('withQuery')->willReturnSelf();

        /** @var Document $requestBody */
        $requestBody = $this->getMockBuilder(Document::class)->disableOriginalConstructor()->getMock();
        $method = 'POST';

        $requestInterface->expects(self::once())->method('getMethod')->willReturn($method);
        $requestInterface->expects(self::once())->method('getUri')->willReturn($uri);
        $headers = [
            'Content-Type' => [JsonApiInterface::CONTENT_TYPE],
            $faker->word => [$faker->userName],
        ];
        $requestInterface->expects(self::once())->method('getHeaders')->willReturn($headers);

        /** @var RequestInterface $requestInterface */
        $request = Request::createFromHttpRequest($requestInterface, $requestBody, 'api');
        $this->assertEquals($method, $request->method());
        $this->assertEquals($uri, $request->uri());
        $this->assertEquals($requestBody, $request->document());
    }

    public function testRequestInvalidType(): void
    {
        $this->expectException(BadRequestException::class);
        new Request(
            'GET',
            new Uri('/index.php/api'),
            null,
            'api'
        );
    }

    public function testInvalidHttpMethod(): void
    {
        $this->expectException(BadRequestException::class);
        new Request(
            'no HTTP status',
            new Uri('/index.php/api')
        );
    }

    public function testInvalidRelationshipKeyword(): void
    {
        $this->expectException(BadRequestException::class);
        new Request(
            'GET',
            new Uri('/index.php/api/example/1/noRelationshipKeyword/exampleRelationship'),
            null,
            'api'
        );
    }

    public function testRelationshipDetailRequest(): void
    {
        $request = new Request(
            'GET',
            new Uri('/index.php/api/example/1/relationships/exampleRelationshipDetail'),
            null,
            'api'
        );
        $this->assertEquals('exampleRelationshipDetail', $request->relationship());
    }

    public function testRelationshipRequest(): void
    {
        $request = new Request(
            'GET',
            new Uri('/index.php/api/example/1/exampleRelationship'),
            null,
            'api'
        );
        $this->assertEquals('exampleRelationship', $request->relationship());
    }

    public function testInvalidIncludeDatatype(): void
    {
        $this->expectException(BadRequestException::class);
        new Request(
            'GET',
            new Uri('/index.php/api/examples/example-1?include[]=test'),
            null,
            'api'
        );
    }

    public function testInvalidFieldsDatatype(): void
    {
        $this->expectException(BadRequestException::class);
        new Request(
            'GET',
            new Uri('/index.php/api/examples/example-1?fields=test'),
            null,
            'api'
        );
    }

    public function testNotSupportedFilterString(): void
    {
        $this->expectException(BadRequestException::class);
        new Request(
            'GET',
            new Uri('/index.php/api/examples/example-1?filter=notSupported'),
            null,
            'api'
        );
    }

    public function testJsonAsFilterString(): void
    {
        $faker = $this->faker;
        $filterKey = $faker->word;
        $filter = [
            $filterKey => $faker->name,
        ];
        $request = new Request(
            'GET',
            new Uri('/index.php/api/examples/example-1?filter=' . json_encode($filter)),
            null,
            'api'
        );
        $this->assertEquals($filter[$filterKey], $request->filter()->get($filterKey));
    }

    public function testInvalidPaginationDatatype(): void
    {
        $this->expectException(BadRequestException::class);
        new Request(
            'GET',
            new Uri('/index.php/api/examples/example-1?page=invalid'),
            null,
            'api'
        );
    }

    public function testInvalidSortingDatatype(): void
    {
        $this->expectException(BadRequestException::class);
        new Request(
            'GET',
            new Uri('/index.php/api/examples/example-1?sort[]=invalid'),
            null,
            'api'
        );
    }

    public function testPersistingOfRuntimeChanges(): void
    {
        $requestBody = $this->getMockBuilder(Document::class)->disableOriginalConstructor()->getMock();
        $request = new Request(
            'get',
            new Uri(
                '/index.php/api/examples/example-1?include=tests,tests.user&fields[user]=username,birthday&' .
                'page[offset]=0&page[limit]=10&sort=-createdAt&filter[test]=test,test2'
            ),
            $requestBody,
            'api'
        );

        $filterKey = $this->faker()->slug;
        $filterValue = $this->faker()->word;
        $request->filter()->set($filterKey, $filterValue);

        $paginationKey = $this->faker()->slug;
        $paginationValue = $this->faker->numberBetween();
        $request->pagination()->set($paginationKey, $paginationValue);

        $includes = array_unique($this->faker->words());
        foreach ($includes as $include) {
            self::assertFalse($request->requestsInclude($include));
            $request->include($include);
            self::assertTrue($request->requestsInclude($include));
        }

        $sortField = $this->faker->word;
        $sortDirection = $this->faker()->randomElement([RequestInterface::ORDER_ASC, RequestInterface::ORDER_DESC]);
        $request->sorting()->set($sortField, $sortDirection);

        $fieldType = $this->faker()->word;
        $fieldName = $this->faker()->word;
        self::assertTrue($request->requestsField($fieldType, $fieldName));
        $request->field($fieldType, $fieldName);
        self::assertTrue($request->requestsField($fieldType, $fieldName));
        self::assertFalse($request->requestsField($fieldType, $this->faker()->word));

        $uri = $request->uri();
        $queryParameters = [];
        parse_str($uri->getQuery(), $queryParameters);

        self::assertArrayHasKey('filter', $queryParameters);
        self::assertArrayHasKey($filterKey, $queryParameters['filter']);
        self::assertEquals($filterValue, $queryParameters['filter'][$filterKey]);

        self::assertArrayHasKey('page', $queryParameters);
        self::assertArrayHasKey($paginationKey, $queryParameters['page']);
        self::assertEquals($paginationValue, $queryParameters['page'][$paginationKey]);

        foreach ($includes as $include) {
            self::assertStringContainsString($include, $queryParameters['include']);
        }

        self::assertArrayHasKey('sort', $queryParameters);
        if (RequestInterface::ORDER_DESC === $sortDirection) {
            self::assertStringContainsString("-$sortField", $queryParameters['sort']);
        } else {
            self::assertStringContainsString($sortField, $queryParameters['sort']);
        }

        self::assertArrayHasKey('fields', $queryParameters);
        self::assertArrayHasKey($fieldType, $queryParameters['fields']);
        self::assertStringContainsString($fieldName, $queryParameters['fields'][$fieldType]);
    }
}
