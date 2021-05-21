[back to README](../README.md)

# Request and response

`Dogado\JsonApi\Model\Request\RequestInterface`:

| Method                                                                                                 | Return Type                   | Description                                                                                                                                                                                 |
|--------------------------------------------------------------------------------------------------------|-------------------------------|---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| method()                                                                                               | string                        | The http method of this request                                                                                                                                                             |
| uri()                                                                                                  | Psr\Http\Message\UriInterface | The request uri                                                                                                                                                                             |
| headers()                                                                                              | KeyValueCollectionInterface   | Request headers. Can be changed.                                                                                                                                                            |
| type()                                                                                                 | string                        | Requested resource type                                                                                                                                                                     |
| id()                                                                                                   | string|null                   | Requested resource id, if present                                                                                                                                                           |
| relationship()                                                                                         | string|null                   | Requested relationship, if present                                                                                                                                                          |
| requestsAttributes()                                                                                   | bool                          | Indicates if the response for this request should contain attributes for a resource                                                                                                         |
| requestsMetaInformation()                                                                              | bool                          | Indicates if the response for this request should contain meta information for a resource                                                                                                   |
| requestsRelationships()                                                                                | bool                          | Indicates if the response for this request should contain relationships for a resource                                                                                                      |
| requestsField(string $type, string $name)                                                              | bool                          | Indicates if the response should contain the given field for the given type.                                                                                                                |
| requestsInclude(string $relationship)                                                                  | bool                          | Indicates if a response should include the given relationship.                                                                                                                              |
| field(string $type, string $name)                                                                      | self                          | Define a field as requested.                                                                                                                                                                |
| filter()                                                                                               | KeyValueCollectionInterface   | Retrieve all filter items. Can be changed.                                                                                                                                                  |
| include(string $relationship)                                                                          | self                          | Define a relationship as included.                                                                                                                                                          |
| sorting()                                                                                              | KeyValueCollectionInterface   | Retrieve a collection of sorting options. The sort field is the key and the value contains either RequestInterface::ORDER_ASC or RequestInterface::ORDER_DESC. Can be changed.              |
| pagination()                                                                                           | KeyValueCollectionInterface   | Retrieve all pagination options. Can be changed.                                                                                                                                            |
| createSubRequest(string $relationship, ?ResourceInterface $resource = null, bool $keepFilters = false) | RequestInterface              | Creates a request for the given relationship. If called twice, the call will return the already created sub request. A sub request does not contain pagination and sorting from its parent. |
| document()                                                                                             | DocumentInterface|null        | Retrieve the request document if available.                                                                                                                                                 |

`Dogado\JsonApi\Model\Response\ResponseInterface`:

| Method     | Return Type                 | Description       |
|------------|-----------------------------|-------------------|
| status()   | integer                     | Http status       |
| headers()  | KeyValueCollectionInterface | Http headers      |
| document() | DocumentInterface|null      | Response document |

*****

[prev: Errors and exceptions](../docs/06-errors.md) | [back to README](../README.md) | [next: Model conversion](../docs/08-models.md)
