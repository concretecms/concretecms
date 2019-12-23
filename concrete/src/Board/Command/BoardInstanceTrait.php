<?php

namespace Concrete\Core\Board\Command;

use Concrete\Core\Entity\Board\Instance;

trait BoardInstanceTrait
{

    /**
     * @var Instance
     */
    protected $instance;

    /**
     * @param Instance $instance
     */
    public function setInstance(Instance $instance): void
    {
        $this->instance = $instance;
    }

    /**
     * @return Instance
     */
    public function getInstance(): Instance
    {
        return $this->instance;
    }
    
    


}
