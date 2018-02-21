<?php

namespace Concrete\Core\System\Mutex;

use Concrete\Core\Application\Application;

class FileLockMutex implements MutexInterface
{
    /**
     * @var string
     */
    protected $temporaryDirectory;

    /**
     * @var array
     */
    protected $resources = [];

    public function __construct($temporaryDirectory)
    {
        $this->temporaryDirectory = rtrim(str_replace(DIRECTORY_SEPARATOR, '/', $temporaryDirectory), '/');
    }

    public function __destruct()
    {
        foreach (array_keys($this->resources) as $key) {
            $this->release($key);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\System\Mutex\MutexInterface::isSupported()
     */
    public static function isSupported(Application $app)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\System\Mutex\MutexInterface::acquire()
     */
    public function acquire($key)
    {
        $success = false;
        $key = (string) $key;
        if (isset($this->resources[$key])) {
            $fd = $this->resources[$key];
            if (is_resource($fd)) {
                throw new MutexBusyException($key);
            }
        }
        $filename = $this->keyToFilename($key);
        @touch($filename);
        @chmod($filename, 0666);
        $fd = @fopen($filename, 'r+');
        if (!is_resource($fd) || @flock($fd, LOCK_EX | LOCK_NB) !== true) {
            throw new MutexBusyException($key);
        }
        $this->resources[$key] = $fd;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\System\Mutex\MutexInterface::release()
     */
    public function release($key)
    {
        if (isset($this->resources[$key])) {
            $fd = $this->resources[$key];
            unset($this->resources[$key]);
            @flock($fd, LOCK_UN);
            @fclose($fd);
            @unlink($this->keyToFilename($key));
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\System\Mutex\MutexInterface::execute()
     */
    public function execute($key, callable $callback)
    {
        $this->acquire($key);
        try {
            $callback();
        } finally {
            $this->release($key);
        }
    }

    /**
     * @param string $key
     *
     * @return string
     */
    protected function keyToFilename($key)
    {
        return $this->temporaryDirectory . '/' . md5((string) $key) . '.lock';
    }
}
