<?php

namespace Concrete\Core\System\Mutex;

use RuntimeException;

/**
 * Exception thrown when a mutex key is not defined in the app.mutex configuration key.
 */
class InvalidMutexKeyException extends RuntimeException
{
    /**
     * The mutex key.
     *
     * @var string
     */
    protected $mutexKey;

    /**
     * Initialize the instance.
     *
     * @param string $mutexKey The mutex key
     */
    public function __construct($mutexKey)
    {
        $this->mutexKey = (string) $mutexKey;
        parent::__construct(t(/*i18n: A mutex is a system object that represents a system to run code; usually this word shouldn't be translated */'The mutex with key "%1$s" is not defined in the "%2$s" configuration key.', $this->mutexKey, 'app.mutex'));
    }

    /**
     * Get the mutex key.
     *
     * @return string
     */
    public function getMutexKey()
    {
        return $this->mutexKey;
    }
}
