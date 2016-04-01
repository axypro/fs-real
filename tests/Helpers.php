<?php

namespace axy\fs\real\tests;

class Helpers
{
    /**
     * @param string $dir
     */
    public static function clearDir($dir)
    {
        foreach (glob($dir.'/*') as $fn) {
            if (basename($fn) === '.gitkeep') {
                continue;
            }
            if (is_link($fn) || is_file($fn)) {
                unlink($fn);
            } elseif (is_dir($fn)) {
                self::clearDir($fn);
                rmdir($fn);
            }
        }
    }

    /**
     * @return bool
     */
    public static function isHHVM()
    {
        return defined('HHVM_VERSION');
    }
}
