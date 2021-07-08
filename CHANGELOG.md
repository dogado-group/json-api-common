# Changelog

## 2.0.0 - 2021-07-XX

* Dropped `doctrine/annotations` support. All JSON API model declarations must be php 8 attributes.
* The legacy annotation classes under `Dogado\JsonApi\Annotations` have been removed.
* The `value` properties for the `Attribute` and `Type` attributes have been renamed to `name`.
* The `DataModelAnalyser::process` method only accepts objects and strings per data type. Previously, the type check has been done manually and caused a `InvalidArgumentException`.

## v1.2.0 - 2021-07-XX

- introduce dedicated php 8 attribute classes to replace annotations in the next major release
- trigger deprecation errors when using annotations

## v1.1.1 - 2021-07-04

- remove `illuminate/support` as dependency

## v1.1.0 - 2021-06-24

- add php 8 attribute support for JSON:API model conversion
- drop php 7.4 support

## v1.0.0 - 2021-05-21

- initial release
