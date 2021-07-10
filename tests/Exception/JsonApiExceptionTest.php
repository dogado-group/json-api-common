<?php
namespace Dogado\JsonApi\Tests\Exception;

use Dogado\JsonApi\Exception\JsonApi\BadRequestException;
use Dogado\JsonApi\Exception\JsonApi\NotAllowedException;
use Dogado\JsonApi\Exception\JsonApi\ResourceNotFoundException;
use Dogado\JsonApi\Exception\JsonApi\UnsupportedMediaTypeException;
use Dogado\JsonApi\Exception\JsonApi\ValidationException;
use Dogado\JsonApi\Exception\JsonApiException;
use Dogado\JsonApi\Model\Error\ErrorInterface;
use Dogado\JsonApi\Model\JsonApiInterface;
use Dogado\JsonApi\Tests\TestCase;
use Generator;

class JsonApiExceptionTest extends TestCase
{
    /**
     * @dataProvider provideScenarios
     */
    public function test(JsonApiException $exception, string $expectedMessage, int $expectedHttpStatus): void
    {
        $this->assertEquals($expectedMessage, $exception->getMessage());
        assert($exception->errors()->first() instanceof ErrorInterface);
        $this->assertEquals($expectedMessage, $exception->errors()->first()->title());
        $this->assertEquals($expectedHttpStatus, $exception->getHttpStatus());
    }

    public function provideScenarios(): Generator
    {
        $message = $this->faker()->text();
        $httpStatus = $this->faker()->numberBetween(400, 599);
        yield [(new JsonApiException($message))->setHttpStatus($httpStatus), $message, $httpStatus];

        $message = $this->faker()->text();
        yield [new BadRequestException($message), $message, 400];

        $message = $this->faker()->text();
        yield [new NotAllowedException($message), $message, 403];

        $type = $this->faker()->slug();
        $id = (string) $this->faker()->numberBetween();
        $message = 'Resource "' . $id . '" of type "' . $type . '" not found!';
        yield [new ResourceNotFoundException($type, $id), $message, 404];

        $contentType = $this->faker()->slug();
        $message = sprintf(
            'Invalid content type "%s" given, "%s" expected',
            $contentType,
            JsonApiInterface::CONTENT_TYPE
        );
        yield [new UnsupportedMediaTypeException($contentType), $message, 415];

        $message = $this->faker()->text();
        yield [new ValidationException($message), $message, 422];
    }
}