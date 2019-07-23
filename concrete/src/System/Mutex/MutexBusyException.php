<?php

namespace Concrete\Core\System\Mutex;

use RuntimeException;

class MutexBusyException extends RuntimeException
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
        parent::__construct(t(/*i18n: A mutex is a system object that represents a system to run code; usually this word shouldn't be translated */'The mutex with key "%s" is busy.', $this->mutexKey));
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
