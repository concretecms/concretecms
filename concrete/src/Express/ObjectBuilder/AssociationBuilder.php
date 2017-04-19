<?php
namespace Concrete\Core\Express\ObjectBuilder;

use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Express\ObjectAssociationBuilder;
use Concrete\Core\Express\ObjectBuilder;

class AssociationBuilder
{

    protected $objectBuilder;
    protected $associationBuilder;
    protected $entitiesToSave = [];

    public function __construct(ObjectAssociationBuilder $associationBuilder, ObjectBuilder $objectBuilder)
    {
        $this->objectBuilder = $objectBuilder;
        $this->associationBuilder = $associationBuilder;
    }

    protected function addEntityToSave($entity)
    {
        if (!in_array($entity, $this->entitiesToSave)) {
            $this->entitiesToSave[] = $entity;
        }
    }

    public function refresh(Entity $entity)
    {
        return $this->objectBuilder->getEntityManager()->refresh($entity);
    }

    public function __call($method, $arguments)
    {

        if (isset($arguments[0]) && $arguments[0] instanceof ObjectBuilder) {
            $arguments[0] = $arguments[0]->getEntity();
        }

        $this->addEntityToSave($arguments[0]);
        $this->addEntityToSave($this->objectBuilder->getEntity());

        array_unshift($arguments, $this->objectBuilder->getEntity());

        if (is_callable([$this->associationBuilder, $method])) {
            call_user_func_array([$this->associationBuilder, $method], $arguments);
        }
        return $this;
    }

    public function save()
    {
        $em = $this->objectBuilder->getEntityManager();
        foreach($this->entitiesToSave as $entity) {
            $em->persist($entity);
        }
        $em->flush();
        return $this->objectBuilder->getEntity();
    }

}
