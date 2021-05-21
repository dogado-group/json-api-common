<?php
namespace Dogado\JsonApi\Tests\Model\Response;

use Dogado\JsonApi\Model\Document\Document;
use Dogado\JsonApi\Model\JsonApiInterface;
use Dogado\JsonApi\Model\Response\AcceptedResponse;
use Dogado\JsonApi\Model\Response\CreatedResponse;
use Dogado\JsonApi\Model\Response\DocumentResponse;
use Dogado\JsonApi\Model\Response\EmptyResponse;
use Dogado\JsonApi\Support\Collection\KeyValueCollection;
use Dogado\JsonApi\Tests\TestCase;

class ResponseTest extends TestCase
{
    public function testAccepted(): void
    {
        $faker = $this->faker;
        $dummyHeaders = new KeyValueCollection([
            $faker->word => $faker->name,
            $faker->word => $faker->address,
        ]);
        $response = new AcceptedResponse($dummyHeaders);
        $this->assertNull($response->document());
        $this->assertEquals(202, $response->status());
        foreach ($dummyHeaders->all() as $key => $value) {
            $this->assertEquals($value, $response->headers()->get($key));
        }
        $this->assertEquals(JsonApiInterface::CONTENT_TYPE, $response->headers()->getRequired('Content-Type'));
    }

    public function testCreated(): void
    {
        $faker = $this->faker;
        $location = $faker->url;
        $dummyHeaders = new KeyValueCollection([
            $faker->word => $faker->name,
            $faker->word => $faker->address,
        ]);
        /** @var Document $document */
        $document = $this->getMockBuilder(Document::class)->disableOriginalConstructor()->getMock();

        $response = new CreatedResponse($location, $dummyHeaders, $document);
        $this->assertEquals($document, $response->document());
        $this->assertEquals(201, $response->status());
        foreach ($dummyHeaders->all() as $key => $value) {
            $this->assertEquals($value, $response->headers()->get($key));
        }
        $this->assertEquals(JsonApiInterface::CONTENT_TYPE, $response->headers()->getRequired('Content-Type'));
        $this->assertEquals($location, $response->headers()->getRequired('Location'));
    }

    public function testDocument(): void
    {
        $faker = $this->faker;
        $status = $faker->numberBetween();
        $dummyHeaders = new KeyValueCollection([
            $faker->word => $faker->name,
            $faker->word => $faker->address,
        ]);
        /** @var Document $document */
        $document = $this->getMockBuilder(Document::class)->disableOriginalConstructor()->getMock();

        $response = new DocumentResponse($document, $dummyHeaders, $status);
        $this->assertEquals($document, $response->document());
        $this->assertEquals($status, $response->status());
        foreach ($dummyHeaders->all() as $key => $value) {
            $this->assertEquals($value, $response->headers()->get($key));
        }
        $this->assertEquals(JsonApiInterface::CONTENT_TYPE, $response->headers()->getRequired('Content-Type'));
    }

    public function testEmpty(): void
    {
        $faker = $this->faker;
        $dummyHeaders = new KeyValueCollection([
            $faker->word => $faker->name,
            $faker->word => $faker->address,
        ]);

        $response = new EmptyResponse($dummyHeaders);
        $this->assertEquals(204, $response->status());
        $this->assertNull($response->document());
        foreach ($dummyHeaders->all() as $key => $value) {
            $this->assertEquals($value, $response->headers()->get($key));
        }
        $this->assertEquals(JsonApiInterface::CONTENT_TYPE, $response->headers()->getRequired('Content-Type'));
    }
}