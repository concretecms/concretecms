<?php

namespace Concrete\Core\System\Mutex;

use Concrete\Core\Application\Application;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Foundation\Environment\FunctionInspector;
use RuntimeException;

class SemaphoreMutex implements MutexInterface
{
    use MutexTrait;

    /**
     * @var array
     */
    protected $semaphores = [];

    /**
     * @param Repository $config
     */
    public function __construct(Repository $config)
    {
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\System\Mutex\MutexInterface::isSupported()
     */
    public static function isSupported(Application $app)
    {
        $result = false;
        if (PHP_VERSION_ID >= 50601) { // we need the $nowait parameter of sem_acquire, available since PHP 5.6.1
            $fi = $app->make(FunctionInspector::class);
            $result = $fi->functionAvailable('sem_get') && $fi->functionAvailable('sem_acquire') && $fi->functionAvailable('sem_release') & $fi->functionAvailable('ftok');
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\System\Mutex\MutexInterface::acquire()
     */
    public function acquire($key)
    {
        $key = (string) $key;
        if (isset($this->semaphores[$key]) && is_resource($this->semaphores[$key])) {
            $sem = $this->semaphores[$key];
        } else {
            $semKey = $this->keyToInt($key);
            $sem = sem_get($semKey, 1);
        }
        if (@sem_acquire($sem, true) !== true) {
            throw new MutexBusyException($key);
        }
        $this->semaphores[$key] = $sem;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\System\Mutex\MutexInterface::release()
     */
    public function release($key)
    {
        $key = (string) $key;
        if (isset($this->semaphores[$key])) {
            $sem = $this->semaphores[$key];
            unset($this->semaphores[$key]);
            if (is_resource($sem)) {
                @sem_release($sem);
            }
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
     * @throws InvalidMutexKeyException
     * @throws RuntimeException
     *
     * @return int
     */
    protected function keyToInt($key)
    {
        // for ftok, only the low-order 8-bits of id are significant.
        // The behavior of ftok() is unspecified if these bits are 0.
        // This means that the second parameter of ftok can be a single-bite character ranging from chr(1) to chr(255)
        $index = $this->getMutexKeyIndex($key) + 1;
        $existingApplicationFiles = [
            DIR_BASE . '/index.php',
        ];
        for (; ;) {
            $existingApplicationFile = array_shift($existingApplicationFiles);
            if ($existingApplicationFile === null) {
                throw new RuntimeException('Mutex index is too big');
            }
            if ($index <= 255) {
                break;
            }
            $index -= 255;
        }
        $result = @ftok($existingApplicationFile, chr($index));
        if (!is_int($result) || $result === -1) {
            throw new RuntimeException("ftok() failed for path {$existingApplicationFile} and index {$index}");
        }

        return $result;
    }
}
