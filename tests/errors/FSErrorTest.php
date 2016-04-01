<?php
/**
 * @package axy\fs\real
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */

namespace axy\fs\real\tests\errors;

use axy\fs\real\errors\FSError;

/**
 * coversDefaultClass axy\fs\real\FSError
 */
class FSErrorTest extends \PHPUnit_Framework_TestCase
{
    public function testError()
    {
        $previous = new \LogicException('msg');
        $e = new FSError('I/O error', 10, 'x.txt', $previous);
        $this->assertInstanceOf('RuntimeException', $e);
        $this->assertInstanceOf('axy\fs\ifs\errors\FSError', $e);
        $this->assertSame($previous, $e->getPrevious());
        $this->assertSame('I/O error', $e->getMessage());
        $this->assertSame('x.txt', $e->getTargetFileName());
    }
}
