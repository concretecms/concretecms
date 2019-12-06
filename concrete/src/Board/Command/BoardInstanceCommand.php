<?php

namespace Concrete\Core\Board\Command;

use Concrete\Core\Entity\Board\Instance;
use Concrete\Core\Foundation\Command\CommandInterface;

abstract class BoardInstanceCommand implements CommandInterface
{

    /**
     * @var Instance
     */
    protected $instance;

    /**
     * BoardInstanceCommand constructor.
     * @param Instance $instance
     */
    public function __construct(Instance $instance)
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
