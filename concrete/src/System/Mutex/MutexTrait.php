<?php

namespace Concrete\Core\System\Mutex;

trait MutexTrait
{
    /**
     * @var \Concrete\Core\Config\Repository\Repository
     */
    protected $config;

    /**
     * Get the index of the mutex as defined in the app.mutex configuration key.
     *
     * @param string|mixed $key The mutex key to look for
     *
     * @throws InvalidMutexKeyException Throws an InvalidMutexKeyException if $key is not listed in the app.mutex configuration key.
     *
     * @return int
     */
    protected function getMutexKeyIndex($key)
    {
        $configuredMutex = $this->config->get('app.mutex');
        if (is_array($configuredMutex)) {
            $index = array_search((string) $key, $configuredMutex, true);
        } else {
            $index = false;
        }
        if (!is_int($index)) {
            throw new InvalidMutexKeyException($key);
        }

        return $index;
    }
}
