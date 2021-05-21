[back to README](../README.md)

# Links

Links for resources or documents group through an object of type `Dogado\JsonApi\Support\Resource\Link\LinkCollectionInterface`.

| Method                                 | Return type                                                      | Description                                                                                    |
|----------------------------------------|------------------------------------------------------------------|------------------------------------------------------------------------------------------------|
| all()                                  | array                                                            | All link objects of this collection.                                                           |
| count()                                | int                                                              | Number of collection entries.                                                                  |
| isEmpty()                              | bool                                                             | Checks if the collection contains any elements.                                                |
| has(string $name)                      | bool                                                             | Checks if the collection contains a special link.                                              |
| get(string $name)                      | [LinkInterface](../src/Model/Resource/Link/LinkInterface.php)    | Returns a link by name or throws an \InvalidArgumentException if relationship does not exists. |
| set(LinkInterface $link)               | self                                                             | Set a link object into the collection.                                                         |
| remove(string $name)                   | self                                                             | Remove a link by name from the collection.                                                     |
| removeElement(LinkInterface $link)     | self                                                             | Remove a link object from the collection.                                                      |
| createLink(string $name, string $href) | self                                                             | Create a new link object in the collection.                                                    |

A link itself is an object of type `Dogado\JsonApi\Model\Resource\Link\LinkInterface`. 

| Method                         | Return type                                                                              | Description                                                       |
|--------------------------------|------------------------------------------------------------------------------------------|-------------------------------------------------------------------|
| name()                         | string                                                                                   | The link name.                                                    |
| href()                         | string                                                                                   | The link target.                                                  |
| metaInformation()              | [KeyValueCollectionInterface](../src/Support/Collection/KeyValueCollectionInterface.php) | Collection of meta information for this link.                     |
| duplicate(string $name = null) | [LinkInterface](../src/Model/Resource/Link/LinkInterface.php)                            | Helper method to duplicate this link, optional with another name. |

*****

[prev: Attributes and meta information](../docs/03-collections.md) | [back to README](../README.md) | [next: Documents](../docs/05-documents.md)
