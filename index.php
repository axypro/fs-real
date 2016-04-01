<?php
/**
 * File system access
 *
 * @package axy\fs\real
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 * @license https://raw.github.com/axypro/fs-real/master/LICENSE MIT
 * @link https://github.com/axypro/fs-real repository
 * @link https://packagist.org/packages/axy/fs-real composer package
 * @uses PHP5.4+
 */

namespace axy\fs\real;

if (!is_file(__DIR__.'/vendor/autoload.php')) {
    throw new \LogicException('Please: composer install');
}

require_once(__DIR__.'/vendor/autoload.php');
