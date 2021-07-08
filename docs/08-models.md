[back to README](../README.md)

# Model conversion

A very common business case when using API's is to convert a result document into a business layer data model, and a data model into a document the remote API would understand.
To make the JSON API integration easier, this package also offers the possibility to accomplish that, based on annotations. In this package, this is called "model conversion".

In order to do that, we need to translate models and their property definitions into JSON API resources. Here is an example based on custom annotations:

```php
<?php
namespace App\Models;

use DateTime;
use Dogado\JsonApi\Annotations\Attribute;
use Dogado\JsonApi\Annotations\Type;
use Dogado\JsonApi\Annotations\Id;
use Dogado\JsonApi\Support\Model\CustomAttributeGetterInterface;
use Dogado\JsonApi\Support\Model\CustomAttributeSetterInterface;
use InvalidArgumentException;

/**
 * _Required_: The JSON API resource type (/data/type)
 * @Type("user")
 */
class User implements CustomAttributeGetterInterface, CustomAttributeSetterInterface
{
    /**
     * Translates to: /data/id
     * @Id()
     */
    private ?int $id = null;

    /**
     * Translates to: /data/attributes/name
     * @Attribute(ignoreOnNull=true)
     */
    private ?string $name = null;

    /**
     * Translates to: /data/attributes/email
     * @Attribute("email")
     */
    private ?string $emailAddress = null;

    /**
     * Translates to: /data/attributes/options/receiveNewsletters
     * @Attribute("/options/receiveNewsletters")
     */
    private ?bool $receiveNewsletters = null;

    /**
     * Translates to: /data/attributes/address
     * @Attribute("address")
     */
    private AddressValueObject $address;

    /**
     * @Attribute()
     */
    private ?DateTime $createdAt = null;

    /**
     * @Attribute()
     */
    private ?DateTime $updatedAt = null;

    public function __construct()
    {
        $this->address = new AddressValueObject();
    }

    /**
     * @return mixed
     */
    public function __getAttribute(string $property)
    {
        switch ($property) {
            case 'updatedAt':
            case 'createdAt':
                return $this->createdAt->format(DateTime::ATOM);
            default:
                return null;
        }
    }

    /**
     * @param mixed $value
     */
    public function __setAttribute(string $property, $value): bool
    {
        switch ($property) {
            case 'updatedAt':
                $dateTime = DateTime::createFromFormat(DateTime::ATOM, $value);
                if (!$dateTime) {
                    throw new InvalidArgumentException('updatedAt is no valid atom string');
                }
                $this->updatedAt = $dateTime;
                return true;
            default:
                return false;
        }
    }
}
```

The model conversion supports the filling and conversion of all types of member properties. This also includes object properties, under two conditions:
* the value object also contains `@Attribute()` annotations in the properties (`@Id` and `@Type` is only supported on root level)
* when converting from JSON API resource to model, the object property has to be an initialized object instance

Whenever you want to use objects as Attributes which do not hold Attributes definitions themselves, you have to use either the `CustomAttributeGetterInterface` or `CustomAttributeSetterInterface`, depending on your needs.

## php 8 attribute support

[php 8 introduced attributes](https://www.php.net/manual/en/language.attributes.overview.php) which are influenced
by annotations. The model conversion supports this feature. All annotation classes can just as well be used as php 8 attributes.
This is the php 8 equivalent to the above written example:

```php
<?php
namespace App\Models;

use DateTime;
use Dogado\JsonApi\Annotations\Attribute;
use Dogado\JsonApi\Annotations\Type;
use Dogado\JsonApi\Annotations\Id;

#[Type('user')]
class User
{
    #[Id]
    private ?int $id = null;

    /**
     * Yes, even named arguments are working here!
     */
    #[Attribute(ignoreOnNull: true)]
    private ?string $name = null;

    #[Attribute('email')]
    private ?string $emailAddress = null;

    #[Attribute('/options/receiveNewsletters')]
    private ?bool $receiveNewsletters = null;

    #[Attribute('address')]
    private AddressValueObject $address;

    #[Attribute]
    private ?DateTime $createdAt = null;

    #[Attribute]
    private ?DateTime $updatedAt = null;
}
```

## Typed properties

This package supports php `8.0`, which means that it also supports typed properties, and a limited casting of values. Casting will be done for the following property data types:
* `bool`/`boolean`
* `int`/`integer`
* `float`
* `string`
* `array`

`mixed` values are kept and all other types will be ignored.

It is highly recommended to use typed properties as much as possible, and it is good practice to also make them nullable, since the remote JSON API server might not return some attributes that your data model expects. Otherwise, this will end in a php fatal error.

## Attributes that should be ignored in case of a `null` value

Usually, the model converter would convert all `@Attribute()` tagged properties into JSON API attributes, even if they are `null`, but let's assume you only want to update specific attributes.
You might run into the issue, that the JSON API server expects those other attributes which you do not want to change in a different state than `null`, but the model converter will convert them to `null` anyway.
That's why there is the `@Attribute` option `ignoreOnNull=false`. Whenever set to `true`, the model converter will not put it into the resulting resource instance if it contains a `null` value. 

## Handling attribute array/object values

Whenever an attribute is either an array, or an object (objects will always be converted to array if there is no value object defined for them), there are two ways to convert them into model properties.

### Named array item properties

You can create properties which represent the array/object structure within the JSON API resource.

```php
    /**
     * Translates to: /data/attributes/options/receiveNewsletters
     * @Attribute("/options/receiveNewsletters")
     */
    private ?bool $receiveNewsletters = null;
```

_However, this does not support collection addresses in wildcard style: `@Attribute("/options/*/receiveNewsletters")`_

### Array properties

You can define the attribute that contains an object or array as array property.

```php
    /**
     * Translates to: /data/attributes/options
     * @Attribute()
     */
    private ?array $options = null;
```

## Usage example

If you want to see more practical examples, check out the unit tests located in `/tests/Converter`.

### Convert to resource

```php
use App\Models\Model;
use Dogado\JsonApi\Model\Resource\Resource;
use Dogado\JsonApi\Converter\ModelConverter;

$model = new Model();
$resource = (new ModelConverter())->toResource($model);
assert($resource instanceof Resource);
```

### Convert to model

```php
use App\Models\Model;
use Dogado\JsonApi\Model\Resource\Resource;
use Dogado\JsonApi\Converter\ResourceConverter;

$resource = new Resource(
    'type-name',
    '12345',
    [
        'stringValue' => 'loremIpsum',
        'array' => [
            'item' => 'fooBar'
        ],
    ],
);

$model = new Model();
(new ResourceConverter())->toModel($resource, $model);
// `$model` will now contain the JSON API resource data
```

*****

[prev: Request and response](../docs/07-requests.md) | [back to README](../README.md)
