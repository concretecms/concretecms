<?php
namespace Concrete\Core\Permission\Registry\Entry\Access\Entity;

use Concrete\Core\Permission\Access\Entity\Entity as AccessEntity;

class Entity implements EntityInterface
{

    protected $entity;

    public function __construct(AccessEntity $entity)
    {
        $this->entity = $entity;
    }

    public function getAccessEntity()
    {
        return $this->entity;
    }

}
