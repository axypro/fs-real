# axy\fs\real

File system access (PHP).

Real implementation of [axy/fs/ifs](https://github.com/axypro/fs-ifs).

[![Latest Stable Version](https://img.shields.io/packagist/v/axy/fs-real.svg?style=flat-square)](https://packagist.org/packages/axy/fs-real)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%205.4-8892BF.svg?style=flat-square)](https://php.net/)
[![Build Status](https://img.shields.io/travis/axypro/fs-real/master.svg?style=flat-square)](https://travis-ci.org/axypro/fs-real)
[![Coverage Status](https://coveralls.io/repos/axypro/fs-real/badge.svg?branch=master&service=github)](https://coveralls.io/github/axypro/fs-real?branch=master)
[![License](https://poser.pugx.org/axy/fs-real/license)](LICENSE)

* The library does not require any dependencies.
* Tested on PHP 5.4+, PHP 7, HHVM (on Linux), PHP 5.5 (on Windows).
* Install: `composer require axy/fs-real`.
* License: [MIT](LICENSE).

## Documentation

It is an implementation of interfaces from [axy/fs-ifs](https://github.com/axypro/fs-ifs).
This implementation works with the real file system.

The class `FS` is implementation of `axy\fs\ifs\IFS`.

`FS::open()` returns an instance of `RealFile` class that is implementation of `axy\fs\ifs\IFile`.

Messages of the `FSError` exception are similar build-in functions WARNINGs.

## Bugs

The library successfully tested on PHP 5.4, 5.5, 5.5 and 7.0 via travis-ci.org.

For HHVM there are several little bugs:

* `copy()` does not throw exception if the source does not exist.
* `$file->getMetaData()` for closed `$file` does not throw an exception.
* If create a file using `createTempFile()` and gets meta data using `$file->getMetaData()` then the file `meta->filename` does not exists.

