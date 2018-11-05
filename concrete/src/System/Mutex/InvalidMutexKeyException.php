<?php

namespace Concrete\Core\System\Mutex;

use RuntimeException;

/**
 * Exception thrown when a mutex key is not valid.
 */
class InvalidMutexKeyException extends RuntimeException
{
    /**
     * The mutex key.
     *
     * @var mixed
     */
    protected $mutexKey;

    /**
     * Initialize the instance.
     *
     * @param mixed $mutexKey The invalid mutex key
     */
    public function __construct($mutexKey)
    {
        $this->mutexKey = (string) $mutexKey;
        parent::__construct(t(/*i18n: A mutex is a system object that represents a system to run code; usually this word shouldn't be translated */'The mutex with key "%s" is not valid.', $this->mutexKey));
    }

    /**
     * Get the invalid mutex key.
     *
     * @return mixed
     */
    public function getMutexKey()
    {
        return $this->mutexKey;
    }
}
