[![Latest Stable Version](https://img.shields.io/packagist/v/drupol/phposinfo.svg?style=flat-square)](https://packagist.org/packages/drupol/phposinfo)
 [![GitHub stars](https://img.shields.io/github/stars/drupol/phposinfo.svg?style=flat-square)](https://packagist.org/packages/drupol/phposinfo)
 [![Total Downloads](https://img.shields.io/packagist/dt/drupol/phposinfo.svg?style=flat-square)](https://packagist.org/packages/drupol/phposinfo)
 [![Build Status](https://img.shields.io/travis/drupol/phposinfo/master.svg?style=flat-square)](https://travis-ci.org/drupol/phposinfo)
 [![Build Status](https://img.shields.io/appveyor/ci/drupol/phposinfo.svg?style=flat-square)](https://ci.appveyor.com/project/drupol/phposinfo)
 [![Scrutinizer code quality](https://img.shields.io/scrutinizer/quality/g/drupol/phposinfo/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/drupol/phposinfo/?branch=master)
 [![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/drupol/phposinfo/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/drupol/phposinfo/?branch=master)
 [![Mutation testing badge](https://badge.stryker-mutator.io/github.com/drupol/phposinfo/master)](https://stryker-mutator.github.io)
 [![License](https://img.shields.io/packagist/l/drupol/phposinfo.svg?style=flat-square)](https://packagist.org/packages/drupol/phposinfo)

# PHP OS Info

## Description

Get information of the current operating system where PHP is running on.

Information that you can retrieve are:

* Operating system
* Operating system family

There are many packages that does that already but most of them are based on
the use of the variable `PHP_OS` that contains the operating system name PHP was 
built on, from [php.net](https://www.php.net/manual/en/reserved.constants.php):

     The operating system PHP was built for.

However, `PHP_OS` might be sometimes not very accurate, then using
[php_uname()](https://php.net/php_uname) might be a better fit for detecting the
operating system, we only use it as a fallback.

This library uses [php_uname()](https://php.net/php_uname) and a static list of
existing operating systems, and then from there, tries to deduct the operating
system family.

From PHP 7.2, the variable `PHP_OS_FAMILY` was added and based on the definition
from [php.net](https://www.php.net/manual/en/reserved.constants.php):

     The operating system family PHP was built for.
     Either of 'Windows', 'BSD', 'Darwin', 'Solaris', 'Linux' or 'Unknown'. 

So once again, if you're using a PHP which is cross compiled, using those
constant is a bad idea.

## Requirements

* PHP >= 7

## Installation

```composer require drupol/phposinfo```

## Usage

```php
<?php

declare(strict_types = 1);

include 'vendor/autoload.php';

use drupol\phposinfo\OsInfo;
use drupol\phposinfo\Enum\Family;
use drupol\phposinfo\Enum\Os;

// Get the OS name.
OsInfo::os();

// Get the OS family.
OsInfo::family();

// Check if the OS is Unix based.
OsInfo::isUnix();

// Check if the OS is Apple based.
OsInfo::isApple();

// Check if the OS is Windows based.
OsInfo::isWindows();

// Check the OS version.
OsInfo::version();

// Check the OS release.
OsInfo::release();

// Check if the OS Family is Family::UNIX_ON_WINDOWS.
OsInfo::isFamily(Family::UNIX_ON_WINDOWS);

// Check if the OS is Os::FREEBSD.
OsInfo::isOs(Os::FREEBSD);

// Check if the OS is Windows.
OsInfo::isOsName('windows');

// Check if the OS family is darwin.
OsInfo::isFamilyName('darwin');
```

## Code quality, tests and benchmarks

Every time changes are introduced into the library, [Travis CI](https://travis-ci.org/drupol/phposinfo/builds) run the tests and the benchmarks.

The library has tests written with [PHPSpec](http://www.phpspec.net/).
Feel free to check them out in the `spec` directory. Run `composer phpspec` to trigger the tests.

Before each commit some inspections are executed with [GrumPHP](https://github.com/phpro/grumphp), run `./vendor/bin/grumphp run` to check manually.

[PHPInfection](https://github.com/infection/infection) is used to ensure that your code is properly tested, run `composer infection` to test your code.

## Contributing

Feel free to contribute to this library by sending Github pull requests. I'm quite reactive :-)
