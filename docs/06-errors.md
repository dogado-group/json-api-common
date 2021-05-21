[back to README](../README.md)

# Exceptions and Errors

The `JsonApiException` automatically creates an error collection by default, if no existing errors have been passed to the constructor, and converts the current Exception context to the first error (`Dogado\JsonApi\Model\Error\ErrorInterface`).

The error collection can be retrieved and appended by calling the `errors()` method which returns `Dogado\JsonApi\Model\Error\ErrorCollectionInterface`.

These exceptions are available to be handled including the correct http status code:

|  Exception                                                       | Description                           |
|------------------------------------------------------------------|---------------------------------------|
| `Dogado\JsonApi\Exception\JsonApiException`                      | For general server errors             |
| `Dogado\JsonApi\Exception\JsonApi\BadRequestException`           | For client (request) errors           |
| `Dogado\JsonApi\Exception\JsonApi\NotAllowedException`           | For 403 errors                        |
| `Dogado\JsonApi\Exception\JsonApi\ResourceNotFoundException`     | For 404 errors on a concrete resource |
| `Dogado\JsonApi\Exception\JsonApi\UnsupportedMediaTypeException` | For invalid content type header       |
| `Dogado\JsonApi\Exception\JsonApi\UnsupportedTypeException`      | For 404 errors on a resource list     |
| `Dogado\JsonApi\Exception\JsonApi\ValidationException`           | For validation specific 422 errors    |

However, there are some internal exceptions, too:

|  Exception                                                       | Description                                                            |
|------------------------------------------------------------------|------------------------------------------------------------------------|
| `Dogado\JsonApi\Exception\BadResponseException`                  | If a JSON API client received a server response which is no valid json |
| `Dogado\JsonApi\Exception\DataModelSerializerException`          | If the serialization of a data model failed due to different reasons   |
| `Dogado\JsonApi\Exception\DocumentSerializerException`           | If the document serializer produced no valid json                      |

*****

[prev: Documents](../docs/05-documents.md) | [back to README](../README.md) | [next: Request and response](../docs/07-requests.md)
