<?php

namespace Concrete\Core\Page\Container\Command;

use Concrete\Core\Entity\Page\Container;
use Concrete\Core\Foundation\Command\CommandInterface;

abstract class ContainerCommand implements CommandInterface
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
