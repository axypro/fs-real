<?php
/**
 * @package axy\fs\real
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */

namespace axy\fs\real;

use axy\fs\ifs\IFile;
use axy\fs\ifs\Stat;
use axy\fs\ifs\MetaData;
use axy\fs\real\helpers\ECatcher;
use axy\errors\NotValid;

class RealFile implements IFile
{
    /**
     * The constructor
     *
     * @param resource $handle
     * @param string $filename [optional]
     * @throws \axy\errors\NotValid
     */
    public function __construct($handle, $filename = null)
    {
        if (!is_resource($handle)) {
            throw new NotValid('File handle', 'require stream resource, '.gettype($handle).' given');
        }
        $type = get_resource_type($handle);
        if ($type !== 'stream') {
            throw new NotValid('File handle', 'require stream resource, '.$type.' resource given');
        }
        $this->filename = $filename;
        $this->handle = $handle;
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        ECatcher::start($this->filename);
        fclose($this->handle);
        ECatcher::stop();
    }

    /**
     * {@inheritdoc}
     */
    public function isEOF()
    {
        ECatcher::start($this->filename);
        $result = feof($this->handle);
        ECatcher::stop();
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        ECatcher::start($this->filename);
        fflush($this->handle);
        ECatcher::stop();
    }

    /**
     * {@inheritdoc}
     */
    public function write($data, $length = null)
    {
        ECatcher::start($this->filename);
        $result = $length ? fwrite($this->handle, $data, $length) : fwrite($this->handle, $data);
        ECatcher::stop();
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function read($length)
    {
        ECatcher::start($this->filename);
        $result = fread($this->handle, $length);
        ECatcher::stop();
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getChar()
    {
        ECatcher::start($this->filename);
        $result = fgetc($this->handle);
        ECatcher::stop();
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getLine($length = null)
    {
        ECatcher::start($this->filename);
        $result = $length ? fgets($this->handle, $length) : fgets($this->handle);
        ECatcher::stop();
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function lock($operation, &$wouldBlock = null)
    {
        ECatcher::start($this->filename);
        $result = flock($this->handle, $operation, $wouldBlock);
        ECatcher::stop();
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getStat()
    {
        ECatcher::start($this->filename);
        $stat = fstat($this->handle);
        ECatcher::stop();
        return new Stat($stat);
    }

    /**
     * {@inheritdoc}
     */
    public function setPosition($offset, $whence = self::SEEK_SET)
    {
        ECatcher::start($this->filename);
        fseek($this->handle, $offset, $whence);
        ECatcher::stop();
    }

    /**
     * {@inheritdoc}
     */
    public function getPosition()
    {
        ECatcher::start($this->filename);
        $result = ftell($this->handle);
        ECatcher::stop();
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function rewindPosition()
    {
        ECatcher::start($this->filename);
        rewind($this->handle);
        ECatcher::stop();
    }

    /**
     * {@inheritdoc}
     */
    public function truncate($size = 0)
    {
        ECatcher::start($this->filename);
        ftruncate($this->handle, $size);
        ECatcher::stop();
    }
    
    /**
     * {@inheritdoc}
     */
    public function getMetaData()
    {
        ECatcher::start($this->filename);
        $result = stream_get_meta_data($this->handle);
        ECatcher::stop();
        return new MetaData($result);
    }

    /**
     * @var resource
     */
    private $handle;

    /**
     * @var string
     */
    private $filename;
}
