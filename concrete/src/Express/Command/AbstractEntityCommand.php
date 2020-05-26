<?php
namespace Concrete\Core\Express\Command;

use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Foundation\Command\CommandInterface;

abstract class AbstractEntityCommand implements CommandInterface
{


    /**
     * @var Entity
     */
    protected $entity;

    /**
     * AbstractEntityCommand constructor.
     * @param Entity $entity
     */
    public function __construct(Entity $entity)
    {
        $this->entity = $entity;
    }

    /**
     * @return Entity
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param Entity $entity
     */
    public function setEntity(Entity $entity)
    {
        $this->entity = $entity;
    }





}
