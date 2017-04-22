<?php
/**
 * @package axy\fs\real
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */

namespace axy\fs\real\tests;

use axy\fs\real\FS;
use axy\fs\real\errors\FSError;

/**
 * coversDefaultClass axy\fs\real\FS
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class FSTest extends \PHPUnit_Framework_TestCase
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
        var_dump(umask());
        $this->fs = new FS();
        $this->tmp = __DIR__.'/'.self::TMP_DIR;
        clearstatcache(true);
        Helpers::clearDir($this->tmp);
    }

    /**
     * covers ::open
     */
    public function testOpen()
    {
        $fn = $this->tmp.'/open';
        $file = $this->fs->open($fn, "wt");
        $this->assertInstanceOf('axy\fs\ifs\IFile', $file);
        $file->write('content');
        $file->close();
        $file2 = $this->fs->open($fn, "rt");
        $this->assertSame('content', $file2->read(100));
        $file2->close();
        unlink($fn);
        $this->setExpectedException('axy\fs\ifs\errors\FSError');
        $this->fs->open($fn, "rt");
    }

    public function testOpenFileName()
    {
        $fn = $this->tmp.'/open';
        $file = $this->fs->open($fn, "wt");
        $this->setExpectedException('axy\fs\ifs\errors\FSError');
        try {
            $file->close();
            $file->read(10);
        } catch (FSError $e) {
            $this->assertSame($fn, $e->getTargetFileName());
            throw $e;
        }
    }

    /**
     * covers ::isExists
     */
    public function testIsExists()
    {
        $this->assertTrue($this->fs->isExists(__FILE__));
        $this->assertTrue($this->fs->isExists(__DIR__));
        $this->assertFalse($this->fs->isExists($this->tmp.'/unknown.txt'));
    }

    /**
     * covers ::isFile
     */
    public function testIsFile()
    {
        $this->assertTrue($this->fs->isFile(__FILE__));
        $this->assertFalse($this->fs->isFile(__DIR__));
        $this->assertFalse($this->fs->isFile(__DIR__.'/unknown.txt'));
    }

    /**
     * covers ::isDir
     */
    public function testIsDir()
    {
        $this->assertFalse($this->fs->isDir(__FILE__));
        $this->assertTrue($this->fs->isDir(__DIR__));
        $this->assertFalse($this->fs->isDir(__DIR__.'/unknown.txt'));
    }

    /**
     * covers ::isLink
     */
    public function testIsLink()
    {
        $ln = $this->tmp.'/link';
        $lnDir = $this->tmp.'/dir';
        symlink(__FILE__, $ln);
        symlink($this->tmp, $lnDir);
        $this->assertFalse($this->fs->isLink(__FILE__));
        $this->assertFalse($this->fs->isLink(__DIR__));
        $this->assertFalse($this->fs->isLink(__DIR__.'/unknown.txt'));
        $this->assertTrue($this->fs->isLink($ln));
        $this->assertTrue($this->fs->isExists($ln));
        $this->assertTrue($this->fs->isFile($ln));
        $this->assertFalse($this->fs->isDir($ln));
        $this->assertTrue($this->fs->isLink($lnDir));
        $this->assertTrue($this->fs->isExists($lnDir));
        $this->assertFalse($this->fs->isFile($lnDir));
        $this->assertTrue($this->fs->isDir($lnDir));
        unlink($ln);
        unlink($lnDir);
    }

    /**
     * covers ::isExecutable
     */
    public function testIsExecutable()
    {
        $fn = $this->tmp.'/able';
        touch($fn);
        chmod($fn, 0777);
        $this->assertTrue($this->fs->isExecutable($fn));
        chmod($fn, 0666);
        $this->assertFalse($this->fs->isExecutable($fn));
        chmod($fn, 0444);
        $this->assertFalse($this->fs->isExecutable($fn));
        chmod($fn, 0333);
        $this->assertTrue($this->fs->isExecutable($fn));
        unlink($fn);
        $this->assertFalse($this->fs->isExecutable($fn));
    }

    /**
     * covers ::isWritable
     */
    public function testIsWritable()
    {
        $fn = $this->tmp.'/able';
        touch($fn);
        chmod($fn, 0777);
        $this->assertTrue($this->fs->isWritable($fn));
        chmod($fn, 0666);
        $this->assertTrue($this->fs->isWritable($fn));
        chmod($fn, 0444);
        $this->assertFalse($this->fs->isWritable($fn));
        chmod($fn, 0333);
        $this->assertTrue($this->fs->isWritable($fn));
        unlink($fn);
        $this->assertFalse($this->fs->isWritable($fn));
    }

    /**
     * covers ::isReadable
     */
    public function testIsReadable()
    {
        $fn = $this->tmp.'/able';
        touch($fn);
        chmod($fn, 0777);
        $this->assertTrue($this->fs->isReadable($fn));
        chmod($fn, 0666);
        $this->assertTrue($this->fs->isReadable($fn));
        chmod($fn, 0444);
        $this->assertTrue($this->fs->isReadable($fn));
        chmod($fn, 0333);
        $this->assertFalse($this->fs->isReadable($fn));
        unlink($fn);
        $this->assertFalse($this->fs->isReadable($fn));
    }

    /**
     * covers ::write
     */
    public function testWrite()
    {
        $fn = $this->tmp.'/write';
        $this->assertSame(3, $this->fs->write($fn, 'one'));
        $this->assertFileExists($fn);
        $this->assertSame('one', file_get_contents($fn));
        $this->assertSame(3, $this->fs->write($fn, 'two'));
        $this->assertFileExists($fn);
        $this->assertSame('two', file_get_contents($fn));
        $this->assertSame(6, $this->fs->write($fn, ' three', FS::FILE_APPEND));
        $this->assertFileExists($fn);
        $this->assertSame('two three', file_get_contents($fn));
        chmod($fn, 0555);
        $this->setExpectedException('axy\fs\ifs\errors\FSError');
        try {
            $this->fs->write($fn, ' four');
            unlink($fn);
        } catch (FSError $e) {
            unlink($fn);
            $this->assertSame($fn, $e->getTargetFileName());
            throw $e;
        }
    }

    /**
     * covers ::read
     */
    public function testRead()
    {
        $fn = $this->tmp.'/read.txt';
        file_put_contents($fn, 'content');
        $this->assertSame('content', $this->fs->read($fn));
        $this->assertSame('on', $this->fs->read($fn, 1, 2));
        $this->assertSame('nt', $this->fs->read($fn, 5, 20));
        unlink($fn);
        $this->setExpectedException('axy\fs\ifs\errors\FSError');
        $this->fs->read($fn);
    }

    /**
     * covers ::getFileLines
     */
    public function testGetFileLines()
    {
        $fn = $this->tmp.'/lines.txt';
        $lines = [
            'one',
            'two',
            '',
            'three',
            '',
            '',
        ];
        file_put_contents($fn, implode(PHP_EOL, $lines));
        $expected1 = [
            'one'.PHP_EOL,
            'two'.PHP_EOL,
            ''.PHP_EOL,
            'three'.PHP_EOL,
            ''.PHP_EOL,
        ];
        $this->assertSame($expected1, $this->fs->getFileLines($fn));
        $expected2 = [
            'one',
            'two',
            '',
            'three',
            '',
        ];
        $this->assertSame($expected2, $this->fs->getFileLines($fn, FS::FILE_IGNORE_NEW_LINES));
        $expected3 = [
            'one',
            'two',
            'three',
        ];
        $actual3 = $this->fs->getFileLines($fn, FS::FILE_SKIP_EMPTY_LINES | FS::FILE_IGNORE_NEW_LINES);
        $this->assertSame($expected3, $actual3);
        unlink($fn);
        $this->setExpectedException('axy\fs\ifs\errors\FSError');
        $this->fs->getFileLines($fn);
    }

    /**
     * covers ::copy
     */
    public function testCopy()
    {
        $fnSrc = $this->tmp.'/src';
        $fnDest = $this->tmp.'/dest';
        file_put_contents($fnSrc, 'test content');
        $this->fs->copy($fnSrc, $fnDest);
        $this->assertFileExists($fnSrc);
        $this->assertFileExists($fnDest);
        $this->assertSame('test content', file_get_contents($fnSrc));
        $this->assertSame('test content', file_get_contents($fnDest));
        unlink($fnSrc);
        unlink($fnDest);
        if (Helpers::isHHVM()) {
            $this->markTestSkipped('HHVM');
        } else {
            $this->setExpectedException('axy\fs\ifs\errors\FSError');
            $this->fs->copy($fnSrc, $fnDest);
        }
    }

    /**
     * covers ::copy
     */
    public function testRemove()
    {
        $fnSrc = $this->tmp.'/src';
        $fnDest = $this->tmp.'/dest';
        file_put_contents($fnSrc, 'test content');
        $this->fs->rename($fnSrc, $fnDest);
        $this->assertFileNotExists($fnSrc);
        $this->assertFileExists($fnDest);
        $this->assertSame('test content', file_get_contents($fnDest));
        unlink($fnDest);
        $this->setExpectedException('axy\fs\ifs\errors\FSError');
        $this->fs->rename($fnSrc, $fnDest);
    }

    /**
     * covers ::unlink
     */
    public function testUnlink()
    {
        $fn = $this->tmp.'/src';
        touch($fn);
        $this->fs->unlink($fn);
        $this->assertFileNotExists($fn);
        $this->setExpectedException('axy\fs\ifs\errors\FSError');
        $this->fs->unlink($fn);
    }

    /**
     * covers ::makeDir
     */
    public function testMakeDir()
    {
        $dirA = $this->tmp.'/mkdir';
        $dirB = $dirA.'/nested';
        $this->fs->makeDir($dirA);
        $this->assertTrue(is_dir($dirA));
        $this->assertSame(070, fileperms($dirA) & 070);
        rmdir($dirA);
        $this->fs->makeDir($dirB, 0730, true);
        $this->assertTrue(is_dir($dirA));
        $this->assertTrue(is_dir($dirB));
        $this->assertSame(030, fileperms($dirA) & 030);
        $this->assertSame(030, fileperms($dirB) & 030);
        rmdir($dirB);
        rmdir($dirA);
        $this->setExpectedException('axy\fs\ifs\errors\FSError');
        $this->fs->makeDir($dirB);
    }

    /**
     * covers ::removeDir
     */
    public function testRemoveDir()
    {
        $dir = $this->tmp.'/dir';
        mkdir($dir);
        $this->fs->removeDir($dir);
        $this->assertFalse(is_dir($dir));
        $this->setExpectedException('axy\fs\ifs\errors\FSError');
        $this->fs->removeDir($dir);
    }

    /**
     * covers ::changeMode
     */
    public function testChangeMode()
    {
        $fn = $this->tmp.'/mode';
        touch($fn);
        $this->fs->changeMode($fn, 0760);
        clearstatcache();
        $this->assertSame(0760, fileperms($fn) & 0777);
        unlink($fn);
        $this->setExpectedException('axy\fs\ifs\errors\FSError');
        $this->fs->changeMode($fn, 0777);
    }

    /**
     * covers ::changeOwner
     */
    public function testChangeOwner()
    {
        $fn = $this->tmp.'/mode';
        $this->setExpectedException('axy\fs\ifs\errors\FSError');
        $this->fs->changeOwner($fn, 0777);
    }

    /**
     * covers ::changeGroup
     */
    public function testChangeGroup()
    {
        $fn = $this->tmp.'/mode';
        $this->setExpectedException('axy\fs\ifs\errors\FSError');
        $this->fs->changeGroup($fn, 0777);
    }

    /**
     * covers ::getMode
     */
    public function testGetMode()
    {
        $fn = $this->tmp.'/mode';
        touch($fn);
        $this->assertSame(fileperms($fn), $this->fs->getMode($fn));
        chmod($fn, 0630);
        clearstatcache();
        $this->assertSame(fileperms($fn), $this->fs->getMode($fn));
        $this->assertSame(030, $this->fs->getMode($fn, 070));
        unlink($fn);
        $this->setExpectedException('axy\fs\ifs\errors\FSError');
        $this->fs->getMode($fn);
    }

    /**
     * covers ::getOwner
     */
    public function testGetOwner()
    {
        $fn = $this->tmp.'/mode';
        touch($fn);
        $this->assertSame(fileowner($fn), $this->fs->getOwner($fn));
        unlink($fn);
        $this->setExpectedException('axy\fs\ifs\errors\FSError');
        $this->fs->getOwner($fn);
    }

    /**
     * covers ::getGroup
     */
    public function testGetGroup()
    {
        $fn = $this->tmp.'/mode';
        touch($fn);
        $this->assertSame(filegroup($fn), $this->fs->getGroup($fn));
        unlink($fn);
        $this->setExpectedException('axy\fs\ifs\errors\FSError');
        $this->fs->getGroup($fn);
    }

    /**
     * covers ::getType
     */
    public function testGetType()
    {
        $link = $this->tmp.'/link';
        symlink(__FILE__, $link);
        $this->assertSame('file', $this->fs->getType(__FILE__));
        $this->assertSame('dir', $this->fs->getType(__DIR__));
        $this->assertSame('link', $this->fs->getType($link));
        unlink($link);
        $this->setExpectedException('axy\fs\ifs\errors\FSError');
        $this->fs->getType($link);
    }

    /**
     * covers ::getSize
     */
    public function testGetSize()
    {
        $this->assertSame(filesize(__FILE__), $this->fs->getSize(__FILE__));
        $this->setExpectedException('axy\fs\ifs\errors\FSError');
        $this->fs->getSize($this->tmp.'/not-found');
    }

    /**
     * covers ::getModificationTime
     */
    public function testGetModificationTime()
    {
        $this->assertSame(filemtime(__FILE__), $this->fs->getModificationTime(__FILE__));
        $this->setExpectedException('axy\fs\ifs\errors\FSError');
        $this->fs->getModificationTime($this->tmp.'/not-found');
    }

    /**
     * covers ::getAccessTime
     */
    public function testGetAccessTime()
    {
        $this->assertSame(fileatime(__FILE__), $this->fs->getAccessTime(__FILE__));
        $this->setExpectedException('axy\fs\ifs\errors\FSError');
        $this->fs->getAccessTime($this->tmp.'/not-found');
    }

    /**
     * covers ::getInodeChangeTime
     */
    public function testGetInodeChangeTime()
    {
        $this->assertSame(filectime(__FILE__), $this->fs->getInodeChangeTime(__FILE__));
        $this->setExpectedException('axy\fs\ifs\errors\FSError');
        $this->fs->getInodeChangeTime($this->tmp.'/not-found');
    }

    /**
     * covers ::touch
     */
    public function testTouch()
    {
        $fn = $this->tmp.'/touch';
        $this->fs->touch($fn);
        $this->assertFileExists($fn);
        $this->fs->touch($fn, 500000);
        $this->assertFileExists($fn);
        $this->assertSame(500000, filemtime($fn));
        unlink($fn);
        $dir = $this->tmp.'/dir';
        mkdir($dir, 400);
        $fnT = $dir.'/touch';
        $this->setExpectedException('axy\fs\ifs\errors\FSError');
        try {
            $this->fs->touch($fnT);
            unlink($fnT);
            rmdir($dir);
        } catch (FSError $e) {
            $this->assertSame($fnT, $e->getTargetFileName());
            rmdir($dir);
            throw $e;
        }
    }

    /**
     * covers ::getUMask
     */
    public function testGetUMask()
    {
        $this->assertSame(umask(), $this->fs->getUMask());
    }

    /**
     * covers ::getStat
     */
    public function testGetStat()
    {
        $stat = $this->fs->getStat(__FILE__);
        $this->assertInstanceOf('axy\fs\ifs\Stat', $stat);
        $expected = stat(__FILE__);
        $this->assertSame($expected['ino'], $stat->ino);
        $this->setExpectedException('axy\fs\ifs\errors\FSError');
        $this->fs->getStat($this->tmp.'/not-found');
    }

    /**
     * covers ::getRealPath
     */
    public function testGetRealPath()
    {
        $fn = $this->tmp.'/rp';
        touch($fn);
        $fnE = $this->tmp.'/../'.self::TMP_DIR.'/rp';
        $this->assertSame($fn, $this->fs->getRealPath($fnE));
        unlink($fn);
        $this->assertFalse($this->fs->getRealPath($fnE));
    }

    /**
     * covers ::createHardLink
     */
    public function testCreateHardLink()
    {
        $target = $this->tmp.'/target';
        $link = $this->tmp.'/link';
        file_put_contents($target, 'content');
        $this->fs->createHardLink($target, $link);
        $this->assertFileExists($target);
        $this->assertFileExists($link);
        $this->assertSame('file', filetype($link));
        $this->assertFileEquals($target, $link);
        file_put_contents($link, 'new');
        $this->assertSame('new', file_get_contents($target));
        $statT = stat($target);
        $statL = stat($link);
        $this->assertEquals($statT['ino'], $statL['ino']);
        unlink($target);
        $this->assertFileExists($link);
        $this->assertSame('new', file_get_contents($link));
        unlink($link);
        $this->setExpectedException('axy\fs\ifs\errors\FSError');
        $this->fs->createHardLink($target, $link);
    }

    /**
     * covers ::createSymbolicLink
     */
    public function testCreateSymbolicLink()
    {
        $target = $this->tmp.'/target';
        $link = $this->tmp.'/link';
        file_put_contents($target, 'content');
        $this->fs->createSymbolicLink($target, $link);
        $this->assertFileExists($target);
        $this->assertFileExists($link);
        $this->assertSame('link', filetype($link));
        $this->assertFileEquals($target, $link);
        file_put_contents($link, 'new');
        $this->assertSame('new', file_get_contents($target));
        $statT = stat($target);
        $statL = stat($link);
        $this->assertEquals($statT['ino'], $statL['ino']);
        unlink($target);
        $this->assertFileNotExists($link);
        $this->fs->unlink($link);
    }

    /**
     * covers ::getLinkTarget
     */
    public function testGetLinkTarget()
    {
        $target = $this->tmp.'/target';
        $link = $this->tmp.'/link';
        file_put_contents($target, 'content');
        symlink($target, $link);
        $this->assertSame($target, $this->fs->getLinkTarget($link));
        $this->setExpectedException('axy\fs\ifs\errors\FSError');
        $this->fs->getLinkTarget($target);
    }

    /**
     * covers ::changeLinkOwner
     */
    public function testChangeLinkOwner()
    {
        $this->setExpectedException('axy\fs\ifs\errors\FSError');
        $this->fs->changeLinkOwner($this->tmp.'/no-link', 'undefined-owner');
    }

    /**
     * covers ::changeLinkGroup
     */
    public function testChangeLinkGroup()
    {
        $this->setExpectedException('axy\fs\ifs\errors\FSError');
        $this->fs->changeLinkGroup($this->tmp.'/no-link', 'undefined-group');
    }

    /**
     * covers ::getLinkStat
     */
    public function testGetLinkStat()
    {
        $target = $this->tmp.'/target';
        $link = $this->tmp.'/link';
        file_put_contents($target, 'content');
        symlink($target, $link);
        $statT = $this->fs->getStat($target);
        $statL = $this->fs->getStat($link);
        $statLL = $this->fs->getLinkStat($link);
        $this->assertInstanceOf('axy\fs\ifs\Stat', $statLL);
        $this->assertSame($statT->ino, $statL->ino);
        $this->assertNotSame($statT->ino, $statLL->ino);
        $stat = lstat($link);
        $this->assertSame($stat['ino'], $statLL->ino);
        $statLT = $this->fs->getLinkStat($target);
        $this->assertSame($statT->ino, $statLT->ino);
        unlink($link);
        $this->setExpectedException('axy\fs\ifs\errors\FSError');
        $this->fs->getLinkStat($link);
    }

    /**
     * covers ::getFreeSpace
     */
    public function testGetFreeSpace()
    {
        $this->setExpectedException('axy\fs\ifs\errors\FSError');
        $this->fs->getFreeSpace($this->tmp.'/dir');
    }

    /**
     * covers ::getTotalSpace
     */
    public function testGetTotalSpace()
    {
        $this->setExpectedException('axy\fs\ifs\errors\FSError');
        $this->fs->getTotalSpace($this->tmp.'/dir');
    }

    /**
     * covers ::createTmpFile
     */
    public function testCreateTmpFile()
    {
        $file = $this->fs->createTempFile();
        $this->assertInstanceOf('axy\fs\ifs\IFile', $file);
        $file->write('content');
        $file->setPosition(1);
        $this->assertSame('ont', $file->read(3));
        $meta = $file->getMetaData();
        if (Helpers::isHHVM()) {
            $file->close();
            $this->markTestSkipped('HHVM');
        } else {
            $this->assertFileExists($meta->filename);
            $file->close();
            $this->assertFileNotExists($meta->filename);
        }
    }

    /**
     * covers ::changeCurrentDirectory
     */
    public function testChangeCurrentDirectory()
    {
        $current = getcwd();
        $dir = $this->tmp.'/dir';
        mkdir($dir);
        $this->fs->changeCurrentDirectory($dir);
        $this->assertSame($dir, getcwd());
        $this->fs->changeCurrentDirectory($current);
        $this->assertSame($current, getcwd());
        rmdir($dir);
        $this->setExpectedException('axy\fs\ifs\errors\FSError');
        $this->fs->changeCurrentDirectory($dir);
    }

    /**
     * covers ::getCurrentDirectory
     */
    public function testGetCurrentDirectory()
    {
        $dir = getcwd();
        $this->assertSame($dir, $this->fs->getCurrentDirectory());
        chdir($this->tmp);
        $this->assertSame($this->tmp, $this->fs->getCurrentDirectory());
        chdir($dir);
        $this->assertSame($dir, $this->fs->getCurrentDirectory());
    }

    /**
     * covers ::glob
     */
    public function testGlob()
    {
        touch($this->tmp.'/a.txt');
        touch($this->tmp.'/b');
        mkdir($this->tmp.'/dir');
        $actual = $this->fs->glob($this->tmp.'/*');
        $this->assertInternalType('array', $actual);
        sort($actual);
        $expected = [
            $this->tmp.'/a.txt',
            $this->tmp.'/b',
            $this->tmp.'/dir',
        ];
        $this->assertSame($actual, $expected);
        $actual = $this->fs->glob($this->tmp.'/*.txt');
        $this->assertInternalType('array', $actual);
        sort($actual);
        $expected = [
            $this->tmp.'/a.txt',
        ];
        $this->assertSame($actual, $expected);
        $actual = $this->fs->glob($this->tmp.'/*', GLOB_ONLYDIR);
        $this->assertInternalType('array', $actual);
        sort($actual);
        $expected = [
            $this->tmp.'/dir',
        ];
        $this->assertSame($actual, $expected);
    }

    /**
     * @var \axy\fs\real\fs;
     */
    private $fs;

    /**
     * @var string
     */
    private $tmp;
}
