<?php
namespace Dogado\JsonApi\Tests\Model\Error;

use Dogado\JsonApi\Model\Error\Error;
use Dogado\JsonApi\Tests\TestCase;
use Exception;

class ErrorTest extends TestCase
{
    public function testSimpleError(): void
    {
        $error = new Error(500, 'Test Error');
        self::assertEquals('Test Error', $error->title());
        self::assertEquals(500, $error->status());
    }

    public function testConfiguredError(): void
    {
        $error = new Error(
            400,
            'Invalid Request',
            'Invalid Parameter "name" given',
            'invalid_request'
        );

        $error->metaInformation()->set('test', 'test');
        $error->source()->set('sourceTest', 'sourceTest');

        self::assertEquals('invalid_request', $error->code());

        self::assertEquals(400, $error->status());

        self::assertEquals('Invalid Request', $error->title());

        self::assertEquals(
            'Invalid Parameter "name" given',
            $error->detail()
        );

        self::assertEquals('test', $error->metaInformation()->getRequired('test'));
        self::assertEquals('sourceTest', $error->source()->getRequired('sourceTest'));
    }

    public function testErrorFromException(): void
    {
        $error = Error::createFrom(new Exception('Test', 13));

        self::assertEquals('13', $error->code());
        self::assertEquals('Test', $error->title());
        self::assertEquals(500, $error->status());
    }

    public function testErrorFromExceptionWithDebug(): void
    {
        $error = Error::createFrom(new Exception('Test'), true);

        self::assertEquals('', $error->code());
        self::assertEquals('Test', $error->title());
        self::assertEquals(500, $error->status());
        self::assertArrayHasKey('file', $error->metaInformation()->all());
    }
}
