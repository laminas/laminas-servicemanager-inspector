# laminas-servicemanager-inspector

[![Build Status](https://travis-ci.com/laminas/laminas-servicemanager.svg?branch=master)](https://travis-ci.com/laminas/laminas-{component})
[![Coverage Status](https://coveralls.io/repos/github/laminas/laminas-servicemanager/badge.svg?branch=master)](https://coveralls.io/github/laminas/laminas-{component}?branch=master)


[WIP]
The overall workflow around `servicemanager` is quite error prone, especially if one doesn't favor the `AoT`.

## Defects analyzed

* Circular dependency
* Cyclic alias 
* Missing factory 
* Factory autoload failure
* Unresolvable service/scalar
* Scalar type/name mismatch
* Many other scenarios of misconfiguration

## Installation

Run the following to install this library:

```bash
$ composer require --dev laminas/laminas-psalm-plugin
```

## Documentation

Browse the documentation online at https://docs.laminas.dev/laminas-servicemanager/

## Support

* [Issues](https://github.com/laminas/laminas-servicemanager/issues/)
* [Forum](https://discourse.laminas.dev/)
