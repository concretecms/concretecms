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
                    $errorDescription = '';
                    set_error_handler(function ($errno, $errstr) use (&$errorDescription) {
                        $errorDescription = (string) $errstr;
                    });
                    $sem = sem_get($semKey, 1);
                    restore_error_handler();
                    if ($sem === false) {
                        if (preg_match('/^sem_get\(\): failed for key 0x[A-Fa-f0-9]+: No space left on device$/', $errorDescription)) {
                            // In case we couldn't create the semaphore because the system reached the maximum number of semaphores,
                            // the system semget() function called by the sem_get() PHP function returns the ENOSPC error code.
                            // Its default description is
                            // "No space left on device"
                            // But it's really misleading.
                            // @see https://github.com/php/php-src/blob/php-7.3.1/ext/sysvsem/sysvsem.c#L216-L220
                            // @see http://man7.org/linux/man-pages/man2/semget.2.html#ERRORS
                            // @see https://www.gnu.org/software/libc/manual/html_node/Error-Codes.html#index-ENOSPC
                            throw new RuntimeException("The system ran out of semaphores.\nYou have to free some semaphores, or disable semaphore-based mutex.");
                        }
                        throw new RuntimeException("sem_get() failed for path {$filename}: {$errorDescription}");
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
