<?php

namespace Concrete\Core\Express\Form\Control\SaveHandler;

use Concrete\Core\Entity\AttributeKey\AttributeKey;
use Concrete\Core\Entity\Express\Control\AssociationControl;
use Concrete\Core\Entity\Express\Control\AttributeKeyControl;
use Concrete\Core\Entity\Express\Control\Control;
use Concrete\Core\Express\BaseEntity;
use Concrete\Core\Express\ObjectManager;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;

class AssociationSaveHandler implements SaveHandlerInterface
{

    protected $entityManager;

    function __construct(EntityManager $manager)
    {
        $this->entityManager = $manager;
    }


    public function saveFromRequest(ObjectManager $manager, Control $control, BaseEntity $entity, Request $request)
    {
        $entityId = $request->request->get('express_association_' . $control->getId());
        $target = $control->getAssociation()->getTargetEntity();
        $className = $manager->getClassName($target);
        $repository = $this->entityManager->getRepository($className);
        $targetEntity = $repository->findOneById($entityId);

        $method = camelcase($control->getAssociation()->getComputedTargetPropertyName());
        $method = "set{$method}";
        $entity->$method($targetEntity);
   }

}