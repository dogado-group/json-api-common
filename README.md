# Common interfaces and classes which represent the JSON:API protocol

[![phpunit](https://github.com/dogado-group/json-api-common/actions/workflows/phpunit.yml/badge.svg)](https://github.com/dogado-group/json-api-common/actions/workflows/phpunit.yml)
[![Coverage Status](https://coveralls.io/repos/github/dogado-group/json-api-common/badge.svg?branch=main)](https://coveralls.io/github/dogado-group/json-api-common?branch=main)
[![Total Downloads](https://poser.pugx.org/dogado/json-api-common/downloads)](https://packagist.org/packages/dogado/json-api-common)
[![Latest Stable Version](https://poser.pugx.org/dogado/json-api-common/v/stable)](https://packagist.org/packages/dogado/json-api-common)
[![Latest Unstable Version](https://poser.pugx.org/dogado/json-api-common/v/unstable.png)](https://packagist.org/packages/dogado/json-api-common)
[![License](https://poser.pugx.org/dogado/json-api-common/license)](https://packagist.org/packages/dogado/json-api-common)

This library contains php classes and interfaces shared between
[`dogado/json-api-server`](https://github.com/dogado-group/json-api-server) and 
[`dogado/json-api-client`](https://github.com/dogado-group/json-api-client).

## Installation

```sh
composer require dogado/json-api-common
```

## Documentation

1. [JsonApiTrait](docs/01-trait.md)
1. [Resources](docs/02-resources.md)
    1. [Relationships](docs/02-resources.md#relationships)
1. [Attributes and Meta-Informations](docs/03-collections.md)
1. [Links](docs/04-links.md)
1. [Documents](docs/05-documents.md)
1. [Exceptions and Errors](docs/06-errors.md)
1. [Request and Response](docs/07-requests.md)
1. [Model conversion](docs/08-models.md)

## Credits

- [Chris Döhring](https://github.com/chris-doehring)
- [Philipp Marien](https://github.com/pmarien)
- [eosnewmedia team](https://github.com/eosnewmedia)

This package contains code taken from [enm/json-api-common](https://github.com/eosnewmedia/JSON-API-Common).

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
