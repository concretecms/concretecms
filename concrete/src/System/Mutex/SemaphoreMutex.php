<?php

namespace Concrete\Core\System\Mutex;

use Concrete\Core\Application\Application;
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
     * Initialize the instance.
     *
     * @param string $temporaryDirectory the path to the temporary directory
     */
    public function __construct($temporaryDirectory)
    {
        $this->setTemporaryDirectory($temporaryDirectory);
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
        $filename = $this->getFilenameForMutexKey($key);
        for ($cycle = 1; ; ++$cycle) {
            $retry = false;
            // Let's cycle a few times to avoid concurrency problems
            try {
                if (isset($this->semaphores[$key]) && is_resource($this->semaphores[$key]) && is_file($filename)) {
                    $statBefore = @stat($filename);
                    $sem = $this->semaphores[$key];
                } else {
                    @touch($filename);
                    @chmod($filename, 0666);
                    $statBefore = @stat($filename);
                    $semKey = @ftok($filename, 'a');
                    if (!is_int($semKey) || $semKey === -1) {
                        $retry = true; // file may have been deleted in the meanwhile
                        throw new RuntimeException("ftok() failed for path {$filename}");
                    }
                    $sem = sem_get($semKey, 1);
                    if ($sem === false) {
                        throw new RuntimeException("sem_get() failed for path {$filename}");
                    }
                }
                if (@sem_acquire($sem, true) !== true) {
                    throw new MutexBusyException($key);
                }
                $statAfter = @stat($filename);
                if (!$statBefore || !$statAfter || $statBefore['ino'] !== $statBefore['ino']) {
                    @sem_release($sem);
                    $retry = true; // file may have been deleted and re-created in the meanwhile
                    throw new RuntimeException("sem_get() failed for path {$filename}");
                }
            } catch (RuntimeException $x) {
                if ($retry === true && $cycle < 5) {
                    continue;
                }
                throw $x;
            }
            break;
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
            $filename = $this->getFilenameForMutexKey($key);
            if (is_file($filename)) {
                @unlink($filename);
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
}
