<?php

namespace Dogado\JsonApi\Tests\Converter\ModelConverterTest;

use DateTime;
use DateTimeInterface;
use Dogado\JsonApi\Attribute\Attribute;
use Dogado\JsonApi\Attribute\Id;
use Dogado\JsonApi\Attribute\Type;
use Dogado\JsonApi\Support\Model\CustomAttributeGetterInterface;
use Dogado\JsonApi\Support\Model\CustomAttributeSetterInterface;
use InvalidArgumentException;

#[Type('dummy-serializer-model')]
class DataModel implements CustomAttributeGetterInterface, CustomAttributeSetterInterface
{
    #[Id]
    private ?int $id = 123456;

    #[Attribute]
    private ?string $name = 'loremIpsum';

    #[Attribute('values')]
    private ValueObject $valueObject;

    #[Attribute('empty-values')]
    private ?ValueObject $valueObjectNotInitialized = null;

    #[Attribute]
    private ValueObjectWithoutAttributes $valueObjectWithoutAttributes;

    #[Attribute(ignoreOnNull: true)]
    private ?string $ignoreOnNull = null;

    #[Attribute]
    private DateTime $createdAt;

    #[Attribute]
    private ?DateTime $updatedAt = null;

    public function __construct(DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
        $this->valueObject = new ValueObject();
        $this->valueObjectWithoutAttributes = new ValueObjectWithoutAttributes();
    }

    public function __getAttribute(string $property): ?string
    {
        return match ($property) {
            'createdAt' => $this->createdAt->format(DateTimeInterface::ATOM),
            default => null,
        };
    }

    public function __setAttribute(string $property, mixed $value): bool
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
