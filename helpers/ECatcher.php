<?php
/**
 * @package axy\fs\real
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */

namespace axy\fs\real\helpers;

use axy\fs\real\errors\FSError;

/**
 * Catches warnings and throws exceptions
 */
class ECatcher
{
    /**
     * Begins errors catch
     *
     * @param string $filename
     */
    public static function start($filename = null)
    {
        if (self::$process) {
            self::stop();
        }
        self::$filename = $filename;
        self::$process = true;
        set_error_handler([__CLASS__, 'handler']);
    }

    /**
     * Ends errors catch
     */
    public static function stop()
    {
        if (self::$process) {
            restore_error_handler();
            self::$process = false;
        }
    }

    /**
     * Error handler
     *
     * @param int $code
     * @param string $message
     * @throws \axy\fs\real\errors\FSError
     */
    public static function handler($code, $message)
    {
        self::stop();
        throw new FSError($message, $code, self::$filename);
    }

    /**
     * @var string
     */
    private static $filename;

    /**
     * @var bool
     */
    private static $process = false;
}
