<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Tests\Support\Model\DataModelAnalyserTest;

use DateTime;
use DateTimeInterface;
use Dogado\JsonApi\Attribute\Attribute;
use Dogado\JsonApi\Attribute\Id;
use Dogado\JsonApi\Attribute\Type;
use Dogado\JsonApi\Support\Model\CustomAttributeGetterInterface;
use stdClass;

#[Type('dummy-model')]
class DummyModel implements CustomAttributeGetterInterface
{
    #[Id]
    private ?string $modelId = '12345';

    #[Attribute]
    protected ?string $name = 'name';

    #[Attribute('name')]
    private ?string $newName = 'newName';

    #[Attribute('/sub-object/test/property/')]
    public ?string $propertyWithinObject = 'lorem';

    #[Attribute('/sub-object///test/second-property/')]
    private ?string $secondPropertyWithinObject = 'ipsum';

    #[Attribute('/sub-object/createdAt')]
    private ?DateTime $createdAt;

    #[Attribute('/sub-object/updatedAt')]
    private ?DateTime $updatedAt = null;

    #[Attribute('sub-model')]
    private DummyValueObjectModel $aggregationModel;

    #[Attribute('filled-model-without-attributes')]
    private stdClass $filledModelWithoutAttributes;

    #[Attribute('sub-model-null')]
    private ?DummyValueObjectModel $aggregationModelNull = null;

    #[Attribute(ignoreOnNull: true)]
    private ?string $ignoreOnNull = null;

    public function __construct(DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
        $this->filledModelWithoutAttributes = new stdClass();
        $this->aggregationModel = new DummyValueObjectModel();
    }

    public function __getAttribute(string $property): ?string
    {
        return match ($property) {
            'createdAt' => $this->createdAt->format(DateTimeInterface::ATOM),
            default => null,
        };
    }
}
