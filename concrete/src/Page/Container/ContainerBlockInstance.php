<?php

namespace Concrete\Core\Page\Container;

use Concrete\Core\Block\Block;
use Concrete\Core\Entity\Page\Container\Instance;

class ContainerBlockInstance
{

    /**
     * @var Block 
     */
    protected $block;

    /**
     * @var Instance 
     */
    protected $instance;
    
    public function __construct(Block $block, Instance $instance)
    {
        $this->instance = $instance;
        $this->block = $block;
    }

    /**
     * @return Block
     */
    public function getBlock(): Block
    {
        return $this->block;
    }

    /**
     * @return Instance
     */
    public function getInstance(): Instance
    {
        return $this->instance;
    }
    
    
}
