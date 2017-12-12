# CDN77 Coding Standard

[![Build Status](https://secure.travis-ci.org/cdn77/coding-standard.png)](https://travis-ci.org/cdn77/coding-standard)

PHP 7.1+ coding standard based on PSR-2, enhanced by [advanced sniffs from Slevomat](https://github.com/slevomat/coding-standard) and also some custom sniffs.


## Sniffs

|      Sniff      |      Description      |
| --------------- | --------------------- |
Cdn77CodingStandard.Sniffs.Classes.ClassesClassStructure | Ensures that the class/interface/trait has consistent order of its members in exact order.
Cdn77CodingStandard.Sniffs.Classes.ClassUsesSeparately | Forbids group `use`.
Cdn77CodingStandard.Sniffs.Classes.InlinePropertyVarTypeHint | Enforces one-line `@var` annotation when no other information is present.
Cdn77CodingStandard.Sniffs.Namespaces.DisallowUseOfGlobalTypes | Forbids `use` of global types in favor of direct references.
Cdn77CodingStandard.Sniffs.TypeHints.NullTypeSpecifiedLast | Requires `null` to be specified as last type in annotations, i.e. `string|null` instead of `null|string`.
Cdn77CodingStandard.Sniffs.WhiteSpace.MethodSpacing | Requires exactly N lines between methods, as configured.
Cdn77CodingStandard.Sniffs.WhiteSpace.OperatorSpacing | Requires space around operators, but excludes declare.


## How to use it

* Require this project as dev dependency:

```
composer req --dev cdn77/coding-standard
```
* Reference this coding standard in your `phpcs.xml`:

```
<rule ref="Cdn77CodingStandard" />
```
