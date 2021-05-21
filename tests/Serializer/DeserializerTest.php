<?php
namespace Dogado\JsonApi\Tests\Serializer;

use Dogado\JsonApi\Serializer\Deserializer;
use Dogado\JsonApi\Tests\TestCase;
use InvalidArgumentException;

class DeserializerTest extends TestCase
{
    public function testDeserializeResourceDocument(): void
    {
        $documentDeserializer = $this->createDeserializer();

        $document = $documentDeserializer->deserializeDocument(
            [
                'jsonapi' => [
                    'version' => '1.0',
                    'meta' => ['test' => 'test']
                ],
                'data' => [
                    'type' => 'test',
                    'id' => 'test-2',
                    'attributes' => [
                        'key' => 'value'
                    ],
                    'links' => [
                        'self' => 'http://example.com',
                        'test' => ['href' => 'http://example.com/test', 'meta' => ['a' => 'b']],
                    ],
                    'relationships' => [
                        'parent' => [
                            'data' => [
                                'type' => 'test',
                                'id' => 'test-1'
                            ],
                            'links' => [
                                'self' => 'http://example.com/test/test-2/parent',
                            ]
                        ],
                        'children' => [
                            'data' => [
                                [
                                    'type' => 'test',
                                    'id' => 'test-3'
                                ]
                            ]
                        ],
                        'empty' => [
                            'meta' => [
                                'empty' => 'empty'
                            ]
                        ]
                    ],
                    'meta' => [
                        'metaKey' => 'metaValue'
                    ]
                ]
            ]
        );

        self::assertEquals('test', $document->data()->first()->type());
        self::assertFalse($document->data()->first()->relationships()->get('parent')->shouldBeHandledAsCollection());
        self::assertTrue($document->data()->first()->relationships()->get('parent')->links()->has('self'));
        self::assertTrue($document->data()->first()->relationships()->get('empty')->metaInformation()->has('empty'));
        self::assertTrue($document->data()->first()->relationships()->get('children')->shouldBeHandledAsCollection());
    }

    public function testInvalidLink(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->createDeserializer()->deserializeDocument(
            [
                'data' => [
                    'type' => 'test',
                    'id' => 'test-2',
                    'links' => [
                        'test' => [],
                    ],
                ]
            ]
        );
    }

    public function testInvalidResource(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->createDeserializer()->deserializeDocument(
            [
                'data' => [
                    'id' => 'test',
                ]
            ]
        );
    }

    public function testErrorDocument(): void
    {
        $document = $this->createDeserializer()->deserializeDocument(
            [
                'errors' => [
                    [
                        'title' => 'Test',
                        'meta' => [
                            'key' => 'value'
                        ]
                    ]
                ]
            ]
        );

        self::assertFalse($document->errors()->isEmpty());
        self::assertEquals('Test', $document->errors()->all()[0]->title());
        self::assertEquals('value', $document->errors()->all()[0]->metaInformation()->getRequired('key'));
    }

    public function testResourceCollectionDocument(): void
    {
        $document = $this->createDeserializer()->deserializeDocument(
            [
                'data' => [
                    [
                        'type' => 'test',
                        'id' => 'test-1'
                    ]
                ],
                'meta' => [
                    'key' => 'value'
                ],
                'links' => [
                    'self' => 'http://example.com/test'
                ],
                'included' => [
                    [
                        'type' => 'test',
                        'id' => 'test-2',
                        'relationships' => [
                            'related' => [
                                'data' => [
                                    'type' => 'example',
                                    'id' => 'example-1'
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        );

        self::assertEquals(1, $document->data()->count());
        self::assertEquals(1, $document->links()->count());
        self::assertEquals(1, $document->metaInformation()->count());
        self::assertEquals(1, $document->included()->count());
    }

    protected function createDeserializer(): Deserializer
    {
        return new Deserializer();
    }
}
