<?php

namespace Concrete\Core\Express\Command;

use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Foundation\Command\Command;

abstract class AbstractEntityCommand extends Command
{
    /**
     * @var \Concrete\Core\Entity\Express\Entity
     */
    protected $entity;

    /**
     * AbstractEntityCommand constructor.
     *
     * @param Entity $entity
     */
    public function __construct(Entity $entity)
    {
        $this->entity = $entity;
    }

    public function getEntity(): Entity
    {
        return $this->entity;
    }

    /**
     * @return $this
     */
    public function setEntity(Entity $entity): object
    {
        $this->entity = $entity;

        return $this;
    }
}
