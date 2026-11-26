Cdn77 Coding Standard
=====================

[![Build Status](https://github.com/cdn77/coding-standard/actions/workflows/ci.yml/badge.svg?branch=7.0.x)](https://github.com/cdn77/coding-standard/actions)
[![Latest Stable Version](https://poser.pugx.org/cdn77/coding-standard/v/stable)](https://packagist.org/packages/cdn77/coding-standard)
[![Total Downloads](https://poser.pugx.org/cdn77/coding-standard/d/total.svg)](https://packagist.org/packages/cdn77/coding-standard)

Based on Doctrine Coding Standard https://github.com/doctrine/coding-standard


## How to use it

* Require this project as composer dev dependency:

```
composer require --dev cdn77/coding-standard
```
* Reference this coding standard in your `phpcs.xml.dist` (_check out [the one used in this project](phpcs.xml.dist)_):

```
<rule ref="Cdn77" />
```
