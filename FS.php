<?php
/**
 * @package axy\fs\real
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */

namespace axy\fs\real;

use axy\fs\ifs\IFS;
use axy\fs\ifs\Stat;
use axy\fs\real\helpers\ECatcher;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class FS implements IFS
{
    /**
     * {@inheritdoc}
     */
    public function open($filename, $mode)
    {
        ECatcher::start($filename);
        $handle = fopen($filename, $mode);
        ECatcher::stop();
        return new RealFile($handle, $filename);
    }

    /**
     * {@inheritdoc}
     */
    public function isExists($filename)
    {
        return file_exists($filename);
    }

    /**
     * {@inheritdoc}
     */
    public function isFile($filename)
    {
        return is_file($filename);
    }

    /**
     * {@inheritdoc}
     */
    public function isDir($filename)
    {
        return is_dir($filename);
    }

    /**
     * {@inheritdoc}
     */
    public function isLink($filename)
    {
        return is_link($filename);
    }

    /**
     * {@inheritdoc}
     */
    public function isExecutable($filename)
    {
        return is_executable($filename);
    }

    /**
     * {@inheritdoc}
     */
    public function isWritable($filename)
    {
        return is_writable($filename);
    }

    /**
     * {@inheritdoc}
     */
    public function isReadable($filename)
    {
        return is_readable($filename);
    }

    /**
     * {@inheritdoc}
     */
    public function write($filename, $data, $flags = 0)
    {
        ECatcher::start($filename);
        $result = file_put_contents($filename, $data, $flags);
        ECatcher::stop();
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function read($filename, $offset = null, $length = null)
    {
        if ($length !== null) {
            $args = [$filename, false, null, $offset, $length];
        } elseif ($offset !== null) {
            $args = [$filename, false, null, $offset];
        } else {
            $args = [$filename];
        }
        ECatcher::start($filename);
        $result = call_user_func_array('file_get_contents', $args);
        ECatcher::stop();
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getFileLines($filename, $flags = 0)
    {
        ECatcher::start($filename);
        $result = file($filename, $flags);
        ECatcher::stop();
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function copy($source, $destination)
    {
        ECatcher::start($source);
        copy($source, $destination);
        ECatcher::stop();
    }

    /**
     * {@inheritdoc}
     */
    public function rename($source, $destination)
    {
        ECatcher::start($source);
        rename($source, $destination);
        ECatcher::stop();
    }

    /**
     * {@inheritdoc}
     */
    public function unlink($filename)
    {
        ECatcher::start($filename);
        unlink($filename);
        ECatcher::stop();
    }

    /**
     * {@inheritdoc}
     */
    public function makeDir($dirname, $mode = 0777, $recursive = false)
    {
        ECatcher::start($dirname);
        mkdir($dirname, $mode, $recursive);
        ECatcher::stop();
    }

    /**
     * {@inheritdoc}
     */
    public function removeDir($dirname)
    {
        ECatcher::start($dirname);
        rmdir($dirname);
        ECatcher::stop();
    }

    /**
     * {@inheritdoc}
     */
    public function changeMode($filename, $mode)
    {
        ECatcher::start($filename);
        chmod($filename, $mode);
        ECatcher::stop();
    }

    /**
     * {@inheritdoc}
     */
    public function changeOwner($filename, $owner)
    {
        ECatcher::start($filename);
        chown($filename, $owner);
        ECatcher::stop();
    }

    /**
     * {@inheritdoc}
     */
    public function changeGroup($filename, $group)
    {
        ECatcher::start($filename);
        chgrp($filename, $group);
        ECatcher::stop();
    }

    /**
     * {@inheritdoc}
     */
    public function getMode($filename, $mask = null)
    {
        ECatcher::start($filename);
        $result = fileperms($filename);
        ECatcher::stop();
        if ($mask !== null) {
            $result &= $mask;
        }
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getOwner($filename)
    {
        ECatcher::start($filename);
        $result = fileowner($filename);
        ECatcher::stop();
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getGroup($filename)
    {
        ECatcher::start($filename);
        $result = filegroup($filename);
        ECatcher::stop();
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getType($filename)
    {
        ECatcher::start($filename);
        $result = filetype($filename);
        ECatcher::stop();
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getSize($filename)
    {
        ECatcher::start($filename);
        $result = filesize($filename);
        ECatcher::stop();
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getModificationTime($filename)
    {
        ECatcher::start($filename);
        $result = filemtime($filename);
        ECatcher::stop();
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getAccessTime($filename)
    {
        ECatcher::start($filename);
        $result = fileatime($filename);
        ECatcher::stop();
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getInodeChangeTime($filename)
    {
        ECatcher::start($filename);
        $result = filectime($filename);
        ECatcher::stop();
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function touch($filename, $time = null, $accessTime = null)
    {
        ECatcher::start($filename);
        touch($filename, $time, $accessTime);
        ECatcher::stop();
    }

    /**
     * {@inheritdoc}
     */
    public function changeUMask($mask)
    {
        umask($mask);
    }

    /**
     * {@inheritdoc}
     */
    public function getUMask()
    {
        return umask();
    }

    /**
     * {@inheritdoc}
     */
    public function getStat($filename)
    {
        ECatcher::start($filename);
        $stat = stat($filename);
        ECatcher::stop();
        return new Stat($stat);
    }

    /**
     * {@inheritdoc}
     */
    public function clearStatCache($clearRealPath = false, $filename = null)
    {
        clearstatcache($clearRealPath, $filename);
    }

    /**
     * {@inheritdoc}
     */
    public function getRealPath($filename)
    {
        return realpath($filename);
    }

    /**
     * {@inheritdoc}
     */
    public function createHardLink($targetName, $linkName)
    {
        ECatcher::start($linkName);
        link($targetName, $linkName);
        ECatcher::stop();
    }

    /**
     * {@inheritdoc}
     */
    public function createSymbolicLink($targetName, $linkName)
    {
        ECatcher::start($linkName);
        symlink($targetName, $linkName);
        ECatcher::stop();
    }

    /**
     * {@inheritdoc}
     */
    public function getLinkTarget($linkName)
    {
        ECatcher::start($linkName);
        $result = readlink($linkName);
        ECatcher::stop();
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function changeLinkOwner($linkName, $owner)
    {
        ECatcher::start($linkName);
        lchown($linkName, $owner);
        ECatcher::stop();
    }

    /**
     * {@inheritdoc}
     */
    public function changeLinkGroup($linkName, $group)
    {
        ECatcher::start($linkName);
        lchgrp($linkName, $group);
        ECatcher::stop();
    }

    /**
     * {@inheritdoc}
     */
    public function getLinkStat($linkName)
    {
        ECatcher::start($linkName);
        $result = lstat($linkName);
        ECatcher::stop();
        return new Stat($result);
    }

    /**
     * {@inheritdoc}
     */
    public function getFreeSpace($dirname)
    {
        ECatcher::start($dirname);
        $result = disk_free_space($dirname);
        ECatcher::stop();
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalSpace($dirname)
    {
        ECatcher::start($dirname);
        $result = disk_total_space($dirname);
        ECatcher::stop();
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function createTempFile()
    {
        ECatcher::start();
        $handle = tmpfile();
        ECatcher::stop();
        return new RealFile($handle);
    }

    /**
     * {@inheritdoc}
     */
    public function glob($pattern, $flags = 0)
    {
        ECatcher::start();
        $result = glob($pattern, $flags);
        ECatcher::stop();
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function changeCurrentDirectory($directory)
    {
        ECatcher::start();
        chdir($directory);
        ECatcher::stop();
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentDirectory()
    {
        return getcwd();
    }
}
