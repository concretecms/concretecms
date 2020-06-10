<?php

namespace Concrete\Core\Foundation\Queue\Mutex;

use Bernard\Queue;
use Concrete\Core\System\Mutex\MutexInterface;

abstract class AbstractMutexGenerator implements MutexGeneratorInterface
{

    /**
     * @var MutexKeyGenerator
     */
    protected $keyGenerator;

    /**
     * @var $mutexer
     */
    protected $mutexer;

    public function __construct(MutexKeyGenerator $keyGenerator, MutexInterface $mutexer)
    {
        $this->keyGenerator = $keyGenerator;
        $this->mutexer = $mutexer;
    }


}