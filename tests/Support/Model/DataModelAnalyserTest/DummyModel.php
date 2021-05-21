<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Tests\Support\Model\DataModelAnalyserTest;

use DateTime;
use Dogado\JsonApi\Annotations\Attribute;
use Dogado\JsonApi\Annotations\Id;
use Dogado\JsonApi\Annotations\Type;
use Dogado\JsonApi\Support\Model\CustomAttributeGetterInterface;

/**
 * @Type("dummy-model")
 */
class DummyModel implements CustomAttributeGetterInterface
{
    /**
     * @Id()
     */
    private ?string $modelId = '12345';

    /**
     * @Attribute()
     */
    protected ?string $name = 'name';

    /**
     * @Attribute("name")
     */
    private ?string $newName = 'newName';

    /**
     * @Attribute("/sub-object/test/property/")
     */
    public ?string $propertyWithinObject = 'lorem';

    /**
     * @Attribute("/sub-object///test/second-property/")
     */
    private ?string $secondPropertyWithinObject = 'ipsum';

    /**
     * @Attribute("/sub-object/createdAt")
     */
    private ?DateTime $createdAt;

    /**
     * @Attribute("/sub-object/updatedAt")
     */
    private ?DateTime $updatedAt = null;

    /**
     * @Attribute("sub-model")
     */
    private DummyValueObjectModel $aggregationModel;

    /**
     * @Attribute("sub-model-null")
     */
    private ?DummyValueObjectModel $aggregationModelNull = null;

    /**
     * @Attribute(ignoreOnNull=true)
     */
    private ?string $ignoreOnNull = null;

    public function __construct(DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
        $this->aggregationModel = new DummyValueObjectModel();
    }

    /**
     * @return null|mixed
     */
    public function __getAttribute(string $propertyName)
    {
        switch ($propertyName) {
            case 'createdAt':
                return $this->createdAt->format(DateTime::ATOM);
            default:
                return null;
        }
    }
}
