<?php

namespace Dogado\JsonApi\Tests\Converter\ModelConverterTest\Php8Attributes;

use DateTime;
use DateTimeInterface;
use Dogado\JsonApi\Annotations\Attribute;
use Dogado\JsonApi\Annotations\Id;
use Dogado\JsonApi\Annotations\Type;
use Dogado\JsonApi\Support\Model\CustomAttributeGetterInterface;
use Dogado\JsonApi\Support\Model\CustomAttributeSetterInterface;
use InvalidArgumentException;

/**
 * @Type("dummy-serializer-model-mixed")
 */
class DataModelWithMixedAnnotations implements CustomAttributeGetterInterface, CustomAttributeSetterInterface
{
    #[Id]
    private ?int $id = 1234567;

    #[Attribute]
    private ?string $name = 'loremIpsum123';

    /**
     * @Attribute("values")
     */
    private ValueObject $valueObject;

    #[Attribute('empty-values')]
    private ?ValueObject $valueObjectNotInitialized = null;

    #[Attribute(ignoreOnNull: true)]
    private ?string $ignoreOnNull = null;

    /**
     * @Attribute()
     */
    private DateTime $createdAt;

    #[Attribute]
    private ?DateTime $updatedAt = null;

    public function __construct(DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
        $this->valueObject = new ValueObject();
    }

    /**
     * @return mixed
     */
    public function __getAttribute(string $property)
    {
        switch ($property) {
            case 'createdAt':
                return $this->createdAt->format(DateTimeInterface::ATOM);
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
            case 'createdAt':
                $dateTime = DateTime::createFromFormat(DateTimeInterface::ATOM, $value);
                if (!$dateTime) {
                    throw new InvalidArgumentException('createdAt is no valid atom string');
                }
                $this->createdAt = $dateTime;
                return true;
            default:
                return false;
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getValueObject(): ValueObject
    {
        return $this->valueObject;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }
}
