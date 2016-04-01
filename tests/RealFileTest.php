<?php
/**
 * @package axy\fs\real
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */

namespace axy\fs\real\tests;

use axy\fs\real\errors\FSError;
use axy\fs\real\RealFile;

/**
 * coversDefaultClass axy\fs\real\RealFile
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class RealFileTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @const string
     */
    const TMP_DIR = 'tmp';

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->tmp = __DIR__.'/'.self::TMP_DIR;
        clearstatcache(true);
        Helpers::clearDir($this->tmp);
    }

    /**
     * @expectedException \axy\errors\NotValid
     */
    public function testInvalidHandle()
    {
        /** @noinspection PhpParamsInspection */
        return new RealFile(3);
    }

    /**
     * covers ::read
     */
    public function testRead()
    {
        $fn = $this->tmp.'/read.txt';
        file_put_contents($fn, 'one two three four five');
        $fp = fopen($fn, "rt");
        $file = new RealFile($fp);
        $this->assertSame('one tw', $file->read(6));
        $this->assertSame('o three', $file->read(7));
        $this->assertSame(' four five', $file->read(50));
        $this->assertSame('', $file->read(50));
        fclose($fp);
    }

    /**
     * covers ::read
     */
    public function testReadError()
    {
        $fn = $this->tmp.'/read.txt';
        file_put_contents($fn, 'one two three four five');
        $fp = fopen($fn, "rt");
        $file = new RealFile($fp);
        fclose($fp);
        $this->setExpectedException('axy\fs\real\errors\FSError');
        $file->read(100);
    }

    /**
     * covers ::getChar
     */
    public function testGetChar()
    {
        $fn = $this->tmp.'/read.txt';
        file_put_contents($fn, 'one two');
        $fp = fopen($fn, "rt");
        $file = new RealFile($fp);
        $this->assertSame('o', $file->getChar());
        $this->assertSame('ne t', $file->read(4));
        $this->assertSame('w', $file->getChar());
        $this->assertSame('o', $file->getChar());
        $this->assertFalse($file->getChar());
        fclose($fp);
        $this->setExpectedException('axy\fs\real\errors\FSError');
        $file->getChar();
    }

    /**
     * covers ::getLine
     */
    public function testGetLine()
    {
        $lines = [
            'first line',
            'second line',
            'third line',
        ];
        $fn = $this->tmp.'/read.txt';
        file_put_contents($fn, implode(PHP_EOL, $lines));
        $fp = fopen($fn, "rt");
        $file = new RealFile($fp);
        $this->assertSame('first line'.PHP_EOL, $file->getLine());
        $this->assertSame('se', $file->getLine(3));
        $this->assertSame('cond line'.PHP_EOL, $file->getLine(33));
        $this->assertSame('thir', $file->read(4));
        $this->assertSame('d line', $file->getLine());
        $this->assertFalse($file->getLine());
        fclose($fp);
        $this->setExpectedException('axy\fs\real\errors\FSError');
        $file->getLine();
    }

    public function testWrite()
    {
        $fn = $this->tmp.'/write.txt';
        $fp = fopen($fn, "wt");
        $file = new RealFile($fp);
        $this->assertSame(6, $file->write('first '));
        $this->assertSame(3, $file->write('second', 3));
        $this->assertSame('first sec', file_get_contents($fn));
        fclose($fp);
        $this->setExpectedException('axy\fs\real\errors\FSError');
        $file->write('after close');
    }

    /**
     * covers ::getStat
     */
    public function testGetStat()
    {
        $fn = __FILE__;
        $expected = stat($fn);
        $fp = fopen($fn, "rt");
        $file = new RealFile($fp);
        $actual = $file->getStat();
        fclose($fp);
        $this->assertInstanceOf('axy\fs\ifs\Stat', $actual);
        $this->assertSame($expected['mode'], $actual->mode);
        $this->assertSame($expected['ino'], $actual->ino);
        $this->setExpectedException('axy\fs\real\errors\FSError');
        $file->getStat();
    }

    /**
     * covers ::setPosition
     * covers ::getPosition
     * covers ::rewindPosition
     */
    public function testPosition()
    {
        $fp = fopen(__FILE__, "rt");
        $file = new RealFile($fp);
        $this->assertSame(0, $file->getPosition());
        $file->read(5);
        $this->assertSame(5, $file->getPosition());
        fseek($fp, 3);
        $this->assertSame(3, $file->getPosition());
        $file->setPosition(4);
        $this->assertSame(4, ftell($fp));
        $this->assertSame(4, $file->getPosition());
        $file->setPosition(5, RealFile::SEEK_CUR);
        $this->assertSame(9, ftell($fp));
        $this->assertSame(9, $file->getPosition());
        $file->rewindPosition();
        $this->assertSame(0, ftell($fp));
        $this->assertSame(0, $file->getPosition());
        fclose($fp);
        $this->setExpectedException('axy\fs\real\errors\FSError');
        $file->setPosition(3);
    }

    /**
     * covers ::truncate
     */
    public function testTruncate()
    {
        $fn = $this->tmp.'/truncate.txt';
        file_put_contents($fn, 'one two three four five');
        $fp = fopen($fn, "r+");
        $file = new RealFile($fp);
        $file->truncate(5);
        $this->assertSame('one t', file_get_contents($fn));
        $file->truncate();
        $this->assertSame('', file_get_contents($fn));
        fclose($fp);
        $this->setExpectedException('axy\fs\real\errors\FSError');
        $file->truncate();
    }

    /**
     * covers ::close
     */
    public function testClose()
    {
        $fp = fopen(__FILE__, "rt");
        $file = new RealFile($fp);
        $file->close();
        $this->setExpectedException('axy\fs\real\errors\FSError');
        $file->read(5);
    }

    /**
     * covers ::isEOF
     */
    public function testIsEOF()
    {
        $fn = $this->tmp.'/read.txt';
        file_put_contents($fn, 'one two three');
        $fp = fopen($fn, "rt");
        $file = new RealFile($fp);
        $this->assertFalse($file->isEOF());
        $file->read(5);
        $this->assertFalse($file->isEOF());
        $file->read(15);
        $this->assertTrue($file->isEOF());
        $this->assertTrue($file->isEOF());
        $file->read(15);
        $this->assertTrue($file->isEOF());
        $file->setPosition(3);
        $this->assertFalse($file->isEOF());
        fclose($fp);
        $this->setExpectedException('axy\fs\real\errors\FSError');
        $file->isEOF();
    }

    /**
     * covers ::getMetaData
     */
    public function testGetMetaData()
    {
        $fn = $this->tmp.'/read.txt';
        file_put_contents($fn, 'one two three');
        $fp = fopen($fn, "rt");
        $file = new RealFile($fp);
        $file->read(2);
        $meta = $file->getMetaData();
        $file->close();
        $this->assertInstanceOf('axy\fs\ifs\MetaData', $meta);
        $this->assertSame($fn, $meta->filename);
        $this->assertSame("rt", $meta->mode);
        $this->assertSame(false, $meta->eof);
        $this->setExpectedException('axy\fs\real\errors\FSError');
        $file->getMetaData();
    }

    public function testLockNot()
    {
        $fn = $this->tmp.'/lock.txt';
        file_put_contents($fn, 'content');
        $first = new RealFile(fopen($fn, "rt"));
        $second = new RealFile(fopen($fn, "at"));
        $second->write(' text');
        $this->assertSame('content text', $first->read(20));
        $first->close();
        $second->close();
    }

    public function testLockRead()
    {
        $fn = $this->tmp.'/lock.txt';
        file_put_contents($fn, 'content');
        $first = new RealFile(fopen($fn, "rt"));
        $this->assertTrue($first->lock(LOCK_SH, $blocked));
        $this->assertEquals(0, $blocked);
        $this->assertSame('con', $first->read(3));
        $second = new RealFile(fopen($fn, "rt"));
        $this->assertFalse($second->lock(LOCK_EX | LOCK_NB, $blocked));
        $this->assertEquals(1, $blocked);
        $this->assertTrue($second->lock(LOCK_SH | LOCK_NB, $blocked));
        $this->assertEquals(0, $blocked);
        $this->assertTrue($first->lock(LOCK_UN));
        $third = new RealFile(fopen($fn, "rt"));
        $this->assertFalse($third->lock(LOCK_EX | LOCK_NB, $blocked));
        $this->assertEquals(1, $blocked);
        $second->lock(LOCK_UN);
        $this->assertTrue($third->lock(LOCK_EX | LOCK_NB, $blocked));
        $this->assertEquals(0, $blocked);
        $third->lock(LOCK_UN);
        $first->close();
        $second->close();
        $third->close();
    }

    public function testLockWrite()
    {
        $fn = $this->tmp.'/lock.txt';
        file_put_contents($fn, 'content');
        $first = new RealFile(fopen($fn, "at"));
        $this->assertTrue($first->lock(LOCK_EX, $blocked));
        $this->assertEquals(0, $blocked);
        $second = new RealFile(fopen($fn, "rt"));
        $this->assertFalse($second->lock(LOCK_EX | LOCK_NB, $blocked));
        $this->assertEquals(1, $blocked);
        $this->assertFalse($second->lock(LOCK_SH | LOCK_NB, $blocked));
        $this->assertEquals(1, $blocked);
        $first->close();
        $second->close();
    }

    public function testFSErrorFileName()
    {
        $fn = __FILE__;
        $fp = fopen($fn, "rt");
        $file = new RealFile($fp, $fn);
        fclose($fp);
        try {
            $file->read(1);
            $this->fail('not thrown');
        } catch (FSError $e) {
            $this->assertSame($fn, $e->getTargetFileName());
        }
    }

    /**
     * @var string
     */
    private $tmp;
}
