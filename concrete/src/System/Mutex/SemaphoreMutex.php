<?php

namespace Concrete\Core\System\Mutex;

use Concrete\Core\Application\Application;
use Concrete\Core\Foundation\Environment\FunctionInspector;

class SemaphoreMutex implements MutexInterface
{
    /**
     * @var array
     */
    protected $semaphores = [];

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\System\Mutex\MutexInterface::isSupported()
     */
    public static function isSupported(Application $app)
    {
        $fi = $app->make(FunctionInspector::class);

        return $fi->functionAvailable('sem_get') && $fi->functionAvailable('sem_acquire') && $fi->functionAvailable('sem_release');
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
                sem_release($sem);
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
     * @return int
     */
    protected function keyToInt($key)
    {
        // djb2
        $key = (string) $key;
        $hash = 5381;
        $len = strlen($key);
        for ($i = 0; $i < $len; ++$i) {
            $hash = (($hash << 5) + $hash + ord($key[$i])) & 0x7FFFFFFF;
        }

        return $hash;
    }
}
