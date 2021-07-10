<?php
namespace Dogado\JsonApi\Tests\Exception;

use Dogado\JsonApi\Exception\BadResponseException;
use Dogado\JsonApi\Exception\DocumentSerializerException;
use Dogado\JsonApi\Tests\TestCase;

class ExceptionTest extends TestCase
{
    public function testBadResponse(): void
    {
        $jsonError = $this->faker()->text();
        $e = BadResponseException::invalidJsonDocument($jsonError);
        $this->assertStringContainsString($jsonError, $e->getMessage());
        $this->assertEquals(BadResponseException::CODE_INVALID_JSON_DOCUMENT, $e->getCode());
    }

    public function testDocumentSerializer(): void
    {
        $jsonError = $this->faker()->text();
        $e = DocumentSerializerException::unableGenerateJsonDocument($jsonError);
        $this->assertStringContainsString($jsonError, $e->getMessage());
        $this->assertEquals(DocumentSerializerException::CODE_JSON_DOCUMENT_GENERATION_FAILED, $e->getCode());
    }
}