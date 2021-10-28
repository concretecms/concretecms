<?php

namespace Concrete\Core\Page\Container\Command;

use Concrete\Core\Entity\Page\Container;
use Concrete\Core\Foundation\Command\Command;


abstract class ContainerCommand extends Command
{
    /**
     * @var \Concrete\Core\Entity\Page\Container
     */
    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function getContainer(): Container
    {
        return $this->container;
    }
}
