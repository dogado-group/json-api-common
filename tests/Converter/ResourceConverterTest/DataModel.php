<?php

namespace Dogado\JsonApi\Tests\Converter\ResourceConverterTest;

use DateTime;
use DateTimeInterface;
use Dogado\JsonApi\Attribute\Attribute;
use Dogado\JsonApi\Attribute\Id;
use Dogado\JsonApi\Attribute\Type;
use Dogado\JsonApi\Support\Model\CustomAttributeSetterInterface;
use InvalidArgumentException;

#[Type('dummy-deserializer-model')]
class DataModel implements CustomAttributeSetterInterface
{
    #[Id]
    private ?int $id = null;

    #[Attribute]
    private ?string $nullAttribute = 'loremIpsum';

    #[Attribute]
    private ?string $stringValue = null;

    #[Attribute]
    private mixed $mixedValue = null;

    #[Attribute]
    private $noTypeDeclaration = null;

    #[Attribute]
    private string $notNullable = 'loremIpsum';

    #[Attribute]
    private ?string $doesNotExistInResource = null;

    #[Attribute]
    private ?string $notCastable = null;

    #[Attribute('values')]
    private ValueObject $valueObject;

    #[Attribute('arrayItems')]
    private ?array $arrayItems = null;

    #[Attribute]
    private ?bool $castBool = null;

    #[Attribute]
    private ?int $castInt = null;

    #[Attribute]
    private ?float $castFloat = null;

    #[Attribute]
    private ?string $castString = null;

    #[Attribute('named/sub/item')]
    private ?string $namedSubItem = null;

    #[Attribute('named/sub/item2')]
    private ?string $namedSubItem2 = null;

    #[Attribute]
    private ?array $willBeCastedToArray = null;

    #[Attribute(ignoreOnNull: true)]
    private ?string $ignoreOnNull = null;

    #[Attribute]
    private ?DateTime $createdAt = null;

    #[Attribute]
    private ?DateTime $updatedAt = null;

    public function __construct()
    {
        $this->valueObject = new ValueObject();
    }

    public function __setAttribute(string $property, mixed $value): bool
    {
        switch ($property) {
            case 'createdAt':
                $dateTime = DateTime::createFromFormat(DateTimeInterface::ATOM, $value);
                if (!$dateTime) {
                    throw new InvalidArgumentException('createdAt value is no valid atom string');
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

    public function getNullAttribute(): ?string
    {
        return $this->nullAttribute;
    }

    public function getStringValue(): ?string
    {
        return $this->stringValue;
    }

    public function getMixedValue(): mixed
    {
        return $this->mixedValue;
    }

    public function getNoTypeDeclaration()
    {
        return $this->noTypeDeclaration;
    }

    public function getNotNullable(): string
    {
        return $this->notNullable;
    }

    public function getDoesNotExistInResource(): ?string
    {
        return $this->doesNotExistInResource;
    }

    public function getNotCastable(): ?string
    {
        return $this->notCastable;
    }

    public function getValueObject(): ValueObject
    {
        return $this->valueObject;
    }

    public function getArrayItems(): ?array
    {
        return $this->arrayItems;
    }

    public function getCastBool(): ?bool
    {
        return $this->castBool;
    }

    public function getCastInt(): ?int
    {
        return $this->castInt;
    }

    public function getCastFloat(): ?float
    {
        return $this->castFloat;
    }

    public function getCastString(): ?string
    {
        return $this->castString;
    }

    public function getNamedSubItem(): ?string
    {
        return $this->namedSubItem;
    }

    public function getNamedSubItem2(): ?string
    {
        return $this->namedSubItem2;
    }

    public function getWillBeCastedToArray(): ?array
    {
        return $this->willBeCastedToArray;
    }

    public function getIgnoreOnNull(): ?string
    {
        return $this->ignoreOnNull;
    }

    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }
}
