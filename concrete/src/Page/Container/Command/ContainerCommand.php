<?php

namespace Concrete\Core\Page\Container\Command;

use Concrete\Core\Entity\Page\Container;
use Concrete\Core\Foundation\Command\CommandInterface;

abstract class ContainerCommand implements CommandInterface
{

    /**
     * @var Container
     */
    protected $container;

    /**
     * AddContainerCommand constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @return Container
     */
    public function getContainer(): Container
    {
        return $this->container;
    }
    
    

    
}
