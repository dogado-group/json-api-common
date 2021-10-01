<?php
namespace Dogado\JsonApi\Tests\Support\Collection;

use Dogado\JsonApi\Support\Collection\KeyValueCollection;
use Dogado\JsonApi\Tests\TestCase;
use InvalidArgumentException;

class KeyValueCollectionTest extends TestCase
{
    public function testAll(): void
    {
        $key = $this->faker->domainWord;
        $collection = new KeyValueCollection([$key => 'test']);
        self::assertArrayHasKey($key, $collection->all());
    }

    public function testCount(): void
    {
        $collection = new KeyValueCollection(['test' => 'test']);
        self::assertCount(1, $collection);
        self::assertEquals(1, $collection->count());
    }

    public function testIsEmpty(): void
    {
        $collection = new KeyValueCollection(['test' => 'test']);
        self::assertFalse($collection->isEmpty());

        $collection = new KeyValueCollection();
        self::assertTrue($collection->isEmpty());
    }

    public function testHas(): void
    {
        $key = $this->faker->domainWord;
        $collection = new KeyValueCollection([$key => 'test']);
        self::assertTrue($collection->has($key));
    }

    public function testGetRequired(): void
    {
        $key = $this->faker->domainWord;
        $value = $this->faker->userName;

        $collection = new KeyValueCollection([$key => $value]);
        self::assertEquals($value, $collection->getRequired($key));
    }

    public function testGetOptional(): void
    {
        $key = $this->faker->domainWord;
        $value = $this->faker->userName;

        $collection = new KeyValueCollection();
        self::assertEquals($value, $collection->get($key, $value));
    }

    public function testCreateSubCollection(): void
    {
        $key = $this->faker->domainWord;
        $subKey = $this->faker->domainWord;
        $value = $this->faker->userName;

        $collection = new KeyValueCollection([$key => [$subKey => $value]]);

        self::assertEquals($value, $collection->getSubCollection($key)->getRequired($subKey));
    }

    public function testGetSubCollectionInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $collection = new KeyValueCollection([$this->faker->domainWord => $this->faker->userName]);

        $collection->getSubCollection($this->faker->domainWord);
    }

    public function testGetSubCollectionItemNoArray(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $key = $this->faker->domainWord;
        $collection = new KeyValueCollection([$key => $this->faker->userName]);

        $collection->getSubCollection($key);
    }

    public function testCreateSubCollectionRequired(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $collection = new KeyValueCollection();
        $collection->getSubCollection($this->faker->domainWord);
    }

    public function testCreateSubCollectionOptional(): void
    {
        $collection = new KeyValueCollection();

        self::assertCount(0, $collection->getSubCollection($this->faker->domainWord, false)->all());
    }

    public function testMergeCollection(): void
    {
        $key = $this->faker->domainWord;
        $value = $this->faker->userName;

        $collection = new KeyValueCollection([$key => $value]);

        self::assertEquals($value, $collection->getRequired($key));

        $key2 = $this->faker->domainWord;
        $value2 = $this->faker->userName;
        $key3 = $this->faker->domainWord;
        $value3 = $this->faker->userName;

        $collection->mergeCollection(new KeyValueCollection([$key2 => $value2, $key3 => $value3]));

        self::assertEquals($value2, $collection->getRequired($key2));
        self::assertEquals($value3, $collection->getRequired($key3));
    }

    public function testMergeCollectionNoOverwrite(): void
    {
        $key = $this->faker->domainWord;
        $value = $this->faker->userName;

        $collection = new KeyValueCollection([$key => $value]);

        self::assertEquals($value, $collection->getRequired($key));

        $value2 = $this->faker->userName;
        $key3 = $this->faker->domainWord;
        $value3 = $this->faker->userName;

        $collection->mergeCollection(new KeyValueCollection([$key => $value2, $key3 => $value3]), false);

        self::assertEquals($value, $collection->getRequired($key));
        self::assertEquals($value3, $collection->getRequired($key3));
    }

    public function testMerge(): void
    {
        $key = $this->faker->domainWord;
        $value = $this->faker->userName;

        $collection = new KeyValueCollection([$key => $value]);

        self::assertEquals($value, $collection->getRequired($key));

        $value2 = $this->faker->userName;
        $key3 = $this->faker->domainWord;
        $value3 = $this->faker->userName;

        $collection->merge([$key => $value2, $key3 => $value3]);

        self::assertEquals($value2, $collection->getRequired($key));
        self::assertEquals($value3, $collection->getRequired($key3));
    }


    public function testMergeNoOverwrite(): void
    {
        $key = $this->faker->domainWord;
        $value = $this->faker->userName;

        $collection = new KeyValueCollection([$key => $value]);

        self::assertEquals($value, $collection->getRequired($key));

        $value2 = $this->faker->userName;
        $key3 = $this->faker->domainWord;
        $value3 = $this->faker->userName;

        $collection->merge([$key => $value2, $key3 => $value3], false);

        self::assertEquals($value, $collection->getRequired($key));
        self::assertEquals($value3, $collection->getRequired($key3));
    }

    public function testGetInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $collection = new KeyValueCollection(['test' => 'test']);
        $collection->getRequired('abc');
    }

    public function testSet(): void
    {
        $collection = new KeyValueCollection(['test' => 'test']);
        self::assertFalse($collection->has('abc'));
        $collection->set('abc', 'abc');
        self::assertTrue($collection->has('abc'));
    }

    public function testSetCollection(): void
    {
        $collection = new KeyValueCollection(['test' => 'test']);
        self::assertFalse($collection->has('abc'));
        $collection->set('abc', new KeyValueCollection(['test' => 'test']));
        self::assertTrue($collection->has('abc'));
        self::assertArrayHasKey('test', $collection->getRequired('abc'));
    }

    public function testRemove(): void
    {
        $collection = new KeyValueCollection(['test' => 'test']);
        self::assertTrue($collection->has('test'));
        $collection->remove('test');
        self::assertFalse($collection->has('test'));
    }

    public function testRemoveInvalid(): void
    {
        $collection = new KeyValueCollection(['test' => 'test']);
        $collection->remove('abc');

        self::assertTrue($collection->has('test'));
    }

    public function testPull(): void
    {
        $collection = new KeyValueCollection(['key' => 'value']);
        $value = $collection->pull('key');
        
        self::assertEquals('value', $value);
        self::assertFalse($collection->has('key'));
    }
}
