<?php
/**
 * @package axy\fs\real
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */

namespace axy\fs\real\errors;

use axy\errors\Runtime;

/**
 * The base error of the library
 */
class FSError extends Runtime implements Error, \axy\fs\ifs\errors\FSError
{
    /**
     * @param string $eMessage [optional]
     * @param int $code [optional]
     * @param string $filename [optional]
     * @param \Exception $p [optional]
     * @param mixed $t [optional]
     */
    public function __construct($eMessage = null, $code = null, $filename = null, \Exception $p = null, $t = null)
    {
        $this->targetFileName = $filename;
        parent::__construct($eMessage, $code, $p, $t);
    }

    /**
     * {@inheritdoc}
     */
    public function getTargetFileName()
    {
        return $this->targetFileName;
    }

    /**
     * @var string
     */
    private $targetFileName;
}
