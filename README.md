# laminas-servicemanager-inspector

[![Build Status](https://travis-ci.com/laminas/laminas-servicemanager-inspector.svg?branch=master)](https://travis-ci.com/laminas/laminas-servicemanager-inspector})
[![Coverage Status](https://coveralls.io/repos/github/laminas/laminas-servicemanager-inspector/badge.svg?branch=master)](https://coveralls.io/github/laminas/laminas-servicemanager-inspector?branch=master)

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
$ composer require --dev laminas/servicemanager-inspector
```

## Usage

```bash
./vendor/bin/laminas servicemanager:inspect
```

## Support

* [Issues](https://github.com/laminas/laminas-servicemanager-inspector/issues/)
* [Forum](https://discourse.laminas.dev/)
