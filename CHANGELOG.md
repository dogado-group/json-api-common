# Changelog

## v3.0.1 - 2022-06-28

- Fix a bug where asterisk characters were not supported in request url's

## v3.0.0 - 2021-10-01

- Request: Add ability to add custom query parameters to requests
- KeyValueCollection: Add ability to pull (get and remove) items by key

## v2.1.0 - 2021-07-29

- Add support for self initialized nullable value objects
- Model converter: Empty value objects are now converted to empty hashes
- Resource converter: Empty hashes now initialize value objects that support the `\Dogado\JsonApi\Support\Model\ValueObjectFactoryInterface`
- Model converter: Fix a bug where uninitialized value objects are treated like valid instances, although they should be `null`

## v2.0.0 - 2021-07-11

- Dropped `doctrine/annotations` support. All JSON API model declarations must be php 8 attributes.
- The legacy annotation classes under `Dogado\JsonApi\Annotations` have been removed.
- The `value` properties for the `Attribute` and `Type` attributes have been renamed to `name`.
- The `DataModelAnalyser::process` method only accepts objects and strings per data type. Previously, the type check has been done manually and caused a `InvalidArgumentException`.

## v1.2.0 - 2021-07-09

- introduce dedicated php 8 attribute classes to replace annotations in the next major release
- trigger deprecation errors when using annotations
- add support for the `mixed` data type within the `ResourceConverter`

## v1.1.1 - 2021-07-04

- remove `illuminate/support` as dependency

## v1.1.0 - 2021-06-24

- add php 8 attribute support for JSON:API model conversion
- drop php 7.4 support

## v1.0.0 - 2021-05-21

- initial release
