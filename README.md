# laminas-servicemanager-inspector

[![Build Status](https://github.com/laminas/laminas-servicemanager-inspector/actions/workflows/continuous-integration.yml/badge.svg)](https://github.com/laminas/laminas-servicemanager-inspector/actions/workflows/continuous-integration.yml)
[![Psalm coverage](https://shepherd.dev/github/laminas/laminas-servicemanager-inspector/coverage.svg?)](https://shepherd.dev/github/laminas/laminas-servicemanager-inspector)

The purpose of this package is to make autowiring reliable (no `AoT` is involved).
At the moment it makes sure that `ReflectionBasedAbstractFactory` won't cause any runtime problems.

The tool can be added to your favorite `CI` so to make sure there are no defects.

[TSC Proposal](https://github.com/laminas/technical-steering-committee/issues/55)

## Defects analyzed

* Circular dependency
* Cyclic alias 
* Missing factory 
* Factory autoload failure
* Unresolvable service 
* Unresolvable scalar
* Scalar type/name mismatch (WIP)
* Other scenarios of misconfiguration

`Laminas/DI/AutowireFactory` support will be coming soon.

## Installation

Run the following to install this library:

```bash
$ composer require --dev laminas/laminas-servicemanager-inspector
```

## Configuration

No configuration is needed. All you need to do is to include `ConfigProvider` (or `Module`) to your application.

## Usage

```bash
./vendor/bin/laminas servicemanager:inspect
```

## Future plans

- Analyze `AutowireFactory` (from `laminas-di`)
- Check if each and every app's root entrypoint (e.g. a class implementing `RequestHandlerInterface`) has a proper factory

## Support

* [Issues](https://github.com/laminas/laminas-servicemanager-inspector/issues/)
* [Forum](https://discourse.laminas.dev/)
