[back to README](../README.md)

# Resources

A JSON API resource is representing a PHP object of type `Dogado\JsonApi\Model\Resource\ResourceInterface` and requires at least `type`.

`Dogado\JsonApi\Model\Resource\ResourceInterface`:

| Method                       | Return type                                                                                                  | Description                                                         |
|------------------------------|--------------------------------------------------------------------------------------------------------------|---------------------------------------------------------------------|
| type()                       | string                                                                                                       | Resource Type Identifier                                            |
| id()                         | string or null                                                                                               | Resource Identifier                                                 |
| attributes()                 | [KeyValueCollectionInterface](../src/Support/Collection/KeyValueCollectionInterface.php)                     | Attributes of the resource                                          |
| relationships()              | [RelationshipCollectionInterface](../src/Support/Resource/Relationship/RelationshipCollectionInterface.php)  | The relationships of a resource                                     |
| links()                      | [LinkCollectionInterface](../src/Support/Resource/Link/LinkCollectionInterface.php)                          | The links for a resource                                            |
| metaInformation()            | [KeyValueCollectionInterface](../src/Support/Collection/KeyValueCollectionInterface.php)                     | Meta Information for a resource                                     |
| duplicate(string $id = null) | [ResourceInterface](../src/Model/Resource/ResourceInterface.php)                                             | Helper method to duplicate this resource, optional with another id. |

## Relationships

A Relationship is representing a PHP object of type `Dogado\JsonApi\Model\Resource\Relationship\RelationshipInterface`:

| Method                         | Return type                                                                              | Description                                                                              |
|--------------------------------|------------------------------------------------------------------------------------------|------------------------------------------------------------------------------------------|
| shouldBeHandledAsCollection()  | boolean                                                                                  | Indicates if the contained data should be handled as object collection or single object. |
| name()                         | string                                                                                   | The relationship name                                                                    |
| related()                      | [ResourceCollectionInterface](../src/Support/Resource/ResourceCollectionInterface.php)   | Collection of related resources for this relationship.                                   |
| links()                        | [LinkCollectionInterface](../src/Support/Resource/Link/LinkCollectionInterface.php)      | Collection of link objects for this relationship.                                        |
| metaInformation()              | [KeyValueCollectionInterface](../src/Support/Collection/KeyValueCollectionInterface.php) | Collection of meta information for this relationship.                                    |
| duplicate(string $name = null) | [RelationshipInterface](../src/Model/Resource/Relationship/RelationshipInterface.php)    | Helper method to duplicate this relationship, optional with another name.                |

A relationship contains, depending on return value of "shouldBeHandledAsCollection", one or many related resources or can be empty.

A relationship needs a unique name (in context of one resource) and offers access to all related resources via `RelationshipInterface::related()`.
Relationships can contain links and meta information like resources.

The relationships of a resource are accessible via `ResourceInterface::relationships()`, which is an instance of `Dogado\JsonApi\Support\Resource\Relationship\RelationshipCollectionInterface`:

| Method                                                       | Return type                                                                            | Description                                                                                            |
|--------------------------------------------------------------|----------------------------------------------------------------------------------------|--------------------------------------------------------------------------------------------------------|
| all()                                                        | array                                                                                  | All relationship objects of this collection.                                                           |
| count()                                                      | int                                                                                    | Number of collection entries.                                                                          |
| isEmpty()                                                    | bool                                                                                   | Checks if the collection contains any elements.                                                        |
| has(string $name)                                            | bool                                                                                   | Checks if the collection contains a special relationship.                                              |
| get(string $name)                                            | [RelationshipInterface](../src/Model/Resource/Relationship/RelationshipInterface.php)  | Returns a relationship by name or throws an \InvalidArgumentException if relationship does not exists. |
| set(RelationshipInterface $relationship)                     | self                                                                                   | Set a relationship object into the collection.                                                         |
| remove(string $name)                                         | self                                                                                   | Remove a relationship by name from the collection.                                                     |
| removeElement(RelationshipInterface $relationship)           | self                                                                                   | Remove a relationship object from the collection.                                                      |

*****

[back to JsonApiTrait](../docs/01-trait.md) | [back to README](../README.md) | [next: Attributes and meta information](../docs/03-collections.md)
