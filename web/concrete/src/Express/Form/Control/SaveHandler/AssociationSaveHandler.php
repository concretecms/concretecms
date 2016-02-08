<?php
namespace Concrete\Core\Express\Form\Control\SaveHandler;

use Concrete\Core\Entity\Express\Control\Control;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Express\ObjectManager;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;

class AssociationSaveHandler implements SaveHandlerInterface
{
    protected $entityManager;

    public function __construct(EntityManager $manager)
    {
        $this->entityManager = $manager;
    }

    public function saveFromRequest(Control $control, Entry $entry, Request $request)
    {
        exit;
        $entityId = $request->request->get('express_association_' . $control->getId());
        $target = $control->getAssociation()->getTargetEntity();

        $method = camelcase($control->getAssociation()->getComputedTargetPropertyName());
        $method = "set{$method}";
        $entry->$method($targetEntity);
    }
}
